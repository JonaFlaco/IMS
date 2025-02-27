<?php

namespace Ext\Triggers\RentInfo;

use App\Core\BaseTrigger;
use App\Core\Node;

class AfterSave extends BaseTrigger
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($id, $data, $is_update = false)
    {
        $rentInfo = $this->coreModel->nodeModel("rent_info")
            ->id($id)
            ->loadFirstOrFail();

        if ($rentInfo->user_form_property) {
            $countHouses = count($rentInfo->user_form_property);
            if ($countHouses) {
                $rentInfo->rent_house = $countHouses;
                $this->coreModel->node_save($rentInfo, [
                    "ignore_post_save" => true,
                    "ignore_pre_save" => true,
                    "dont_add_log" => true,
                    "justification" => "Update House number"
                ]);
            }
        }

        $updatedScores = [];

        foreach ($rentInfo->user_form_property as $house) {
            $node = new Node("property");

            //ID del Dueño de la casa
            $node->owner_id = $house->parent_id;

            //Propiedad
            $node->property_type = $house->property_type;
            $node->owner_house = $house->owner_house;
            $node->house_property = $house->house_property;
            $node->availability_rent = $house->availability_rent;
            $node->rental_price = $house->rental_price;

            //Direccion
            $node->property_same_area = $house->property_same_area;
            $node->province = $house->province;
            $node->city = $house->city;
            $node->neighborhood = $house->neighborhood;
            $node->sector = $house->sector;
            $node->address = $house->addres;
            $node->latitude = $house->latitude;
            $node->lenght = $house->lenght;

            // VERIFICACION PROPIEDAD VIVIENDA
            $node->verification_house = $house->verification_house;

            // PREGUNTAS PARA LA EVALUACION
            // SECCION A
            $node->street_lighting = $house->street_lighting;
            $node->waste_collection = $house->waste_collection;
            $node->nearby_security = $house->nearby_security;
            $node->health_emergency = $house->health_emergency;
            $node->nearby_schools = $house->nearby_schools;
            $node->nearby_parks = $house->nearby_parks;
            $node->nearby_markets = $house->nearby_markets;
            $node->nearby_transport = $house->nearby_transport;
            $node->observation_a_eight = $house->observation_a_eight;
            $node->observation_a_street_lighting = $house->observation_a_street_lighting;
            $node->observation_a_two = $house->observation_a_two;
            $node->observation_a_three = $house->observation_a_three;
            $node->observation_a_four = $house->observation_a_four;
            $node->observation_a_five = $house->observation_a_five;
            $node->observation_a_six = $house->observation_a_six;
            $node->observation_a_seven = $house->observation_a_seven;

            // SECCION B
            $node->separated_bedrooms = $house->separated_bedrooms;
            $node->private_bathroom = $house->private_bathroom;
            $node->laundry_space = $house->laundry_space;
            $node->cooking_space = $house->cooking_space;
            $node->social_space = $house->social_space;
            $node->safe_play_space = $house->safe_play_space;
            $node->wardrobes_storage = $house->wardrobes_storage;
            $node->storage_shelves = $house->storage_shelves;
            $node->room_occupancy = $house->room_occupancy;
            $node->ceiling_height = $house->ceiling_height;
            $node->windows_lighting_ventilation = $house->windows_lighting_ventilation;
            $node->observation_b_eleven = $house->observation_b_eleven;
            $node->observation_b_one = $house->observation_b_one;
            $node->observation_b_two = $house->observation_b_two;
            $node->observation_b_three = $house->observation_b_three;
            $node->observation_b_four = $house->observation_b_four;
            $node->observation_b_five = $house->observation_b_five;
            $node->observation_b_six = $house->observation_b_six;
            $node->observation_b_seven = $house->observation_b_seven;
            $node->observation_b_eight = $house->observation_b_eight;
            $node->observation_b_nine = $house->observation_b_nine;
            $node->observation_b_ten = $house->observation_b_ten;

            // SECCION C
            $node->electricity_service = $house->electricity_service;
            $node->damaged_wiring = $house->damaged_wiring;
            $node->internal_lighting = $house->internal_lighting;
            $node->water_service = $house->water_service;
            $node->water_storage_system = $house->water_storage_system;
            $node->toilet_condition = $house->toilet_condition;
            $node->sink_condition = $house->sink_condition;
            $node->shower_condition = $house->shower_condition;
            $node->waste_disposal_system = $house->waste_disposal_system;
            $node->observation_c_one = $house->observation_c_one;
            $node->observation_c_two = $house->observation_c_two;
            $node->observation_c_three = $house->observation_c_three;
            $node->observation_c_four = $house->observation_c_four;
            $node->observation_c_five = $house->observation_c_five;
            $node->observation_c_six = $house->observation_c_six;
            $node->observation_c_seven = $house->observation_c_seven;
            $node->observation_c_eight = $house->observation_c_eight;
            $node->observation_c_nine = $house->observation_c_nine;

            // SECCION D
            $node->roof_condition = $house->roof_condition;
            $node->walls_condition_rent = $house->walls_condition_rent;
            $node->floor_condition_rent = $house->floor_condition_rent;
            $node->windows_doors_condition = $house->windows_doors_condition;
            $node->flood_exposure = $house->flood_exposure;
            $node->earthquake_repairs = $house->earthquake_repairs;
            $node->exposed_to_landslides = $house->exposed_to_landslides;
            $node->settling_repairs = $house->settling_repairs;
            $node->secure_entrance = $house->secure_entrance;
            $node->clear_addresses = $house->clear_addresses;
            $node->neighborhood_security = $house->neighborhood_security;
            $node->observation_d_one = $house->observation_d_one;
            $node->observation_d_two = $house->observation_d_two;
            $node->observation_d_three = $house->observation_d_three;
            $node->observation_d_four = $house->observation_d_four;
            $node->observation_d_five = $house->observation_d_five;
            $node->observation_d_six = $house->observation_d_six;
            $node->observation_d_seven = $house->observation_d_seven;
            $node->observation_d_eight = $house->observation_d_eight;
            $node->observation_d_nine = $house->observation_d_nine;
            $node->observation_d_ten = $house->observation_d_ten;
            $node->observation_d_eleven = $house->observation_d_eleven;

            // SECCION E
            $node->other_beneficiaries = $house->other_beneficiaries;
            $node->community_relationship_perception = $house->community_relationship_perception;
            $node->housing_accept_diverse_genders = $house->housing_accept_diverse_genders;
            $node->housing_accept_other_nationalities = $house->housing_accept_other_nationalities;
            $node->housing_accept_other_ethnicities = $house->housing_accept_other_ethnicities;
            $node->housing_accept_pets = $house->housing_accept_pets;
            $node->legal_owner_property = $house->legal_owner_property;
            $node->observation_e_one = $house->observation_e_one;
            $node->observation_e_two = $house->observation_e_two;
            $node->observation_e_three = $house->observation_e_three;
            $node->observation_e_four = $house->observation_e_four;
            $node->observation_e_five = $house->observation_e_five;
            $node->observation_e_six = $house->observation_e_six;
            $node->observation_e_seven = $house->observation_e_seven;

            $code = "IOM-CASA-" . $house->id;
            $node->code = $code;
            $house->code = $code;

            $propiedad = $this->coreModel->nodeModel("property")
                ->where("m.code = :code")
                ->bindValue("code", $code)
                ->loadFirstOrDefault();

            if (!$propiedad) {
                $node->save();
            } else {
                $propiedad->owner_id = $house->parent_id;

                //Propiedad
                $propiedad->property_type = $house->property_type;
                $propiedad->owner_house = $house->owner_house;
                $propiedad->house_property = $house->house_property;
                $propiedad->availability_rent = $house->availability_rent;
                $propiedad->rental_price = $house->rental_price;

                //Direccion
                $propiedad->province = $house->province;
                $propiedad->property_same_area = $house->property_same_area;
                $propiedad->city = $house->city;
                $propiedad->neighborhood = $house->neighborhood;
                $propiedad->sector = $house->sector;
                $propiedad->address = $house->addres;
                $propiedad->latitude = $house->latitude;
                $propiedad->lenght = $house->lenght;

                // VERIFICACION PROPIEDAD VIVIENDA
                $propiedad->verification_house = $house->verification_house;

                // PREGUNTAS PARA LA EVALUACION
                // SECCION A
                $propiedad->street_lighting = $house->street_lighting;
                $propiedad->waste_collection = $house->waste_collection;
                $propiedad->nearby_security = $house->nearby_security;
                $propiedad->health_emergency = $house->health_emergency;
                $propiedad->nearby_schools = $house->nearby_schools;
                $propiedad->nearby_parks = $house->nearby_parks;
                $propiedad->nearby_markets = $house->nearby_markets;
                $propiedad->nearby_transport = $house->nearby_transport;
                $propiedad->observation_a_eight = $house->observation_a_eight;
                $propiedad->observation_a_street_lighting = $house->observation_a_street_lighting;
                $propiedad->observation_a_two = $house->observation_a_two;
                $propiedad->observation_a_three = $house->observation_a_three;
                $propiedad->observation_a_four = $house->observation_a_four;
                $propiedad->observation_a_five = $house->observation_a_five;
                $propiedad->observation_a_six = $house->observation_a_six;
                $propiedad->observation_a_seven = $house->observation_a_seven;

                // SECCION B
                $propiedad->separated_bedrooms = $house->separated_bedrooms;
                $propiedad->private_bathroom = $house->private_bathroom;
                $propiedad->laundry_space = $house->laundry_space;
                $propiedad->cooking_space = $house->cooking_space;
                $propiedad->social_space = $house->social_space;
                $propiedad->safe_play_space = $house->safe_play_space;
                $propiedad->wardrobes_storage = $house->wardrobes_storage;
                $propiedad->storage_shelves = $house->storage_shelves;
                $propiedad->room_occupancy = $house->room_occupancy;
                $propiedad->ceiling_height = $house->ceiling_height;
                $propiedad->windows_lighting_ventilation = $house->windows_lighting_ventilation;
                $propiedad->observation_b_eleven = $house->observation_b_eleven;
                $propiedad->observation_b_one = $house->observation_b_one;
                $propiedad->observation_b_two = $house->observation_b_two;
                $propiedad->observation_b_three = $house->observation_b_three;
                $propiedad->observation_b_four = $house->observation_b_four;
                $propiedad->observation_b_five = $house->observation_b_five;
                $propiedad->observation_b_six = $house->observation_b_six;
                $propiedad->observation_b_seven = $house->observation_b_seven;
                $propiedad->observation_b_eight = $house->observation_b_eight;
                $propiedad->observation_b_nine = $house->observation_b_nine;
                $propiedad->observation_b_ten = $house->observation_b_ten;

                // SECCION C
                $propiedad->electricity_service = $house->electricity_service;
                $propiedad->damaged_wiring = $house->damaged_wiring;
                $propiedad->internal_lighting = $house->internal_lighting;
                $propiedad->water_service = $house->water_service;
                $propiedad->water_storage_system = $house->water_storage_system;
                $propiedad->toilet_condition = $house->toilet_condition;
                $propiedad->sink_condition = $house->sink_condition;
                $propiedad->shower_condition = $house->shower_condition;
                $propiedad->waste_disposal_system = $house->waste_disposal_system;
                $propiedad->observation_c_one = $house->observation_c_one;
                $propiedad->observation_c_two = $house->observation_c_two;
                $propiedad->observation_c_three = $house->observation_c_three;
                $propiedad->observation_c_four = $house->observation_c_four;
                $propiedad->observation_c_five = $house->observation_c_five;
                $propiedad->observation_c_six = $house->observation_c_six;
                $propiedad->observation_c_seven = $house->observation_c_seven;
                $propiedad->observation_c_eight = $house->observation_c_eight;
                $propiedad->observation_c_nine = $house->observation_c_nine;

                // SECCION D
                $propiedad->roof_condition = $house->roof_condition;
                $propiedad->walls_condition_rent = $house->walls_condition_rent;
                $propiedad->floor_condition_rent = $house->floor_condition_rent;
                $propiedad->windows_doors_condition = $house->windows_doors_condition;
                $propiedad->flood_exposure = $house->flood_exposure;
                $propiedad->earthquake_repairs = $house->earthquake_repairs;
                $propiedad->exposed_to_landslides = $house->exposed_to_landslides;
                $propiedad->settling_repairs = $house->settling_repairs;
                $propiedad->secure_entrance = $house->secure_entrance;
                $propiedad->clear_addresses = $house->clear_addresses;
                $propiedad->neighborhood_security = $house->neighborhood_security;
                $propiedad->observation_d_one = $house->observation_d_one;
                $propiedad->observation_d_two = $house->observation_d_two;
                $propiedad->observation_d_three = $house->observation_d_three;
                $propiedad->observation_d_four = $house->observation_d_four;
                $propiedad->observation_d_five = $house->observation_d_five;
                $propiedad->observation_d_six = $house->observation_d_six;
                $propiedad->observation_d_seven = $house->observation_d_seven;
                $propiedad->observation_d_eight = $house->observation_d_eight;
                $propiedad->observation_d_nine = $house->observation_d_nine;
                $propiedad->observation_d_ten = $house->observation_d_ten;
                $propiedad->observation_d_eleven = $house->observation_d_eleven;

                // SECCION E
                $propiedad->other_beneficiaries = $house->other_beneficiaries;
                $propiedad->community_relationship_perception = $house->community_relationship_perception;
                $propiedad->housing_accept_diverse_genders = $house->housing_accept_diverse_genders;
                $propiedad->housing_accept_other_nationalities = $house->housing_accept_other_nationalities;
                $propiedad->housing_accept_other_ethnicities = $house->housing_accept_other_ethnicities;
                $propiedad->housing_accept_pets = $house->housing_accept_pets;
                $propiedad->legal_owner_property = $house->legal_owner_property;
                $propiedad->observation_e_one = $house->observation_e_one;
                $propiedad->observation_e_two = $house->observation_e_two;
                $propiedad->observation_e_three = $house->observation_e_three;
                $propiedad->observation_e_four = $house->observation_e_four;
                $propiedad->observation_e_five = $house->observation_e_five;
                $propiedad->observation_e_six = $house->observation_e_six;
                $propiedad->observation_e_seven = $house->observation_e_seven;

                $this->coreModel->node_save($propiedad, [
                    "ignore_post_save" => false,
                    "ignore_pre_save" => false,
                    "dont_add_log" => true,
                    "justification" => "Update record"
                ]);
            }
        }
        $this->coreModel->node_save($rentInfo, ["ignore_post_save" => true, "ignore_pre_save" => true, "dont_add_log" => true, "justification" => "Update"]);
    }
}
