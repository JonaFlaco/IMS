<?php

namespace Ext\Triggers\ApprovedBusinessPlan;

use App\Core\BaseTrigger;
use App\Core\Application;
use App\Exceptions\IlegalUserActionException;
use DateTime;

class BeforeSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($data, $is_update = false)
    {
        foreach ($data->tables[1]->data->data->tables as $milestones) {
            $milestones->data->total_milestone_amount =$milestones->data->milestone_amount_one+$milestones->data->contribution_amount;
        }
    }
}
