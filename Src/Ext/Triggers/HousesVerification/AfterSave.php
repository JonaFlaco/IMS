<?php

namespace Ext\Triggers\HousesVerification;

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
        $housesVerification = $this->coreModel->nodeModel("houses_verification")
            ->id($id)
            ->loadFirstOrFail();

        if (
            $housesVerification->room_occupancy == 3 ||
            $housesVerification->exposed_to_landslides == 1 ||
            $housesVerification->housing_accept_diverse_genders == 0 ||
            $housesVerification->housing_accept_other_nationalities == 0 ||
            $housesVerification->housing_accept_other_ethnicities == 0 ||
            $housesVerification->legal_owner_property == 0
        ) {

            $housesVerification->status_id = 3;
            $this->coreModel->node_save($housesVerification, [
                "ignore_post_save" => true,
                "ignore_pre_save" => true,
                "dont_add_log" => true,
                "justification" => "RECHAZADO"
            ]);
            return;
        }

        $housesVerification->score = $this->calculateScore($housesVerification);

        if ($housesVerification->score >= 143) {
            $housesVerification->status_id = 2;
        } else {
            $housesVerification->status_id = 100;
        }

        $this->coreModel->node_save($housesVerification, [
            "ignore_post_save" => true,
            "ignore_pre_save" => true,
            "dont_add_log" => true,
            "justification" => "Update scoring"
        ]);
    }

    function calculateScore($item)
    {
        $score = 0;

        // PREGUNTAS GRUPO A
        if ($item->street_lighting == 1) {
            $score += 5;
        }

        if ($item->waste_collection == 1) {
            $score += 5;
        }

        if ($item->nearby_security == 1) {
            $score += 3;
        }

        if ($item->health_emergency == 1) {
            $score += 3;
        }

        if ($item->nearby_schools == 1) {
            $score += 5;
        }

        if ($item->nearby_parks == 1) {
            $score += 3;
        }

        if ($item->nearby_markets == 1) {
            $score += 5;
        }

        if ($item->nearby_transport == 1) {
            $score += 5;
        }

        // PREGUNTAS GRUPO B
        if ($item->separated_bedrooms == 1) {
            $score += 5;
        }

        if ($item->private_bathroom == 1) {
            $score += 5;
        }

        if ($item->laundry_space == 1) {
            $score += 5;
        }

        if ($item->cooking_space == 1) {
            $score += 5;
        }

        if ($item->social_space == 1) {
            $score += 3;
        }

        if ($item->safe_play_space == 1) {
            $score += 3;
        }

        if ($item->wardrobes_storage == 1) {
            $score += 3;
        }

        if ($item->storage_shelves == 1) {
            $score += 3;
        } elseif ($item->storage_shelves == 2) {
            $score += 2;
        } elseif ($item->storage_shelves == 4) {
            $score += 1;
        }

        if ($item->room_occupancy == 1) {
            $score += 5;
        } elseif ($item->room_occupancy == 2) {
            $score += 3;
        } elseif ($item->room_occupancy == 3) {
            $score += 0;
        }

        if ($item->ceiling_height == 1) {
            $score += 3;
        }

        if ($item->windows_lighting_ventilation == 1) {
            $score += 3;
        }

        // PREGUNTAS GRUPO C
        if ($item->electricity_service == 1) {
            $score += 5;
        }

        if ($item->damaged_wiring == 0) {
            $score += 3;
        }

        if ($item->internal_lighting == 1) {
            $score += 5;
        }

        if ($item->water_service == 1) {
            $score += 5;
        }

        if ($item->water_storage_system == 1) {
            $score += 3;
        }

        if ($item->toilet_condition == 1) {
            $score += 5;
        } elseif ($item->toilet_condition == 2) {
            $score += 3;
        } elseif ($item->toilet_condition == 4) {
            $score += 1;
        }

        if ($item->sink_condition == 1) {
            $score += 5;
        } elseif ($item->sink_condition == 2) {
            $score += 3;
        } elseif ($item->sink_condition == 4) {
            $score += 1;
        }

        if ($item->shower_condition == 1) {
            $score += 5;
        } elseif ($item->shower_condition == 2) {
            $score += 3;
        } elseif ($item->shower_condition == 4) {
            $score += 1;
        }

        if ($item->waste_disposal_system == 1) {
            $score += 5;
        }

        // PREGUNTAS GRUPO D
        if ($item->roof_condition == 1) {
            $score += 5;
        } elseif ($item->roof_condition == 2) {
            $score += 3;
        } elseif ($item->roof_condition == 4) {
            $score += 1;
        }

        if ($item->walls_condition_rent == 1) {
            $score += 5;
        } elseif ($item->walls_condition_rent == 2) {
            $score += 3;
        } elseif ($item->walls_condition_rent == 4) {
            $score += 1;
        }

        if ($item->floor_condition_rent == 1) {
            $score += 5;
        } elseif ($item->floor_condition_rent == 2) {
            $score += 3;
        } elseif ($item->floor_condition_rent == 4) {
            $score += 1;
        }

        if ($item->windows_doors_condition == 1) {
            $score += 5;
        } elseif ($item->windows_doors_condition == 2) {
            $score += 3;
        } elseif ($item->windows_doors_condition == 4) {
            $score += 1;
        }

        if ($item->flood_exposure == 0) {
            $score += 5;
        }

        if ($item->earthquake_repairs == 0) {
            $score += 5;
        }

        if ($item->exposed_to_landslides == 0) {
            $score += 5;
        }

        if ($item->settling_repairs == 0) {
            $score += 5;
        }

        if ($item->secure_entrance == 1) {
            $score += 5;
        }

        if ($item->clear_addresses == 1) {
            $score += 5;
        }

        if ($item->neighborhood_security == 1) {
            $score += 5;
        } elseif ($item->neighborhood_security == 2) {
            $score += 3;
        } elseif ($item->neighborhood_security == 3) {
            $score += 1;
        }

        // PREGUNTAS GRUPO E
        if ($item->other_beneficiaries == 1) {
            $score += 5;
        }

        if ($item->community_relationship_perception == 1) {
            $score += 5;
        } elseif ($item->community_relationship_perception == 2) {
            $score += 5;
        } elseif ($item->community_relationship_perception == 4) {
            $score += 0;
        }

        if ($item->housing_accept_diverse_genders == 1) {
            $score += 5;
        }

        if ($item->housing_accept_other_nationalities == 1) {
            $score += 5;
        }

        if ($item->housing_accept_other_ethnicities == 1) {
            $score += 5;
        }

        if ($item->housing_accept_pets == 1) {
            $score += 3;
        }

        if ($item->legal_owner_property == 1) {
            $score += 5;
        }

        return $score;
    }
}
