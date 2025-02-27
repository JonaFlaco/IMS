<?php

namespace Ext\Externalapi;

use App\Core\BaseExternalApi;
use App\Core\Node;
use App\Exceptions\ForbiddenException;
use App\Exceptions\IlegalUserActionException;
use App\Exceptions\MissingDataFromRequesterException;
use Exception;
use SebastianBergmann\LinesOfCode\IllogicalValuesException;

class EdfEoiEmailVerifyOtp extends BaseExternalApi
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $params = [])
    {
        
        if($this->app->request->isPost() != true) {
            throw new IllogicalValuesException("Only POST requests are accepted");
        }
        $postData = $this->app->request->POST();
        
        if(!isset($postData["email_address"]))
            throw new IllogicalValuesException("Email address is missing");

        $email_address = $postData["email_address"];
            
        
        if(!isset($postData["code"])) 
            throw new IllogicalValuesException("code is missing");

        $code = $postData["code"];

        $item = $this->coreModel->nodeModel("edf_eoi_email_verification")
            ->where("email_address = :email_address")
            ->where("abs(datediff(ss, m.created_date, getdate())) < 120")
            ->where("code = :code")
            ->bindValue("email_address", $email_address)
            ->bindValue("code", $code)
            ->OrderBy("id desc")
            ->loadFirstOrDefault();

        if(!isset($item)) {
            throw new \App\Exceptions\IlegalUserActionException("Codigo invalido");
        } 
        
        $this->app->response->returnSuccess();
        
    }
}
