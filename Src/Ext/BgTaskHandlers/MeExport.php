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

class MeExport extends BgTaskHandlers
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
        $casesSelected = [];
        foreach ($ids_array as $id) {
            //no carga los servicios que fueron importados
            $casesMe = $this->coreModel->nodeModel("b_services")
                ->where("m.id = :ids and m.created_user_id IS NOT NULL and m.created_user_id <> 21468")
                ->bindValue("ids", $id)
                ->load();
            $casesSelected = array_merge($casesSelected, $casesMe);
        }

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Solicitantes');

        // Encabezados
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Oficina');
        $sheet->setCellValue('C1', 'Fecha');
        $sheet->setCellValue('D1', 'WBS');
        $sheet->setCellValue('E1', 'Tipo de asistencia');
        $sheet->setCellValue('F1', 'Sexo');
        $sheet->setCellValue('G1', 'Genero');
        $sheet->setCellValue('H1', 'Edad');
        $sheet->setCellValue('I1', 'Nacionalidad');
        $sheet->setCellValue('J1', 'Discapacidad');
        $sheet->setCellValue('K1', 'Provincia');
        $sheet->setCellValue('L1', 'Cantón');
        $sheet->setCellValue('M1', 'Grupal');
        $sheet->setCellValue('N1', 'Monto asignado');
        $sheet->setCellValue('O1', 'Código de beneficiario');
        $sheet->setCellValue('P1', 'Estado de la asistencia');
        $sheet->setCellValue('Q1', 'Movilidad');
        $sheet->setCellValue('R1', 'Teléfono');

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
        $sheet->getStyle('A1:R1')->applyFromArray($styleArray);

        foreach (range('A', 'R') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $counter = 1;
        foreach ($casesSelected as $item) {
            // Cargar datos del beneficiario
            $bnf_id = $item->bnf_id;
            $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                ->where("m.id = :bnf_id")
                ->bindValue("bnf_id", $bnf_id)
                ->loadFirstOrFail();

            // Cargar evaluación del caso
            $evaluation = $this->coreModel->nodeModel("evaluation")
                ->where("m.beneficiary_id  = :id")
                ->bindValue("id", $bnf_id)
                ->load();

            $sheet->getRowDimension($row)->setRowHeight(20);

            $sheet->setCellValue('A' . $row, $counter);
            // Asignar nombre de la oficina en base a la provincia del case worker
            if (in_array($item->province_id, [24, 8, 7, 18, 2])) {
                $sheet->setCellValue('B' . $row, "C-AMOR");
            }
            if (in_array($item->province_id, [4])) {
                $sheet->setCellValue('B' . $row, "Huaquillas");
            }
            if (in_array($item->province_id, [5, 6, 13])) {
                $sheet->setCellValue('B' . $row, "Tulcán");
            }
            if (in_array($item->province_id, [11, 16, 20, 22])) {
                $sheet->setCellValue('B' . $row, "Lago Agrio");
            }
            if (in_array($item->province_id, [9, 10, 17])) {
                $sheet->setCellValue('B' . $row, "Guayaquil");
            }
            if (in_array($item->province_id, [15, 19])) {
                $sheet->setCellValue('B' . $row, "Manta");
            }
            if (!in_array($item->province_id, [24, 8, 7, 18, 2, 4, 5, 6, 13, 11, 16, 20, 22, 9, 10, 17, 15, 19])) {
                $sheet->setCellValue('B' . $row, $item->province_id_display);
            }
            $start_date   = isset($item->start_date) ? (new DateTime($item->start_date))->format('Y-m-d') : "N/A";
            $sheet->setCellValue('C' . $row, $start_date);
            $sheet->setCellValue('D' . $row, $item->main_wbs_id_display);

            if ($item->sub_service_display)
                $sheet->setCellValue('E' . $row, $item->sub_service_display);

            if ($item->health_referral_new_display)
                $sheet->setCellValue('E' . $row, $item->health_referral_new_display);

            if ($evaluation) {
                $sheet->setCellValue('F' . $row, $evaluation[0]->sex_id_display);
                $sheet->setCellValue('J' . $row, $evaluation[0]->is_disability_display);
            }
            $sheet->setCellValue('G' . $row, $beneficiary->gender_id_display);
            // Calcular edad del ap
            $current_date = new DateTime();
            $birth_date = new DateTime($beneficiary->birth_date);
            $age = $current_date->diff($birth_date)->y;
            $sheet->setCellValue('H' . $row, $age);
            $sheet->setCellValue('I' . $row, $beneficiary->nationality_id_display);

            $sheet->setCellValue('K' . $row, $item->province_id_display);

            if ($beneficiary->canton)
                $sheet->setCellValue('L' . $row, $beneficiary->canton_display);

            if ($item->represent_aplicant == 0 && $item->family_represent) {
                $sheet->setCellValue('M' . $row, "ACOMPAÑANTE");
            } else {
                $sheet->setCellValue('M' . $row, "JEFE DE HOGAR");
            }

            $sheet->setCellValue('N' . $row, $item->bnf_amount);
            $sheet->setCellValue('O' . $row, $beneficiary->code);

            $status_display = null;
            if ($item->status_id == 2)
                $status_display = "Aprobado";
            if ($item->status_id == 3)
                $status_display = "Rechazado";
            if ($item->status_id == 95)
                $status_display = "Pendiente";
            if ($item->status_id == 91)
                $status_display = "Cerrado";

            $sheet->setCellValue('P' . $row, $status_display);


            if (!empty($evaluation) && isset($evaluation[0])) {

                if ($evaluation[0]->first_arrival) {
                    $first_arrival = $evaluation[0]->first_arrival;
                    $arrival_date = new DateTime($first_arrival);
                    $diff = $current_date->diff($arrival_date);
                    $arrival_months = ($diff->y * 12) + $diff->m;

                    if ($arrival_months > 3)
                        $movilidad = 'Permanencia';

                    if ($arrival_months <= 3)
                        $movilidad = 'Transito';

                    if ($beneficiary->nationality_id == 1)
                        $movilidad = 'Comunidad de acogida';


                    $sheet->setCellValue('Q' . $row, $movilidad);
                }
            } else {
                $sheet->setCellValue('Q' . $row, 'Sin evaluación');
            }
            if (!empty($evaluation) && !$evaluation[0]->first_arrival)
                $sheet->setCellValue('Q' . $row, 'Permanencia');

            $sheet->setCellValue('R' . $row, $beneficiary->phone_number);

            $row++;
            $counter++;
        }

        //tab 2
        $sheetTwo = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle('Familiares');

        // Encabezados
        $sheetTwo->setCellValue('A1', '#');
        $sheetTwo->setCellValue('B1', 'Tipo de asistencia');
        $sheetTwo->setCellValue('C1', 'Sexo');
        $sheetTwo->setCellValue('D1', 'Genero');
        $sheetTwo->setCellValue('E1', 'Edad');
        $sheetTwo->setCellValue('F1', 'Nacionalidad');
        $sheetTwo->setCellValue('G1', 'Discapacidad');
        $sheetTwo->setCellValue('H1', 'Grupal');
        $sheetTwo->setCellValue('I1', 'Código de beneficiario');
        $sheetTwo->setCellValue('J1', 'Oficina');
        $sheetTwo->setCellValue('K1', 'Provincia');
        $sheetTwo->setCellValue('L1', 'Canton');
        $sheetTwo->setCellValue('M1', 'Teléfono');
        $sheetTwo->setCellValue('N1', 'Fecha');

        $sheetTwo->getStyle('A1:N1')->applyFromArray($styleArray);

        foreach (range('A', 'N') as $columnID) {
            $sheetTwo->getColumnDimension($columnID)->setAutoSize(true);
        }

        $rowTwo = 2;
        $counterTwo = 1;
        foreach ($casesSelected as $itemTwo) {
            $bnf_id_two = $itemTwo->bnf_id;
            $beneficiary_two = $this->coreModel->nodeModel("beneficiaries")
                ->where("m.id = :bnf_id")
                ->bindValue("bnf_id", $bnf_id_two)
                ->loadFirstOrFail();
            // Cargar miembros asistidos por cada servicio en la lista
            $members_assisteded = $this->coreModel->nodeModel("b_services_members_assisted")
                ->fields(['family_member'])
                ->where("m.parent_id = :id")
                ->bindValue("id", $itemTwo->id)
                ->load();
            // Cargar datos de los familiares
            foreach ($members_assisteded as $famId) {

                if ($famId->family_member) {
                    $member = $this->coreModel->nodeModel("beneficiaries_family_information")
                        ->where("m.id = :id")
                        ->bindValue("id", $famId->family_member)
                        ->loadFirst();
                    $sheetTwo->getRowDimension($rowTwo)->setRowHeight(20);

                    $sheetTwo->setCellValue('A' . $rowTwo, $counterTwo);

                    if ($itemTwo->sub_service_display)
                        $sheetTwo->setCellValue('B' . $rowTwo, $itemTwo->sub_service_display);

                    if ($itemTwo->health_referral_new_display)
                        $sheetTwo->setCellValue('B' . $rowTwo, $itemTwo->health_referral_new_display);

                    $evaluations = $this->coreModel->nodeModel("evaluation")
                        ->where("m.beneficiary_id  = :id")
                        ->bindValue("id", $member->parent_id)
                        ->load();

                    $sexDisplay = null;
                    $disabilityDisplay = null;
                    if ($evaluations) {
                        foreach ($evaluations as $eval) {
                            foreach ($eval->family_sex as $family) {
                                if ($family->family_member == $famId->family_member) {
                                    $sexDisplay = $family->family_member_sex_display;
                                }
                            }
                            foreach ($eval->family_health as $familyH) {
                                if ($familyH->family_member == $famId->family_member) {
                                    $disabilityDisplay = $familyH->family_disability_display;
                                }
                            }
                            if ($sexDisplay)
                                $sheetTwo->setCellValue('C' . $rowTwo, $sexDisplay);

                            if ($disabilityDisplay)
                                $sheetTwo->setCellValue('G' . $rowTwo, $disabilityDisplay);
                        }
                    }

                    $sheetTwo->setCellValue('D' . $rowTwo, $member->gender_id_display);

                    // Calcular edad de cada familiar
                    $currentDate = new DateTime();
                    $famBirthate = new DateTime($member->birthdate);
                    $famAge = $currentDate->diff($famBirthate)->y;
                    $sheetTwo->setCellValue('E' . $rowTwo, $famAge);

                    $sheetTwo->setCellValue('F' . $rowTwo, $member->nationality_display);

                    if ($itemTwo->represent_aplicant == 0 && $itemTwo->family_represent == $famId->family_member) {
                        $sheetTwo->setCellValue('H' . $rowTwo, "JEFE DE HOGAR");
                    } else {
                        $sheetTwo->setCellValue('H' . $rowTwo, "ACOMPAÑANTE");
                    }

                    $bnfCode = $this->coreModel->nodeModel("beneficiaries")
                        ->fields(['code'])
                        ->where("m.id = :bnf_id")
                        ->bindValue("bnf_id", $member->parent_id)
                        ->loadFirstOrFail();
                    $sheetTwo->setCellValue('I' . $rowTwo, $bnfCode->code);

                    if (in_array($itemTwo->province_id, [24, 8, 7, 18, 2])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "C-AMOR");
                    }
                    if (in_array($itemTwo->province_id, [4])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "Huaquillas");
                    }
                    if (in_array($itemTwo->province_id, [5, 6, 13])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "Tulcán");
                    }
                    if (in_array($itemTwo->province_id, [11, 16, 20, 22])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "Lago Agrio");
                    }
                    if (in_array($itemTwo->province_id, [9, 10, 17])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "Guayaquil");
                    }
                    if (in_array($itemTwo->province_id, [15, 19])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, "Manta");
                    }
                    if (!in_array($itemTwo->province_id, [24, 8, 7, 18, 2, 4, 5, 6, 13, 11, 16, 20, 22, 9, 10, 17, 15, 19])) {
                        $sheetTwo->setCellValue('J' . $rowTwo, $itemTwo->province_id_display);
                    }

                    $sheetTwo->setCellValue('K' . $rowTwo, $itemTwo->province_id_display);

                    if ($beneficiary_two->canton)
                        $sheetTwo->setCellValue('L' . $rowTwo, $beneficiary_two->canton_display);

                    $sheetTwo->setCellValue('M' . $rowTwo, $beneficiary_two->phone_number);
                    $start_date_fam   = isset($itemTwo->start_date) ? (new DateTime($itemTwo->start_date))->format('Y-m-d') : "N/A";
                    $sheetTwo->setCellValue('N' . $rowTwo, $start_date_fam);


                    $rowTwo++;
                    $counterTwo++;
                }
            }
        }
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks"  . DS . 'Reporte MYE' .  time() . '.xlsx';
        $writer->save($filename);

        return  $filename;
    }
}
