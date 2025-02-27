<?php

namespace Ext\Models;

use App\Core\Application;
use App\Core\DAL\MainDatabase;
use App\Models\CoreModel;
use App\Models\CTypeLog;
use Exception;
use IlegalUserActionException;

class ExtModel extends CoreModel
{

    public $db;

    public function __construct()
    {
        $this->db = new MainDatabase;
    }


    public function get_edf_business_info($username, $password)
    {

        $query = "
                declare @username nvarchar(250) = :username
                select 
                    eoi.id as business_id,
                    eoi.code,
                    eoi.person_applying as full_name ,
                    eoi.contact_number as phone_1,
                    eoi.business_name,
                    CASE eoi.status_id WHEN 2 THEN 'Approved' WHEN 84 THEN 'Pending' else 'Rejected' END as eoi_status,
                    CASE when ver.status_id is null then 'N/A' WHEN ver.status_id = 2 THEN 'Approved' WHEN ver.status_id = 84 THEN 'Pending' else 'Rejected' END as ver_status,
                    'N/A' as app_status, -- CASE app.status_id WHEN 2 THEN 'Approved' WHEN 84 THEN 'Pending' else  'Rejected' END as app_status,
                    'N/A' as ab_status, -- CASE WHEN ab.status_id in (2,6) THEN 'Approved' WHEN ab.status_id = 84 THEN 'Waitting' else  'Rejected' END as ab_status,
                    'N/A' as icv_status -- CASE WHEN icv.status_id in (3,39) THEN 'Rejected' WHEN icv.status_id = 2 THEN 'Approved' else  'Pending' END as icv_status 
                from edf_eoi as eoi
                left join edf_eoi_verification as ver on ver.business_id = eoi.id
                --left join edf_application as app on app.eoi_id = eoi.id
                --left join edf_approved_business as ab on ab.eoi_id = eoi.id
                --left join edf_investment_committee as icv on icv.eoi_id = eoi.id 
                where eoi.code = @username and eoi.password = :password";
        $this->db->query($query);
        $this->db->bind(':username', $username);
        $this->db->bind(':password', $password);

        return $this->db->resultSingle();

    }

    public function get_edf_milestones_info($id)
    {
        return []; //MV not yet developed
        $query = "
            select 
                CASE WHEN mv.status_id = 2 THEN 'Approved' WHEN mv.status_id in (3,39) THEN 'Approved' else  'Pending' END as status,
                ab.num_milestone as num_ms
            from edf_approved_business_milestones ms
            left join edf_approved_business as ab on ab.id = ms.parent_id
            left join edf_expression_of_interest as eoi on eoi.id = ab.eoi_id
            left join edf_milestone_verification as mv on mv.milestone_fid = ms.id
            where ab.eoi_id = :id";
        $this->db->query($query);
        $this->db->bind(':id', $id);
        $results = $this->db->resultSet();
        return $results;
    }

}
