<?php

namespace Ext\Triggers\ImmediateAssistance;

use App\Core\BaseTrigger;
use App\Exceptions\CriticalException;
use App\Exceptions\IlegalUserActionException;
use App\Helpers\MiscHelper;
use DateTime;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {
        if ($data->tables[1]->data) {
            $selected_options = $data->tables[1]->data;
            if (count($selected_options) > 4) {
                $optionsString = implode(", ", $selected_options);
                throw new IlegalUserActionException("Inválido, usted ha seleccionado la opción 'El jefe no recibe asistencia' y también ha seleccionado: $optionsString");
            }

        }
    }
}
