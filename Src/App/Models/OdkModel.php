<?php 

/*
 *  This model handles the base functions which all the models will use them
 */

namespace App\Models;

use App\Core\Application;
use App\Helpers\MiscHelper;

class OdkModel {
    
    private $db;
    private $coreModel;

    public function __construct($db_id = 4){
        $this->coreModel = Application::getInstance()->coreModel;
        $this->db = \App\Helpers\DbHelper::getMySQLDbObj($db_id);
    }

    
    public function getAllUsers() {
        $query = "
        SELECT
            u._uri AS id,
            u.LOCAL_USERNAME AS name,
            u.FULL_NAME AS full_name,
                u._CREATOR_URI_USER AS created_user_id,
            u._CREATION_DATE AS created_date,
            u._LAST_UPDATE_URI_USER AS updated_user_id,
            u._LAST_UPDATE_DATE AS last_update_date,
            u.IS_REMOVED as deleted,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'GROUP_SITE_ADMINS') = 0 THEN 0 ELSE 1 END AS is_admin,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'ROLE_SITE_ACCESS_ADMIN') = 0 THEN 0 ELSE 1 END AS can_access_admin,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'GROUP_DATA_COLLECTORS') = 0 THEN 0 ELSE 1 END AS can_collect_data
        FROM _registered_users u
        ORDER BY u.LOCAL_USERNAME

        ";

        $this->db->query($query);
        
        $result = $this->db->resultSet();

        return $result;
    }
    

    public function getUser($username) {

        $username = "uid:{$username}|20";

        $query = "
        SELECT
            u._uri AS id,
            u.LOCAL_USERNAME AS name,
            u.FULL_NAME AS full_name,
                u._CREATOR_URI_USER AS created_user_id,
            u._CREATION_DATE AS created_date,
            u._LAST_UPDATE_URI_USER AS updated_user_id,
            u._LAST_UPDATE_DATE AS last_update_date,
            u.IS_REMOVED as deleted,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'GROUP_SITE_ADMINS') = 0 THEN 0 ELSE 1 END AS is_admin,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'ROLE_SITE_ACCESS_ADMIN') = 0 THEN 0 ELSE 1 END AS can_access_admin,
            case when (SELECT COUNT(*) FROM _user_granted_authority p WHERE p.USER = u._uri AND p.GRANTED_AUTHORITY = 'GROUP_DATA_COLLECTORS') = 0 THEN 0 ELSE 1 END AS can_collect_data
        FROM _registered_users u
        WHERE u._uri like CONCAT(:username, '%')
        ORDER BY u.LOCAL_USERNAME

        ";

        $this->db->query($query);
        $this->db->bind("username", $username);
        $result = $this->db->resultSingle();

        return $result;
    }

    private function getDigestAuthPassword($username, $password, $realmString) {
        return MD5( $username . ":" . $realmString . ":" . $password );
    }

    private function getBasicAuthPassword($password, $salt) {
        return SHA1( $password . "{" . $salt . "}" );
    }

    public function verifyCurrentPassword($username, $password) {

        $realmString = $this->getRealmString();
        $passwordObj = $this->getPassword($username);

        $BASIC_AUTH_PASSWORD = $this->getBasicAuthPassword($password, $passwordObj->BASIC_AUTH_SALT);
        $DIGEST_AUTH_PASSWORD = $this->getDigestAuthPassword($username, $password, $realmString);

        return 
            _strtolower($passwordObj->BASIC_AUTH_PASSWORD) == _strtolower($BASIC_AUTH_PASSWORD) && 
            _strtolower($passwordObj->DIGEST_AUTH_PASSWORD) == _strtolower($DIGEST_AUTH_PASSWORD);
    }

    public function changeUserPassword($username, $password) {

        if(empty($username)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Username is empty");
        }

        if(empty($password)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Password is empty");
        }

        $realmString = $this->getRealmString();

        if(empty($realmString)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Realm String is empty");
        }

        $BASIC_AUTH_SALT = substr(_strtolower(MiscHelper::randomString(6)),0,8);

        $BASIC_AUTH_PASSWORD = $this->getBasicAuthPassword($password, $BASIC_AUTH_SALT);
        $DIGEST_AUTH_PASSWORD = $this->getDigestAuthPassword($username, $password, $realmString);

        $this->updateUserPassword($username, $BASIC_AUTH_SALT, $BASIC_AUTH_PASSWORD, $DIGEST_AUTH_PASSWORD);

    }

    private function updateUserPassword($username, $BASIC_AUTH_SALT, $BASIC_AUTH_PASSWORD, $DIGEST_AUTH_PASSWORD) {
        
        $currentUser = $this->getUser($username);

        if(Application::getInstance()->user->isGuest() != true) {
            $currentUser = $this->getUser(Application::getInstance()->user->getName());
        }
    
        if(!$currentUser || ($currentUser->is_admin != true && $currentUser->name != Application::getInstance()->user->getName())) {
            throw new \App\Exceptions\ForbiddenException();
        }
        
        $query = "UPDATE _registered_users SET _LAST_UPDATE_URI_USER = :update_user, BASIC_AUTH_SALT = :BASIC_AUTH_SALT, BASIC_AUTH_PASSWORD = :BASIC_AUTH_PASSWORD, DIGEST_AUTH_PASSWORD = :DIGEST_AUTH_PASSWORD WHERE Local_username = :username";

        $this->db->query($query);
        $this->db->bind("username", $username);
        $this->db->bind("update_user", $currentUser->id);
        $this->db->bind("BASIC_AUTH_SALT", $BASIC_AUTH_SALT);
        $this->db->bind("BASIC_AUTH_PASSWORD", $BASIC_AUTH_PASSWORD);
        $this->db->bind("DIGEST_AUTH_PASSWORD", $DIGEST_AUTH_PASSWORD);
        $result = $this->db->execute();

    }

    public function getRealmString() {

        $query = "SELECT VALUE as value FROM _server_preferences_properties r WHERE r.key = 'LAST_KNOWN_REALM_STRING' LIMIT 0,1";

        $this->db->query($query);
        $result = $this->db->resultSingle();

        if(isset($result)) {
            return $result->value;
        }
        return null;

    }

    public function getPassword($username) {

        $query = "SELECT BASIC_AUTH_SALT, BASIC_AUTH_PASSWORD, DIGEST_AUTH_PASSWORD FROM _registered_users r WHERE r.LOCAL_USERNAME = :username";

        $this->db->query($query);
        $this->db->bind("username", $username);
        return $this->db->resultSingle();

    }
    


    function createAccount($user_id) {

        
        $imsUser = $this->coreModel->nodeModel("users")->id($user_id)->loadFirst();

        $odkUser = $this->getUser($imsUser->name);

        $currentOdkUser = $this->getUser(Application::getInstance()->user->getName());

        if(!$currentOdkUser || $currentOdkUser->is_admin != true) {
            throw new \App\Exceptions\ForbiddenException();
        }

        if(isset($odkUser)) {
            throw new \App\Exceptions\CriticalException("Odk user {$imsUser->name} is already exist");
        }

        $dateTimeStamp = date('Y-m-d h:i:s', time());
        $uid = "uid:{$imsUser->name}|{$dateTimeStamp}";
        
        $realmString = $this->getRealmString();

        $BASIC_AUTH_SALT = substr(_strtolower(MiscHelper::randomString(6)),0,8);
        $newpwd = MiscHelper::randomStrongPassword();
        
        $BASIC_AUTH_PASSWORD = $this->getBasicAuthPassword($newpwd, $BASIC_AUTH_SALT);
        $DIGEST_AUTH_PASSWORD = $this->getDigestAuthPassword($imsUser->name, $newpwd, $realmString);

        //Insert records to _registered_users
        $query = "
        INSERT INTO _registered_users (
            _URI,
            _CREATOR_URI_USER,
            _CREATION_DATE,
            _LAST_UPDATE_DATE,
            LOCAL_USERNAME,
            FULL_NAME,
            BASIC_AUTH_PASSWORD,
            BASIC_AUTH_SALT,
            DIGEST_AUTH_PASSWORD,
            IS_REMOVED
        ) VALUES (
            :_URI,
            :_CREATOR_URI_USER,
            NOW(),
            NOW(),
            :FULL_NAME,
            :LOCAL_USERNAME,
            :BASIC_AUTH_PASSWORD,
            :BASIC_AUTH_SALT,
            :DIGEST_AUTH_PASSWORD,
            0
        )
        ";

        $this->db->query($query);
        $this->db->bind("_URI", $uid);
        $this->db->bind("_CREATOR_URI_USER", $currentOdkUser->id);
        $this->db->bind("LOCAL_USERNAME", $imsUser->full_name);
        $this->db->bind("FULL_NAME", $imsUser->name);
        $this->db->bind("BASIC_AUTH_PASSWORD", $BASIC_AUTH_PASSWORD);
        $this->db->bind("BASIC_AUTH_SALT", $BASIC_AUTH_SALT);
        $this->db->bind("DIGEST_AUTH_PASSWORD", $DIGEST_AUTH_PASSWORD);
        $this->db->execute();


        //Delete existing records in _user_granted_authority
        $query = "DELETE FROM _user_granted_authority WHERE USER = :user_id";
        $this->db->query($query);
        $this->db->bind("user_id", $uid);
        $this->db->execute();
        


        $p_uri = MiscHelper::randomString(10);

        //Insert new records to _user_granted_authority
        $query = "
        INSERT INTO _user_granted_authority (
            _URI,
            _CREATOR_URI_USER,
            _CREATION_DATE,
            _LAST_UPDATE_DATE,
            USER,
            GRANTED_AUTHORITY
        ) VALUES (
            :_URI,
            :_CREATOR_URI_USER,
            NOW(),
            NOW(),
            :USER,
            :GRANTED_AUTHORITY
        )
        ";

        $this->db->query($query);
        $this->db->bind("_URI", $p_uri);
        $this->db->bind("_CREATOR_URI_USER", $currentOdkUser->id);
        $this->db->bind("USER", $uid);
        $this->db->bind("GRANTED_AUTHORITY", "GROUP_DATA_COLLECTORS");
        $this->db->execute();

        return $newpwd;
    }
}



