<?php

namespace Ext\Triggers\EdfEoiTwo;

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

        $edfEoi = $this->coreModel->nodeModel("edf_eoi_two")
            ->id($id)
            ->loadFirstOrFail();

        if ($is_update != true)
            $this->sendEmail($edfEoi);

        $edfEoi->score = $this->calculateScore($edfEoi);

        $this->coreModel->node_save($edfEoi, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update scoring"]);
    }

    function calculateScore($item)
    {

        $score = 0;

        if ($item->represent_gender == 2)
            $score += 5;

        if ($item->has_female_leader  == 1)
            $score += 5;


        if ($item->female_employees) {
            $femalePercent = ($item->female_employees / $item->employees_number) * 15;
            $score += $femalePercent;
        }

        if ($item->business_years < 8) {
            if ($item->business_years >=  1 && $item->business_years <=  3)
                $score += 2;

            if ($item->business_years >=  4 && $item->business_years <=  5)
                $score += 3;

            if ($item->business_years >=  6 && $item->business_years <=  7)
                $score += 4;
        }

        if ($item->business_years >  7)
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
        $comparative = ($item->business_contribution  / ($item->total_oim_contribution)) * 15;
        if ($comparative <= 15)
            $score += $comparative;

        if ($comparative > 15)
            $score += 15;

        //"(2000/*(Amount requested/Jobs created))*25 Limit on maximum points as 25"
        $newStaffPercent = (2000 / ($item->total_oim_contribution / $item->expantion_employees)) * 25;
        if ($newStaffPercent <= 25)
            $score += $newStaffPercent;

        if ($newStaffPercent > 25)
            $score += 25;

        return $score;
    }
    function sendEmail($edfEoi)
    {
        $body = file_get_contents(EXT_EMAIL_TEMPLATE_FOLDER . DS . 'EdfEoiTwoAfterSubmission.html', true);

        $body = _str_replace("{{title}}", "¡Su solicitud ha sido recibida!", $body);
        $body = _str_replace("{{apptitle}}", $this->app->settings->get('APP_TITLE'), $body);
        $body = _str_replace("{{applicant_name}}", $edfEoi->represent_name, $body);


        $attachments = LOGO_FULL_PATH;

        (new EmailService($edfEoi->email, "¡Su solicitud ha sido recibida!", $body))
            ->setUserId($this->app->user->getSystemUserId())
            ->setCtypeId("edf_eoi_two")
            ->setRecordId($edfEoi->id)
            ->setAttachments($attachments)
            ->sendNow();
    }
}
