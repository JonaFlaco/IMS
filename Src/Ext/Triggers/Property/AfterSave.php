<?php

namespace Ext\Triggers\Property;

use App\Core\BaseTrigger;

class AfterSave extends BaseTrigger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {
        $housesVerification = $this->coreModel->nodeModel("property")
            ->id($id)
            ->loadFirstOrFail();

        $housesVerification->score = $this->calculateScore($housesVerification);

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

        // Funcion para actualizar el user_form_property
        $rentInfo = $this->coreModel->nodeModel("rent_info_user_form_property")
            ->where("m.code = :code")
            ->bindValue("code", $housesVerification->code)
            ->loadFirstOrDefault();

        if (isset($rentInfo)) {

            //rentInfoUserFormProperty
            $rentInfo->property_type = $housesVerification->property_type;
            $rentInfo->owner_house = $housesVerification->owner_house;
            $rentInfo->house_property = $housesVerification->house_property;
            $rentInfo->availability_rent = $housesVerification->availability_rent;
            $rentInfo->rental_price = $housesVerification->rental_price;

            //Direccion
            $rentInfo->property_same_area = $housesVerification->property_same_area;
            $rentInfo->province = $housesVerification->province;
            $rentInfo->city = $housesVerification->city;
            $rentInfo->neighborhood = $housesVerification->neighborhood;
            $rentInfo->sector = $housesVerification->sector;
            $rentInfo->addres = $housesVerification->address;
            $rentInfo->latitude = $housesVerification->latitude;
            $rentInfo->lenght = $housesVerification->lenght;

            // VERIFICACION rentInfoUserFormProperty VIVIENDA
            $rentInfo->verification_house = $housesVerification->verification_house;

            // PREGUNTAS PARA LA EVALUACION
            // SECCION A
            $rentInfo->street_lighting = $housesVerification->street_lighting;
            $rentInfo->waste_collection = $housesVerification->waste_collection;
            $rentInfo->nearby_security = $housesVerification->nearby_security;
            $rentInfo->health_emergency = $housesVerification->health_emergency;
            $rentInfo->nearby_schools = $housesVerification->nearby_schools;
            $rentInfo->nearby_parks = $housesVerification->nearby_parks;
            $rentInfo->nearby_markets = $housesVerification->nearby_markets;
            $rentInfo->nearby_transport = $housesVerification->nearby_transport;
            $rentInfo->observation_a_eight = $housesVerification->observation_a_eight;
            $rentInfo->observation_a_street_lighting = $housesVerification->observation_a_street_lighting;
            $rentInfo->observation_a_two = $housesVerification->observation_a_two;
            $rentInfo->observation_a_three = $housesVerification->observation_a_three;
            $rentInfo->observation_a_four = $housesVerification->observation_a_four;
            $rentInfo->observation_a_five = $housesVerification->observation_a_five;
            $rentInfo->observation_a_six = $housesVerification->observation_a_six;
            $rentInfo->observation_a_seven = $housesVerification->observation_a_seven;

            // SECCION B
            $rentInfo->separated_bedrooms = $housesVerification->separated_bedrooms;
            $rentInfo->private_bathroom = $housesVerification->private_bathroom;
            $rentInfo->laundry_space = $housesVerification->laundry_space;
            $rentInfo->cooking_space = $housesVerification->cooking_space;
            $rentInfo->social_space = $housesVerification->social_space;
            $rentInfo->safe_play_space = $housesVerification->safe_play_space;
            $rentInfo->wardrobes_storage = $housesVerification->wardrobes_storage;
            $rentInfo->storage_shelves = $housesVerification->storage_shelves;
            $rentInfo->room_occupancy = $housesVerification->room_occupancy;
            $rentInfo->ceiling_height = $housesVerification->ceiling_height;
            $rentInfo->windows_lighting_ventilation = $housesVerification->windows_lighting_ventilation;
            $rentInfo->observation_b_eleven = $housesVerification->observation_b_eleven;
            $rentInfo->observation_b_one = $housesVerification->observation_b_one;
            $rentInfo->observation_b_two = $housesVerification->observation_b_two;
            $rentInfo->observation_b_three = $housesVerification->observation_b_three;
            $rentInfo->observation_b_four = $housesVerification->observation_b_four;
            $rentInfo->observation_b_five = $housesVerification->observation_b_five;
            $rentInfo->observation_b_six = $housesVerification->observation_b_six;
            $rentInfo->observation_b_seven = $housesVerification->observation_b_seven;
            $rentInfo->observation_b_eight = $housesVerification->observation_b_eight;
            $rentInfo->observation_b_nine = $housesVerification->observation_b_nine;
            $rentInfo->observation_b_ten = $housesVerification->observation_b_ten;

            // SECCION C
            $rentInfo->electricity_service = $housesVerification->electricity_service;
            $rentInfo->damaged_wiring = $housesVerification->damaged_wiring;
            $rentInfo->internal_lighting = $housesVerification->internal_lighting;
            $rentInfo->water_service = $housesVerification->water_service;
            $rentInfo->water_storage_system = $housesVerification->water_storage_system;
            $rentInfo->toilet_condition = $housesVerification->toilet_condition;
            $rentInfo->sink_condition = $housesVerification->sink_condition;
            $rentInfo->shower_condition = $housesVerification->shower_condition;
            $rentInfo->waste_disposal_system = $housesVerification->waste_disposal_system;
            $rentInfo->observation_c_one = $housesVerification->observation_c_one;
            $rentInfo->observation_c_two = $housesVerification->observation_c_two;
            $rentInfo->observation_c_three = $housesVerification->observation_c_three;
            $rentInfo->observation_c_four = $housesVerification->observation_c_four;
            $rentInfo->observation_c_five = $housesVerification->observation_c_five;
            $rentInfo->observation_c_six = $housesVerification->observation_c_six;
            $rentInfo->observation_c_seven = $housesVerification->observation_c_seven;
            $rentInfo->observation_c_eight = $housesVerification->observation_c_eight;
            $rentInfo->observation_c_nine = $housesVerification->observation_c_nine;

            // SECCION D
            $rentInfo->roof_condition = $housesVerification->roof_condition;
            $rentInfo->walls_condition_rent = $housesVerification->walls_condition_rent;
            $rentInfo->floor_condition_rent = $housesVerification->floor_condition_rent;
            $rentInfo->windows_doors_condition = $housesVerification->windows_doors_condition;
            $rentInfo->flood_exposure = $housesVerification->flood_exposure;
            $rentInfo->earthquake_repairs = $housesVerification->earthquake_repairs;
            $rentInfo->exposed_to_landslides = $housesVerification->exposed_to_landslides;
            $rentInfo->settling_repairs = $housesVerification->settling_repairs;
            $rentInfo->secure_entrance = $housesVerification->secure_entrance;
            $rentInfo->clear_addresses = $housesVerification->clear_addresses;
            $rentInfo->neighborhood_security = $housesVerification->neighborhood_security;
            $rentInfo->observation_d_one = $housesVerification->observation_d_one;
            $rentInfo->observation_d_two = $housesVerification->observation_d_two;
            $rentInfo->observation_d_three = $housesVerification->observation_d_three;
            $rentInfo->observation_d_four = $housesVerification->observation_d_four;
            $rentInfo->observation_d_five = $housesVerification->observation_d_five;
            $rentInfo->observation_d_six = $housesVerification->observation_d_six;
            $rentInfo->observation_d_seven = $housesVerification->observation_d_seven;
            $rentInfo->observation_d_eight = $housesVerification->observation_d_eight;
            $rentInfo->observation_d_nine = $housesVerification->observation_d_nine;
            $rentInfo->observation_d_ten = $housesVerification->observation_d_ten;
            $rentInfo->observation_d_eleven = $housesVerification->observation_d_eleven;

            // SECCION E
            $rentInfo->other_beneficiaries = $housesVerification->other_beneficiaries;
            $rentInfo->community_relationship_perception = $housesVerification->community_relationship_perception;
            $rentInfo->housing_accept_diverse_genders = $housesVerification->housing_accept_diverse_genders;
            $rentInfo->housing_accept_other_nationalities = $housesVerification->housing_accept_other_nationalities;
            $rentInfo->housing_accept_other_ethnicities = $housesVerification->housing_accept_other_ethnicities;
            $rentInfo->housing_accept_pets = $housesVerification->housing_accept_pets;
            $rentInfo->legal_owner_property = $housesVerification->legal_owner_property;
            $rentInfo->observation_e_one = $housesVerification->observation_e_one;
            $rentInfo->observation_e_two = $housesVerification->observation_e_two;
            $rentInfo->observation_e_three = $housesVerification->observation_e_three;
            $rentInfo->observation_e_four = $housesVerification->observation_e_four;
            $rentInfo->observation_e_five = $housesVerification->observation_e_five;
            $rentInfo->observation_e_six = $housesVerification->observation_e_six;
            $rentInfo->observation_e_seven = $housesVerification->observation_e_seven;


            $this->coreModel->node_save($rentInfo, [
                "ignore_post_save" => true,
                "ignore_pre_save" => true,
                "dont_add_log" => true,
                "justification" => "Update record"
            ]);
        }
    }

    // Funcion de suma de puntaje
    private function calculateScore($house)
    {
        $score = 0;

        if ($house->street_lighting == 1) {
            $score += 5;
        }

        if ($house->waste_collection == 1) {
            $score += 5;
        }

        if ($house->nearby_security == 1) {
            $score += 3;
        }

        if ($house->health_emergency == 1) {
            $score += 3;
        }

        if ($house->nearby_schools == 1) {
            $score += 5;
        }

        if ($house->nearby_parks == 1) {
            $score += 3;
        }

        if ($house->nearby_markets == 1) {
            $score += 5;
        }

        if ($house->nearby_transport == 1) {
            $score += 5;
        }

        // PREGUNTAS GRUPO B
        if ($house->separated_bedrooms == 1) {
            $score += 5;
        }

        if ($house->private_bathroom == 1) {
            $score += 5;
        }

        if ($house->laundry_space == 1) {
            $score += 5;
        }

        if ($house->cooking_space == 1) {
            $score += 5;
        }

        if ($house->social_space == 1) {
            $score += 3;
        }

        if ($house->safe_play_space == 1) {
            $score += 3;
        }

        if ($house->wardrobes_storage == 1) {
            $score += 3;
        }

        if ($house->storage_shelves == 1) {
            $score += 3;
        } elseif ($house->storage_shelves == 2) {
            $score += 2;
        } elseif ($house->storage_shelves == 4) {
            $score += 1;
        }

        if ($house->room_occupancy == 1) {
            $score += 5;
        } elseif ($house->room_occupancy == 2) {
            $score += 3;
        } elseif ($house->room_occupancy == 3) {
            $score += 0;
        }

        if ($house->ceiling_height == 1) {
            $score += 3;
        }

        if ($house->windows_lighting_ventilation == 1) {
            $score += 3;
        }

        // PREGUNTAS GRUPO C
        if ($house->electricity_service == 1) {
            $score += 5;
        }

        if ($house->damaged_wiring == 0) {
            $score += 3;
        }

        if ($house->internal_lighting == 1) {
            $score += 5;
        }

        if ($house->water_service == 1) {
            $score += 5;
        }

        if ($house->water_storage_system == 1) {
            $score += 3;
        }

        if ($house->toilet_condition == 1) {
            $score += 5;
        } elseif ($house->toilet_condition == 2) {
            $score += 3;
        } elseif ($house->toilet_condition == 4) {
            $score += 1;
        }

        if ($house->sink_condition == 1) {
            $score += 5;
        } elseif ($house->sink_condition == 2) {
            $score += 3;
        } elseif ($house->sink_condition == 4) {
            $score += 1;
        }

        if ($house->shower_condition == 1) {
            $score += 5;
        } elseif ($house->shower_condition == 2) {
            $score += 3;
        } elseif ($house->shower_condition == 4) {
            $score += 1;
        }

        if ($house->waste_disposal_system == 1) {
            $score += 5;
        }

        // PREGUNTAS GRUPO D
        if ($house->roof_condition == 1) {
            $score += 5;
        } elseif ($house->roof_condition == 2) {
            $score += 3;
        } elseif ($house->roof_condition == 4) {
            $score += 1;
        }

        if ($house->walls_condition_rent == 1) {
            $score += 5;
        } elseif ($house->walls_condition_rent == 2) {
            $score += 3;
        } elseif ($house->walls_condition_rent == 4) {
            $score += 1;
        }

        if ($house->floor_condition_rent == 1) {
            $score += 5;
        } elseif ($house->floor_condition_rent == 2) {
            $score += 3;
        } elseif ($house->floor_condition_rent == 4) {
            $score += 1;
        }

        if ($house->windows_doors_condition == 1) {
            $score += 5;
        } elseif ($house->windows_doors_condition == 2) {
            $score += 3;
        } elseif ($house->windows_doors_condition == 4) {
            $score += 1;
        }

        if ($house->flood_exposure == 0) {
            $score += 5;
        }

        if ($house->earthquake_repairs == 0) {
            $score += 5;
        }

        if ($house->exposed_to_landslides == 0) {
            $score += 5;
        }

        if ($house->settling_repairs == 0) {
            $score += 5;
        }

        if ($house->secure_entrance == 1) {
            $score += 5;
        }

        if ($house->clear_addresses == 1) {
            $score += 1;
        }

        if ($house->neighborhood_security == 1) {
            $score += 5;
        } elseif ($house->neighborhood_security == 2) {
            $score += 3;
        } elseif ($house->neighborhood_security == 3) {
            $score += 1;
        }

        // PREGUNTAS GRUPO E
        if ($house->other_beneficiaries == 1) {
            $score += 5;
        }

        if ($house->community_relationship_perception == 1) {
            $score += 5;
        } elseif ($house->community_relationship_perception == 2) {
            $score += 5;
        } elseif ($house->community_relationship_perception == 4) {
            $score += 0;
        }

        if ($house->housing_accept_diverse_genders == 1) {
            $score += 5;
        }

        if ($house->housing_accept_other_nationalities == 1) {
            $score += 5;
        }

        if ($house->housing_accept_other_ethnicities == 1) {
            $score += 5;
        }

        if ($house->housing_accept_pets == 1) {
            $score += 3;
        }

        if ($house->legal_owner_property == 1) {
            $score += 5;
        }

        return $score;
    }
}
