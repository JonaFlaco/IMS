<?php

namespace Ext\Externalapi;

use App\Core\BaseExternalApi;
use App\Core\Communications\EmailService;
use App\Core\Node;
use App\Exceptions\ForbiddenException;
use App\Exceptions\IlegalUserActionException;
use App\Exceptions\MissingDataFromRequesterException;
use Exception;
use SebastianBergmann\LinesOfCode\IllogicalValuesException;

class EdfEoiEmailSendOtp extends BaseExternalApi
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

        $item = $this->coreModel->nodeModel("edf_eoi_email_verification")
            ->where("email_address = :email_address")
            ->where("abs(datediff(ss, m.created_date, getdate())) < 60")
            ->bindValue("email_address", $email_address)
            ->OrderBy("id desc")
            ->loadFirstOrDefault();

        if(isset($item)) {
            throw new \App\Exceptions\IlegalUserActionException("Debes esperar al menos 1 minuto para obtener otro código");
        }

        $code = rand(100000, 999999);

        $node = new Node("edf_eoi_email_verification");
        $node->email_address = $email_address;
        $node->code = $code;
        $node->save();


        (new EmailService($email_address, "Código de verificación de correo electrónico del EDF", "Utilice el siguiente código para verificar su dirección de correo electrónico: <strong>$code</strong>"))->sendNow();

        $this->app->response->returnSuccess();
        
    }
}
