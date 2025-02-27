<?php

namespace Ext\Triggers\EdfEoi;

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

        $edfEoi = $this->coreModel->nodeModel("edf_eoi")
            ->id($id)
            ->loadFirstOrFail();

        // if ($is_update != true)
        //     $this->sendEmail($edfEoi);

        $edfEoi->score = $this->calculateScore($edfEoi);

        $this->coreModel->node_save($edfEoi, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update scoring"]);
    }

    function calculateScore($item)
    {

        $score = 0;

        if ($item->gender_legal_rep == 2)
            $score += 5;

        if ($item->has_female_leader  == 1)
            $score += 5;


        if ($item->female_employees_number ) {
            $femalePercent = ($item->female_employees_number  / $item->employees_number) * 15;
            $score += $femalePercent;
        }

        if ($item->operation_years_company < 8) {
            if ($item->operation_years_company >=  1 && $item->operation_years_company <=  3)
                $score += 2;

            if ($item->operation_years_company >=  4 && $item->operation_years_company <=  5)
                $score += 3;

            if ($item->operation_years_company >=  6 && $item->operation_years_company <=  7)
                $score += 4;
        }

        if ($item->operation_years_company >  7)
            $score += 5;

        if ($item->total_income_tres >= 50000 && $item->total_income_tres <= 100000)
            $score += 5;

        if ($item->total_income_tres > 100000 && $item->total_income_tres <= 1000000)
            $score += 10;

        if ($item->total_income_tres > 1000000 && $item->total_income_tres <= 2000000)
            $score += 15;

        if ($item->employees_number > 0 && $item->employees_number <  10)
            $score += 1;

        if ($item->employees_number > 9 && $item->employees_number <  50)
            $score += 3;

        if ($item->employees_number > 49 && $item->employees_number <  200)
            $score += 4;

        if ($item->employees_number > 199)
            $score += 5;

        $priority_employees_number = $item->migrant_employees + $item->refugee_employees + $item->returned_employees + $item->disability_employees + $item->lgbt_employees;
        if ($priority_employees_number) {
            $priorityPercent = ($priority_employees_number / $item->employees_number) * 5;

            $score += $priorityPercent;
        }

        if ($item->has_social_responsability == 1)
            $score += 2;

        if ($item->has_aditional_benefits  == 1)
            $score += 1;

        if ($item->has_diversity_politics   == 1)
            $score += 2;

        //"(Owner’s contribution/(Amount requested))*15 Limit on maximum points as 15"
        $comparative = ($item->contribution_value  / ($item->total_amount__iom)) * 15;
        if ($comparative <= 15)
            $score += $comparative;

        if ($comparative > 15)
            $score += 15;

        //"(2000/*(Amount requested/Jobs created))*25 Limit on maximum points as 25"
        $newStaffPercent = (2000 / ($item->total_amount__iom / $item->additional_staff_need)) * 25;
        if ($newStaffPercent <= 25)
            $score += $newStaffPercent;

        if ($newStaffPercent > 25)
            $score += 25;


        return $score;
    }

    // function sendEmail($edfEoi)
    // {
    //     $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EdfEoiAfterSubmission.html', true);

    //     $body = _str_replace("{{title}}", "¡Su solicitud ha sido recibida!", $body);
    //     $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
    //     $body = _str_replace("{{applicant_name}}", $edfEoi->person_applying, $body);
    //     $body = _str_replace("{{username}}", $edfEoi->code, $body);
    //     $body = _str_replace("{{password}}", $edfEoi->password, $body);
    //     $body = _str_replace("{{link}}", "https://edf_ecuador.iom.int/en/track-application", $body);

    //     $attachments = LOGO_FULL_PATH;

    //     //Send email to the user's email
    //     (new EmailService($edfEoi->aplicant_email, "¡Su solicitud ha sido recibida!", $body))
    //         ->setUserId($this->app->user->getSystemUserId())
    //         ->setCtypeId("edf_eoi")
    //         ->setRecordId($edfEoi->id)
    //         ->setAttachments($attachments)
    //         ->sendNow();
    // }
}
