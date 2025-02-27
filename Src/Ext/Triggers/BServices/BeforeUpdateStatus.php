<?php

namespace Ext\Triggers\BServices;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\IlegalUserActionException;

class BeforeUpdateStatus extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $from_status_id, &$to_status_id, $step = 0, $total_steps = 0, $path = null, &$justification = null, $confirmed = null)
    {

        $caso = $this->coreModel->nodeModel("b_services")
            ->where("m.id = :id")
            ->bindValue("id", $id)
            ->loadFirstOrFail();
        if ($caso->sub_service == 8 && !$caso->support_docs && $caso->cbi_modality_regular == 2 && $to_status_id == 91)
            throw new IlegalUserActionException("Para cerrar un CBI regularizacion de tipo Adelanto debe adjuntar los documentos de soporte necesarios");
    }
}
