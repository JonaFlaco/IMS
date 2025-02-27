<?php

namespace Ext\Externalapi;

use App\Core\BaseExternalApi;
use App\Exceptions\ForbiddenException;
use App\Exceptions\IlegalUserActionException;
use App\Exceptions\MissingDataFromRequesterException;
use Exception;

class EdfBusinessInfo extends BaseExternalApi
{

    public function __construct()
    {
        parent::__construct(true);
    }

    public function index($id, $params = [])
    {
        $ext_model = new \Ext\Models\ExtModel;

        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            throw new IlegalUserActionException("Only POST requests are accepted");
        }
        
        $username = isset($_POST['username']) ? $_POST['username'] : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        if(empty($username) || empty($password)) {
            throw new MissingDataFromRequesterException("Username or password is missing");
        }
        
        $businessData = $ext_model->get_edf_business_info($username, $password);
        
        if (!isset($businessData)) {
            throw new ForbiddenException("Invalid username or password");
        }

        $resultData = (object)[];
        
        $resultData->business_id = $businessData->business_id;
        $resultData->business_name = $businessData->business_name;
        $resultData->full_name = $businessData->full_name;
        $resultData->status_list = [];
        $resultData->milestones_status_list = [];

        $resultData->status_list[] =  array(
            'name' => 'Expression of Interest',
            'status' => $businessData->eoi_status
        );
        $resultData->status_list[] =  array(
            'name' => 'EOI Verification',
            'status' => $businessData->ver_status
        );
        $resultData->status_list[] =  array(
            'name' => 'Application',
            'status' => $businessData->app_status
        );
        $resultData->status_list[] =  array(
            'name' => 'Approved Business Plan',
            'status' => $businessData->ab_status
        );
        $resultData->status_list[] =  array(
            'name' => 'Investment Committee',
            'status' => $businessData->icv_status
        );

        $edf_milestones_data = $ext_model->get_edf_milestones_info($businessData->business_id);

        $i = 1;
        foreach ($edf_milestones_data as $row) {
            $resultData->milestones_status_list[] =  array(
                'name' => 'Milestones Number ' . ($i++),
                'status' => $row->status
            );
        }

        echo json_encode((array)[
            "status" => "success",
            "result" => $resultData
        ]);
    }
}
