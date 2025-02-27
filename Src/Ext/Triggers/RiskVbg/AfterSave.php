<?php

namespace Ext\Triggers\RiskVbg;

use App\Core\BaseTrigger;

class AfterSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {

        $riskVbg = $this->coreModel->nodeModel("risk_vbg")
            ->id($id)
            ->loadFirstOrFail();

        $riskVbg->score = $this->calculateScore($riskVbg);

        $this->coreModel->node_save($riskVbg, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update scoring"]);
    }

    function calculateScore($item)
    {

        $score = 0;
        if ($item->is_etnicity_group == 1)
            $score += 2;

        if ($item->has_adictions == 1)
            $score += 1;

        if ($item->childhood_violence == 1)
            $score += 3;

        if ($item->previous_vbg == 1)
            $score += 5;

        if ($item->has_dependents == 1)
            $score += 1;

        if ($item->economic_activities == 1)
            $score += 2;

        if ($item->safe_activities == 1)
            $score += 2;

        if (!$item->family_support)
            $score += 3;

        if (!$item->safe_shelter)
            $score += 3;

        if ($item->stress  == 1)
            $score += 1;

        if ($item->violence_behaivor  == 1)
            $score += 3;

        if ($item->violent_childhood  == 1)
            $score += 3;

        if ($item->substances_use  == 1)
            $score += 2;

        if ($item->personality_trastorn  == 1)
            $score += 2;

        if ($item->same_house  == 1)
            $score += 3;

        if ($item->is_survivor_couple == 1)
            $score += 3;

        if ($item->has_power_advantage == 1)
            $score += 3;

        if ($item->has_weapons == 1)
            $score += 4;

        if ($item->irregular_group == 1)
            $score += 5;

        if ($item->has_survivor_stuff == 1)
            $score += 3;

        if ($item->unfaithful == 1)
            $score += 1;

        if ($item->violence) {
            foreach ($item->violence as $violence) {
                if ($violence->value == 1) {
                    $score += 3;
                }
                if ($violence->value == 2) {
                    $score += 3;
                }
                if ($violence->value == 3) {
                    $score += 5;
                }
                if ($violence->value == 4) {
                    $score += 3;
                }
            }
        }

        if ($item->continuum_violence == 1)
            $score += 5;

        if ($item->separated_alert == 1)
            $score += 3;

        if ($item->held_by_agressor == 1)
            $score += 5;

        if ($item->limited_access_stuff == 1)
            $score += 3;

        if ($item->dead_advertisement == 1)
            $score += 5;

        if ($item->killing_intentions == 1)
            $score += 5;

        if ($item->physique_violence_agretions == 1)
            $score += 5;

        if ($item->can_access_house_shelter == 1)
            $score += 4;

        if ($item->health_access == 1)
            $score += 3;

        if ($item->psychologic_services_access == 1)
            $score += 3;

        if ($item->social_service_access == 1)
            $score += 3;

        if ($item->access_assortment == 1)
            $score += 3;

        if ($item->livelihoods == 1)
            $score += 3;


        return $score;
    }
}
