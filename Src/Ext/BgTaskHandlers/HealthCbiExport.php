<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;
use DateTime;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Exceptions\IlegalUserActionException;

class HealthCbiExport extends BgTaskHandlers
{

    private \App\Core\BgTask $task;

    public function __construct($task)
    {
        parent::__construct();

        $this->task = $task;
    }


    public function run()
    {
        $_POST = $this->task->getPostData();
        $ids_array = [];
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $ids_array = _explode(',', $id);
        } else {

            throw new \App\Exceptions\MissingDataFromRequesterException("ID is required, but not provided");
        }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $filename = $this->GenerateOneReport($ids_array, $spreadsheet);

        return $filename;
    }
    public function afterCompletion() {}

    public function GenerateOneReport($ids_array, $spreadsheet)
    {
        $allCasesSalud = [];
        foreach ($ids_array as $id) {
            $casesSalud = $this->coreModel->nodeModel("b_services")
                ->where("m.id = :ids and m.health_referral_new = :Salud")
                ->bindValue("ids", $id)
                ->bindValue("Salud", 1)
                ->load();
            $allCasesSalud = array_merge($allCasesSalud, $casesSalud);
        }

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('data');

        // Encabezados
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Fecha');
        $sheet->setCellValue('C1', 'Código de beneficiario');
        $sheet->setCellValue('D1', 'Casos oficina');
        $sheet->setCellValue('E1', 'Staff responsable');
        $sheet->setCellValue('F1', 'Tipo de identificación');
        $sheet->setCellValue('G1', 'No de identificación');
        $sheet->setCellValue('H1', 'Nombres completos');
        $sheet->setCellValue('I1', 'Celular');
        $sheet->setCellValue('J1', 'Envío de SMS');
        $sheet->setCellValue('K1', 'Monto asignado');
        $sheet->setCellValue('L1', 'Adultos masculinos');
        $sheet->setCellValue('M1', 'Adultos femeninos');
        $sheet->setCellValue('N1', 'NNA masculinos');
        $sheet->setCellValue('O1', 'NNA Femeninos');
        $sheet->setCellValue('P1', 'Servicio');

        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '0033A0']
            ]
        ];
        $sheet->getStyle('A1:P1')->applyFromArray($styleArray);

        foreach (range('A', 'P') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $counter = 1;
        foreach ($allCasesSalud as $item) {
            // Cargar datos del beneficiario
            $bnf_id = $item->bnf_id;
            $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                ->where("m.id = :bnf_id")
                ->bindValue("bnf_id", $bnf_id)
                ->loadFirstOrFail();

            $family_members = $this->coreModel->nodeModel("beneficiaries_family_information")
                ->where("m.parent_id = :id")
                ->bindValue("id", $beneficiary->id)
                ->load();

            // Cargar miembros asistidos por cada servicio en la lista
            $members_assisted = $this->coreModel->nodeModel("b_services_members_assisted")
                ->fields(['family_member','beneficiaries_id'])
                ->where("m.parent_id = :id")
                ->bindValue("id", $item->id)
                ->load();

            // Crear un array con los IDs de los miembros asistidos
            $assisted_ids = array_map(function ($ma) {
                return $ma->family_member;
            }, $members_assisted);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $load_date = isset($item->load_date) ? (new DateTime($item->load_date))->format('Y-m-d') : "N/A";

            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, $load_date);
            $sheet->setCellValue('C' . $row, $beneficiary->code);
            // Asignar nombre de la oficina en base a la provincia del case worker
            if (in_array($item->province_id, [24, 8, 7, 18, 2])) {
                $sheet->setCellValue('D' . $row, "C-AMOR");
            }
            if (in_array($item->province_id, [4])) {
                $sheet->setCellValue('D' . $row, "Huaquillas");
            }
            if (in_array($item->province_id, [5, 6, 13])) {
                $sheet->setCellValue('D' . $row, "Tulcán");
            }
            if (in_array($item->province_id, [11, 16, 20, 22])) {
                $sheet->setCellValue('D' . $row, "Lago Agrio");
            }
            if (in_array($item->province_id, [9, 10, 17])) {
                $sheet->setCellValue('D' . $row, "Guayaquil");
            }
            if (in_array($item->province_id, [15, 19])) {
                $sheet->setCellValue('D' . $row, "Manta");
            }
            if (!in_array($item->province_id, [24, 8, 7, 18, 2, 4, 5, 6, 13, 11, 16, 20, 22, 9, 10, 17, 15, 19])) {
                $sheet->setCellValue('D' . $row, $item->province_id_display);
            }

            $sheet->setCellValue('E' . $row, isset($beneficiary->case_worker) ? $beneficiary->case_worker_display : "N/A");
            $sheet->setCellValue('F' . $row, "Cedula");
            if ($item->represent_aplicant == 0) {
                $id_fam = $item->family_represent;
                try {
                    $represent_member = $this->coreModel->nodeModel("beneficiaries_family_information")
                        ->fields(['full_name', 'family_national_id'])
                        ->where("m.id = :id_fam")
                        ->bindValue("id_fam", $id_fam)
                        ->loadFirstOrFail();

                    $sheet->setCellValue('G' . $row, $represent_member->family_national_id);
                    $sheet->setCellValue('H' . $row, $represent_member->full_name);
                } catch (\Exception $e) {
                    $sheet->setCellValue('G' . $row, "N/A");
                    $sheet->setCellValue('H' . $row, "N/A");
                }
            } else {
                $sheet->setCellValue('G' . $row, $beneficiary->national_id_no);
                $sheet->setCellValue('H' . $row, $beneficiary->full_name);
            }
            $sheet->setCellValue('I' . $row, $beneficiary->phone_number);
            $sheet->setCellValue('J' . $row, isset($item->sms_validate) ? ($item->sms_validate == 1 ? "SI" : "NO") : "N/A");
            $sheet->setCellValue('K' . $row, $item->bnf_amount);

            // Variables para contar adultos y NNA por género
            $male_adults = 0;
            $female_adults = 0;
            $male_nna = 0;
            $female_nna = 0;

            // Calcular edad y categorizar al titular
            $current_date = new DateTime();

            $assisted_bnf_ids = array_map(function ($be) {
                return $be->beneficiaries_id;
            }, $members_assisted);
            if (in_array($beneficiary->id, $assisted_bnf_ids)) {
                $birth_date = new DateTime($beneficiary->birth_date);
                $age = $current_date->diff($birth_date)->y;
                $gender = $beneficiary->gender_id == 1 ? 'M' : ($beneficiary->gender_id == 2 ? 'F' : '');

                if ($age >= 18) {
                    if ($gender == 'M') {
                        $male_adults++;
                    } elseif ($gender == 'F') {
                        $female_adults++;
                    }
                } else {
                    if ($gender == 'M') {
                        $male_nna++;
                    } elseif ($gender == 'F') {
                        $female_nna++;
                    }
                }
            }

            // Procesar cada miembro de la familia
            foreach ($family_members as $member) {
                // Verificar si el miembro está en la lista de miembros asistidos
                if (!in_array($member->id, $assisted_ids)) {
                    continue;
                }

                $member_birth_date = new DateTime($member->birthdate);
                $member_age = $current_date->diff($member_birth_date)->y;
                $member_gender = $member->gender_id == 1 ? 'M' : ($member->gender_id == 2 ? 'F' : '');

                if ($member_age >= 18) {
                    if ($member_gender == 'M') {
                        $male_adults++;
                    } elseif ($member_gender == 'F') {
                        $female_adults++;
                    }
                } else {
                    if ($member_gender == 'M') {
                        $male_nna++;
                    } elseif ($member_gender == 'F') {
                        $female_nna++;
                    }
                }
            }

            $sheet->setCellValue('L' . $row, $male_adults);
            $sheet->setCellValue('M' . $row, $female_adults);
            $sheet->setCellValue('N' . $row, $male_nna);
            $sheet->setCellValue('O' . $row, $female_nna);
            $sheet->setCellValue('P' . $row, $item->health_referral_new_display);

            $row++;
            $counter++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks"  . DS . 'Reporte CBI Salud' .  time() . '.xlsx';
        $writer->save($filename);

        return  $filename;
    }
}
