<?php

/*
 *  This model handles user related function with database
 */

namespace App\Models;

use \App\Core\Application;

class UserModel extends BaseModel
{

    public $db;

    public function __construct()
    {
        $this->db = new \App\Core\DAL\MainDatabase;
    }


    /**
     * login
     *
     * @param  string $name
     * @param  string $pw
     * @param  bool $verify_password
     * @return void
     *
     * Login function
     */
    public function login($userName, $password, $verify_password = true): ?object
    {
       
        $user = $this->getUserForLogin($userName);

        //If the username not found
        if (!isset($user)) {
            return null;
        }

        if (password_verify($password ?? "", $user->password ?? "") || !$verify_password) {
            
            $this->updateUserSecretKey($user);
            $this->updateNoOfLoginAttempts($user->id, true);
            
        } else {

            return $this->ifLoginFailed($user);
        }

        return $user;
    }

    public function getUserForLogin($userName)
    {
        $lang_postfix = \App\Core\Application::getInstance()->user->getLangId();
        if(!empty($lang_postfix))
            $lang_postfix = "_" . $lang_postfix;

        $select = $this->newSelect()
            ->cols([
                'u.id',
                'u.name',
                'u.password',
                'u.secret_key',
                'u.email',
                'u.full_name',
                'u.gender_id',
                'l.id as lang_id',
                'l.direction as lang_direction',
                'l.name as lang_name',
                'u.no_of_tries',
                "STUFF((SELECT ',' + cast(id as varchar(50)) from core_FN_GetOicUsers(u.id, DEFAULT) FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as user_oic_of"
            ])
            ->from('users as u ')
            ->join(
                'LEFT',
                'languages as l',
                'l.id = u.lang_id'
            )
            ->where('u.name = :name')
            ->bindValue('name', $userName);

        $user = $this->db->querySelectSingle($select);

        //If the username not found
        if (!isset($user)) {
            return null;
        }

        $userRoles = $this->getUserRoles($user->id);

        $user = (object)array_merge((array)$user, array("roles" => $userRoles));

        return $user;
    }


    private function getUserRoles($userId)
    {
        $select = $this->newSelect()
            ->cols([
                'ur.value_id as role_id',
                'isnull(r.is_admin,0) AS is_admin',
                'isnull(r.is_super_admin,0) as is_super_admin'
            ])
            ->from('users_roles as ur')
            ->join(
                'LEFT',
                'roles as r',
                'r.id = ur.value_id'
            )
            ->where('ur.parent_id in (select id from core_FN_GetOicUsers(:id, DEFAULT))')
            ->bindValue('id', $userId);
        return $this->db->querySelect($select);
    }

    private function ifLoginFailed($user)
    {

        $this->updateNoOfLoginAttempts($user->id);

        //If number of tries is more than the setting then lock the account
        if (($user->no_of_tries + 1) >= $this->getSetting("security_lock_user_after_x_tries")) {
            $this->lockUserAccount($user->id, $user->full_name);
        }

        return null;
    }

    private function updateNoOfLoginAttempts($userId, $reset = false)
    {

        $update = $this->newUpdate()
            ->table('users')
            ->set('no_of_tries', ($reset ? '0' : 'isnull(no_of_tries,0) + 1'))
            ->where('id = :id')
            ->bindValue('id', $userId);
        $this->db->queryUpdate($update);
    }

    private function updateUserSecretKey($user)
    {
        
        $user->secret_key = \App\Helpers\MiscHelper::randomString(25);

        Application::getInstance()->user->setSecretKey($user->secret_key);

        $update = $this->newUpdate()
            ->table('users')
            ->set('secret_key', ':secret_key')
            ->where('id = :id')
            ->bindValue('id', $user->id)
            ->bindValue('secret_key', $user->secret_key);

        $this->db->queryUpdate($update);
    }

    private function lockUserAccount($userId, $userFullName)
    {

        $update = $this->newUpdate()
            ->table('users')
            ->set('is_active', '0')
            ->where('id = :id')
            ->bindValue('id', $userId);
        $this->db->queryUpdate($update);

        //after lock the account, send a notification to admins
        $message = "$userFullName account deactivated after many failed attempts to login";
        Application::getInstance()->pushNotification->add($message, Application::getInstance()->user->getSystemUserId(), null, array('admin'), "users", $userId, "warning", true);
        
        (new CTypeLog("users"))
            ->setContentId($userId)
            ->setUserId(Application::getInstance()->user->getSystemUserId())
            ->setJustification("This account deactivated after many failed attempts to login")
            ->setTitle("Account Locked")
            ->setGroupNam("edit")
            ->save();
    }



    public function changeAccountIsActive($userId, $newValue)
    {

        $update = $this->newUpdate()
            ->table('users')
            ->set('is_active', $newValue ? "1" : "0")
            ->set('no_of_tries', "0")
            ->where('id = :id')
            ->bindValue('id', $userId);
        $this->db->queryUpdate($update);

        (new CTypeLog("users"))
            ->setContentId($userId)
            ->setUserId(Application::getInstance()->user->getSystemUserId())
            ->setJustification(($newValue ? "Unlocked" : "Locked") . " account")
            ->setTitle(($newValue ? "Unlocked" : "Locked") . " account")
            ->setGroupNam("edit")
            ->save();
    }



    /**
     * checkIfUserIsAdmin
     *
     * @param  int $userId
     * @return void
     *
     * This function check if user is admin or no
     */
    public function checkIfUserIsAdmin($userId = null)
    {

        if ($userId == null) {
            $userId = Application::getInstance()->user->getId('user_id');
        }

        $select = $this->newSelect()
            ->cols([
                'count(*) as result'
            ])
            ->from('users_roles as ur')
            ->join(
                'LEFT',
                'roles as r',
                'r.id = ur.value_id'
            )
            ->where('parent_id = :id')
            ->where('(r.is_admin = 1 or r.is_super_admin = 1)')
            ->bindValue('id', $userId);
        $row = $this->db->querySelectSingle($select);

        return $row->result > 0;
    }





    /**
     * checkUserSecretKey
     *
     * @return void
     *
     * This function checks if user has valid session if so then refresh it
     */
    public function checkUserSecretKey()
    {
        
        if (Application::getInstance()->user->isGuest()) {

            $langObjSelect = $this->newSelect()
                ->cols(['name'])
                ->from('languages')
                ->where('name=:name')
                ->bindValue('name', Application::getInstance()->request->getParam("lang"));
            $langObj = $this->db->querySelectSingle($langObjSelect);

            if (isset($langObj)) {
                Application::getInstance()->session->set('user_lang', $langObj->name);
            }

            return;
        }

        $this->updateUserHeartbeat();
        
        $user = $this->getUserForLogin(Application::getInstance()->user->getName());

        //If the result is valid
        if (!isset($user)) {
            Application::getInstance()->user->logout(false);
            return;
        }

        //If user's session is not valid logout
        if (_strtolower($user->secret_key) != _strtolower(Application::getInstance()->user->getSecretKey())) {
            Application::getInstance()->user->logout(false);
        }

        //Set user session
        Application::getInstance()->user->setSession($user);
    }

    private function updateUserHeartbeat()
    {

        $update = $this->newUpdate()
            ->table('users')
            ->set('last_heartbeat', 'getdate()')
            ->where('id = :id')
            ->bindValue('id', Application::getInstance()->user->getId());
        $this->db->queryUpdate($update);
    }





















    /**
     * getResetPasswordSession
     *
     * @param  string $ukey
     * @return void
     *
     * Get pw reset session using ukey
     */
    public function getResetPasswordSession($ukey): ?object
    {

        $select = $this->newSelect()
            ->cols([
                'u.*'
            ])
            ->from('password_reset_requests as r')
            ->join(
                'LEFT',
                'users as u',
                'u.id = r.user_id'
            )
            ->where('r.ukey = :ukey')
            ->where('isnull(r.is_used,0) = 0')
            ->where('datediff(minute,r.created_date,getdate()) < 60') //The key will expire after 60 minutes
            ->limit(1)
            ->bindValue('ukey', $ukey);
        return $this->db->querySelectSingle($select);
    }


    public function markResetPasswordSessionAsUsed($ukey)
    {

        $update = $this->newUpdate()
            ->table('password_reset_requests')
            ->set('is_used', '1')
            ->where('ukey = :ukey')
            ->bindValue('ukey', $ukey);
        $this->db->queryUpdate($update);
    }





    /**
     * updatePassword
     *
     * @param  int $user_id
     * @param  string $pw
     * @param  string $ukey
     * @return void
     *
     * Update user pw
     */
    public function updatePassword($userId, $password, $ukey = null)
    {

        $newSecretKey = \App\Helpers\MiscHelper::randomString(25);

        Application::getInstance()->user->setSecretKey($newSecretKey);

        if (isset($ukey)) {
            $this->markResetPasswordSessionAsUsed($ukey);
        }

        $update = $this->newUpdate()
            ->table('users')
            ->set('password', ':password')
            ->set('no_of_tries', '0')
            ->set('secret_key', ':secret_key')
            ->where('id = :id')
            ->bindValue('password', $password)
            ->bindValue('secret_key', $newSecretKey)
            ->bindValue('id', $userId);
        $this->db->queryUpdate($update);

        $justification = isset($ukey) ? "ukey: " . $ukey : "";
        
        (new CTypeLog("users"))
            ->setContentId($userId)
            ->setJustification($justification)
            ->setTitle("Password Updated")
            ->setGroupNam("edit")
            ->save();
    }





    /**
     * update_user_lang
     *
     * @param  int $lang_id
     * @param  int $user_id
     * @return void
     *
     * Update user language
     */
    public function setLang($langId, $userId = null)
    {

        $update = $this->newUpdate()
            ->table('users')
            ->set('lang_id', ':lang_id')
            ->where('id = :id')
            ->bindValue('lang_id', $langId)
            ->bindValue('id', !empty($userId) ? $userId : \App\Core\Application::getInstance()->user->getId());
        $this->db->queryUpdate($update);
    }









    /**
     * get_user_daily_log
     *
     * @param  int $user_id
     * @return void
     *
     * This function return daily user login
     */
    public function get_user_daily_log($user_id): array
    {
        $query = "
        SET NOCOUNT ON;

        Declare @UserId bigint = :user_id
        Declare @FromDate Date
        SELECT @FromDate = isnull(created_date, (select min(created_date) from ctypes)) from users where id = @UserId
        declare @ToDate Date = getdate()

        ;With
        E1(N) As (Select 1 From (Values (1), (1), (1), (1), (1), (1), (1), (1), (1), (1)) DT(N)),
        E2(N) As (Select 1 From E1 A Cross Join E1 B),
        E4(N) As (Select 1 From E2 A Cross Join E2 B),
        E6(N) As (Select 1 From E4 A Cross Join E2 B),
        Tally(N) As
        (
        Select
        Row_Number() Over (Order By (Select Null))
        From
        E6
        )
        select
        DateAdd(Day, N - 1, @FromDate) date,
        x.c as value
        from Tally
        cross apply (
        select
            count(*) as c
        from users_login_logs l
        where
        convert(date,l.created_date,103) = DateAdd(Day, N - 1, @FromDate) and l.created_user_id = @UserId
        ) x
        where N <= DateDiff(Day, @FromDate, @ToDate) + 1
        order by tally.N desc

        ";

        $this->db->query($query);

        $this->db->bind(':user_id', $user_id);

        return $this->db->resultSet();
    }

}
