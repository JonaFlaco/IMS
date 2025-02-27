<?php

namespace Ext\Triggers\EdfEoiVerification;

use App\Core\BaseTrigger;
use App\Core\Communications\EmailService;

class AfterSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {

        $item = $this->coreModel->nodeModel("edf_eoi_verification")
            ->id($id)
            ->loadFirstOrFail();

        $edf = $this->coreModel->nodeModel("edf_eoi")
            ->where("m.id = :id")
            ->bindValue("id", $item->business_id)
            ->load();

        //$code = rand(100000, 999999);
        //$item->password = $code;
        $item->score = $this->recalculateScore($item, $edf);
        $this->coreModel->node_save($item, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update scoring"]);
    }

    function recalculateScore($item, $edf)
    {

        $score = 0;

        $business_contr = $edf[0]->contribution_value;
        $iom_contribution = $edf[0]->total_amount__iom;
        $aditional_staff_value = $edf[0]->additional_staff_need;


        //Is the amount the business owner is proposing to contribute comparable to the amount they are requesting?
        if ($item->correct_contribution_bsns && $item->correct_grant_iom) {
            // Both values are set
            $business_contr = $item->correct_contribution_bsns;
            $iom_contribution = $item->correct_grant_iom;
        } elseif ($item->correct_contribution_bsns) {
            // Only correct_contribution_bsns is set
            $business_contr = $item->correct_contribution_bsns;
        } elseif ($item->correct_grant_iom) {
            // Only correct_grant_iom is set
            $iom_contribution = $item->correct_grant_iom;
        }

        //Cost per job How many additional staff will they add, given a benchmark of 1 staff per $5,000 granted  
        if ($item->correct_aditional_staff || $item->correct_grant_iom) {
            //correct aditional_staff_value if correct_aditional_staff exists
            if ($item->correct_aditional_staff)
                $aditional_staff_value = $item->correct_aditional_staff;

            $investment = ($aditional_staff_value / ($iom_contribution / 2000) * 25);
            $score += $investment > 25 ? 25 : $investment;
        }

        // Is the amount the business owner is proposing to contribute comparable to the amount they are requesting?
        $correct_comp = ($business_contr / ($iom_contribution)) * 10;
        $correct_score = $correct_comp > 10 ? 10 : $correct_comp;
        $score += $correct_score;

        //Years of operation
        if ($item->correct_years_operation) {

            if ($item->correct_years_operation > 7)
                $score += 5;

            if ($item->correct_years_operation > 1 && $item->correct_years_operation <= 7)
                $score += ($item->correct_years_operation / 7) * 5;
        }
        if (!$item->correct_years_operation) {

            if ($edf[0]->operation_years_company > 7)
                $score = 5;

            if ($edf[0]->operation_years_company > 1 && $edf[0]->operation_years_company <= 7)
                $score += ($edf[0]->operation_years_company / 7) * 5;
        }

        //Is the legal representative a woman?
        if ($item->correct_gender_rep == 2)
            $score += 5;

        if (!$item->correct_gender_rep && $edf[0]->gender_legal_rep == 2)
            $score += 5;

        //Is there more female workers than male workers currently?
        if ($item->correct_current_employees)
            $score += ($item->correct_number_female_employees / $item->correct_current_employees) * 5;

        if (!$item->correct_current_employees)
            $score += ($edf[0]->female_employees_number / $edf[0]->employees_number) * 5;

        //Number of employees from groups considered priority
        if ($item->confirm_priority_employees == 0) {
            $priorityNum = $item->correct_migrant_employees + $item->correct_refugee_employees + $item->correct_returned_employees + $item->correct_disability_employees + $item->correct_lgbt_employees;
            $score += ($priorityNum / $item->correct_current_employees) * 5;
        } else {
            $priority_employees_number = $edf[0]->migrant_employees + $edf[0]->refugee_employees + $edf[0]->returned_employees + $edf[0]->disability_employees + $edf[0]->lgbt_employees;
            if ($priority_employees_number) {
                $priorityPercent = ($priority_employees_number / $edf[0]->employees_number) * 5;

                $score += $priorityPercent;
            }
        }

        //  Is the location good for business
        if ($item->business_location == 1)
            $score += 2.5;

        //  Is the business environment safe and inclusive for all employees? (including bathrooms, social areas, working conditions, safe use of equipment, PPE, fireproof measures, security measures, and others depending on each business)
        if ($item->business_safe == 1)
            $score += 2.5;

        //Is the business compliant with labour standards and good buisness practices?
        if ($item->standards_practices == 1)
            $score += 2.5;

        //During your visit, did you observe business activities or were they able to demonstrate  recent activity related to the business (including customers, staff working, production, equipment in use, other)?
        if ($item->observe_business == 1)
            $score += 2.5;

        //Do you recommend this business for EDF grant?
        if ($item->edf_recommend  == 1)
            $score += 2.5;


        if ($item->employee_ver) {
            //What is your average monthly salary?
            $averageSalary = ($item->employee_ver[0]->salary + $item->employee_ver[1]->salary) / 2;
            if ($averageSalary >= 460)
                $score += 6;

            //Does the business pay your salary on time?
            if ($item->employee_ver[0]->time_pay == 1 && $item->employee_ver[1]->time_pay == 1)
                $score += 6;

            if ($item->employee_ver[0]->time_pay == 1 && $item->employee_ver[1]->time_pay == 0)
                $score += 3;

            if ($item->employee_ver[0]->time_pay == 0 && $item->employee_ver[1]->time_pay == 1)
                $score += 3;

            //Do you receive any incentives (e.g. bonus, overtime, childcare, transport, trainings)?
            if ($item->employee_ver[0]->incentives  == 1 && $item->employee_ver[1]->incentives  == 1)
                $score += 6;

            if ($item->employee_ver[0]->incentives == 1 && $item->employee_ver[1]->incentives == 0)
                $score += 3;

            if ($item->employee_ver[0]->incentives == 0 && $item->employee_ver[1]->incentives == 1)
                $score += 3;

            //Do you think your job is sustainable?
            if ($item->employee_ver[0]->sustainable_job   == 1 && $item->employee_ver[1]->sustainable_job   == 1)
                $score += 6;

            if ($item->employee_ver[0]->sustainable_job  == 1 && $item->employee_ver[1]->sustainable_job  == 0)
                $score += 3;

            if ($item->employee_ver[0]->sustainable_job  == 0 && $item->employee_ver[1]->sustainable_job  == 1)
                $score += 3;

            //Is this a good working environment?
            if ($item->employee_ver[0]->environment_work    == 1 && $item->employee_ver[1]->environment_work    == 1)
                $score += 6;

            if ($item->employee_ver[0]->environment_work   == 1 && $item->employee_ver[1]->environment_work   == 0)
                $score += 3;

            if ($item->employee_ver[0]->environment_work   == 0 && $item->employee_ver[1]->environment_work   == 1)
                $score += 3;
        }

        return $score;
    }
}
