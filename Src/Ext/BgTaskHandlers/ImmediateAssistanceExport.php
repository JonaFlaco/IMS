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

class ImmediateAssistanceExport extends BgTaskHandlers
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
            $casesMe = $this->coreModel->nodeModel("immediate_assistance")
                ->where("m.id = :ids")
                ->bindValue("ids", $id)
                ->load();
            $casesSelected = array_merge($casesSelected, $casesMe);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Solicitantes');

        // Encabezados
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Asistencia Otorgada');
        $sheet->setCellValue('C1', 'Nombre Completo');
        $sheet->setCellValue('D1', 'Genero');
        $sheet->setCellValue('E1', 'National Number ID');
        $sheet->setCellValue('F1', 'Nacionalidad');
        $sheet->setCellValue('G1', 'Discapacidad');
        $sheet->setCellValue('H1', 'Oficina');
        $sheet->setCellValue('I1', 'Provincia');
        $sheet->setCellValue('J1', 'Canton');
        $sheet->setCellValue('K1', 'Total Kits');
        $sheet->setCellValue('L1', 'Tipo de Kit');
        $sheet->setCellValue('M1', 'Tipo de transporte');
        $sheet->setCellValue('N1', 'Dinámica de movilidad');
        $sheet->setCellValue('O1', 'Edad');
        $sheet->setCellValue('P1', 'ID');
        $sheet->setCellValue('Q1', 'Total canasta Familiar');
        $sheet->setCellValue('R1', 'Total higiene Familiar');
        $sheet->setCellValue('S1', 'Total Kit materno');
        $sheet->setCellValue('T1', 'Total Kit alimento individual');
        $sheet->setCellValue('U1', 'Total kit higiene femenino');
        $sheet->setCellValue('V1', 'Total kit higiene menstrual');
        $sheet->setCellValue('W1', 'Total kit emergencia sanitaria');
        $sheet->setCellValue('X1', 'Total kit viajero');
        $sheet->setCellValue('Y1', 'Total kit higiene masculino');
        $sheet->setCellValue('Z1', 'Total kit vestimenta');
        $sheet->setCellValue('AA1', 'Total kit escolar 1 a 8');
        $sheet->setCellValue('AB1', 'Total kit salud sexual reproductiva');
        $sheet->setCellValue('AC1', 'Total kit material didactico');
        $sheet->setCellValue('AD1', 'Total kit escolar 9 a 17');
        $sheet->setCellValue('AE1', 'Total otro');
        $sheet->setCellValue('AF1', 'Total kit bebé');
        $sheet->setCellValue('AG1', 'Total kit dignidad');
        $sheet->setCellValue('AH1', 'Total kit bolso viajero');
        $sheet->setCellValue('AI1', 'Total Transporte');
        $sheet->setCellValue('AJ1', 'Total Alojamiento');
        $sheet->setCellValue('AK1', 'Total Asistencia Legal');
        $sheet->setCellValue('AL1', 'Fecha');
        $sheet->setCellValue('AM1', 'Organización');
        $sheet->setCellValue('AN1', 'Usuario que lo registro');
        $sheet->setCellValue('AO1', 'Otro');

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
        $sheet->getStyle('A1:AO1')->applyFromArray($styleArray);

        foreach (range('A', 'AO') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $counter = 1;

        foreach ($casesSelected as $item) {
            $sheet->getRowDimension($row)->setRowHeight(20);

            $sheet->setCellValue('A' . $row, $counter);
            // Asignar nombre de la oficina en base a la provincia del case worker
            if (in_array($item->province, [24, 8, 7, 18, 2])) {
                $sheet->setCellValue('H' . $row, "C-AMOR");
            }
            if (in_array($item->province, [4])) {
                $sheet->setCellValue('H' . $row, "Huaquillas");
            }
            if (in_array($item->province, [5, 6, 13])) {
                $sheet->setCellValue('H' . $row, "Tulcán");
            }
            if (in_array($item->province, [11, 16, 20, 22])) {
                $sheet->setCellValue('H' . $row, "Lago Agrio");
            }
            if (in_array($item->province, [9, 10, 17])) {
                $sheet->setCellValue('H' . $row, "Guayaquil");
            }
            if (in_array($item->province, [15, 19])) {
                $sheet->setCellValue('H' . $row, "Manta");
            }
            if (!in_array($item->province, [24, 8, 7, 18, 2, 4, 5, 6, 13, 11, 16, 20, 22, 9, 10, 17, 15, 19])) {
                $sheet->setCellValue('H' . $row, $item->province_display);
            }

            $kits_sol = '';
            $transporte = 0;
            $alojamiento = 0;
            $asistenciaLegal = 0;
            foreach ($item->assistance_granted as $assistance_ind) {
                $kits_sol .= $assistance_ind->name . ', ';
                if ($assistance_ind->value == 1) {
                    $transporte = 1;
                }
                if ($assistance_ind->value == 2) {
                    $alojamiento = 1;
                }
                if ($assistance_ind->value == 5) {
                    $asistenciaLegal = 1;
                }
            }
            $kits_sol = rtrim($kits_sol, ', ');

            $sheet->setCellValue('B' . $row, $kits_sol);
            $sheet->setCellValue('C' . $row, $item->full_name);
            $sheet->setCellValue('D' . $row, $item->id_gender_display);
            $sheet->setCellValue('E' . $row, $item->national_id_no);
            $sheet->setCellValue('F' . $row, $item->id_nationality_display);
            $sheet->setCellValue('J' . $row, $item->canton_id_display);
            $sheet->setCellValue('I' . $row, $item->province_display);
            $sheet->setCellValue('AN' . $row, $item->created_user_id_display);
            $sheet->setCellValue('AO' . $row, $item->other_organization);

            $kits = '';
            foreach ($item->type_kit as $kit_ind) {
                $kits .= $kit_ind->name . ', ';
            }
            $kits = rtrim($kits, ', ');

            $sheet->setCellValue('L' . $row, $kits);
            $sheet->setCellValue('M' . $row, $item->tranport_display);
            $sheet->setCellValue('N' . $row, $item->mobility_dynamics_display);
            $sheet->setCellValue('O' . $row, $item->age);
            $sheet->setCellValue('P' . $row, $item->id);

            // if ($item->final_destination)
            //     $sheet->setCellValue('J' . $row, $item->final_destination);

            if ($item->is_disability == 1)
                $sheet->setCellValue('G' . $row, "Si");
            if ($item->is_disability == 0)
                $sheet->setCellValue('G' . $row, "No");

            //Suma Variables
            $total_canasta_fam = 0;
            $total_higiene_fam = 0;
            $total_kit_materno = 0;
            $total_kit_alimento_individual = 0;
            $total_kit_higiene_femenino = 0;
            $total_kit_higiene_menstrual = 0;
            $total_kit_emergencia_sanitaria = 0;
            $total_kit_viajero = 0;
            $total_kit_higiene_masculino = 0;
            $total_kit_vestimenta = 0;
            $total_kit_escolar_1_8 = 0;
            $total_kit_salud_sexual_reproductiva = 0;
            $total_kit_material_didactico = 0;
            $total_kit_escolar_9_17 = 0;
            $total_otro = 0;
            $total_kit_bebe = 0;
            $total_kit_dignidad = 0;
            $total_kit_bolso_viajero = 0;

            //Suma kit canasta familiar
            if ($item->num_alimentos_fam == 2)
                $total_canasta_fam += 1;

            if ($item->num_alimentos_fam == 3)
                $total_canasta_fam += 2;

            if ($item->num_alimentos_fam == 4)
                $total_canasta_fam += 3;

            $sheet->setCellValue('Q' . $row, $total_canasta_fam);


            //Suma kits de higiene familiar
            if ($item->num_higiene_fam == 2)
                $total_higiene_fam += 1;

            if ($item->num_higiene_fam == 3)
                $total_higiene_fam += 2;

            if ($item->num_higiene_fam == 4)
                $total_higiene_fam += 3;

            $sheet->setCellValue('R' . $row, $total_higiene_fam);

            //Conteo de aplicante principal
            foreach ($item->type_kit as $kit) {
                if ($kit->value == 1)
                    $total_kit_alimento_individual += 1;

                if ($kit->value == 2)
                    $total_kit_materno += 1;

                if ($kit->value == 4)
                    $total_kit_higiene_femenino += 1;

                if ($kit->value == 5)
                    $total_kit_higiene_menstrual += 1;

                if ($kit->value == 6)
                    $total_kit_emergencia_sanitaria += 1;

                if ($kit->value == 7)
                    $total_kit_viajero += 1;

                if ($kit->value == 8)
                    $total_kit_higiene_masculino += 1;

                if ($kit->value == 9)
                    $total_kit_vestimenta += 1;

                if ($kit->value == 10)
                    $total_kit_escolar_1_8 += 1;

                if ($kit->value == 11)
                    $total_kit_salud_sexual_reproductiva += 1;

                if ($kit->value == 12)
                    $total_kit_material_didactico += 1;

                if ($kit->value == 14)
                    $total_kit_escolar_9_17 += 1;

                if ($kit->value == 15)
                    $total_otro += 1;

                if ($kit->value == 16)
                    $total_kit_bebe += 1;

                if ($kit->value == 17)
                    $total_kit_dignidad += 1;

                if ($kit->value == 18)
                    $total_kit_bolso_viajero += 1;
            }

            //Conteo de kits de familiares
            foreach ($item->family_members as $family) {
                foreach ($family->fam_kit_type as $familyKits) {
                    if ($familyKits->value == 1)
                        $total_kit_alimento_individual += 1;

                    if ($familyKits->value == 2)
                        $total_kit_materno += 1;

                    if ($familyKits->value == 4)
                        $total_kit_higiene_femenino += 1;

                    if ($familyKits->value == 5)
                        $total_kit_higiene_menstrual += 1;

                    if ($familyKits->value == 6)
                        $total_kit_emergencia_sanitaria += 1;

                    if ($familyKits->value == 7)
                        $total_kit_viajero += 1;

                    if ($familyKits->value == 8)
                        $total_kit_higiene_masculino += 1;

                    if ($familyKits->value == 9)
                        $total_kit_vestimenta += 1;

                    if ($familyKits->value == 10)
                        $total_kit_escolar_1_8 += 1;

                    if ($familyKits->value == 11)
                        $total_kit_salud_sexual_reproductiva += 1;

                    if ($familyKits->value == 12)
                        $total_kit_material_didactico += 1;

                    if ($familyKits->value == 14)
                        $total_kit_escolar_9_17 += 1;

                    if ($familyKits->value == 15)
                        $total_otro += 1;

                    if ($familyKits->value == 16)
                        $total_kit_bebe += 1;

                    if ($familyKits->value == 17)
                        $total_kit_dignidad += 1;

                    if ($familyKits->value == 18)
                        $total_kit_bolso_viajero += 1;
                }
            }

            $sheet->setCellValue('S' . $row, $total_kit_materno);
            $sheet->setCellValue('T' . $row, $total_kit_alimento_individual);
            $sheet->setCellValue('U' . $row, $total_kit_higiene_femenino);
            $sheet->setCellValue('V' . $row, $total_kit_higiene_menstrual);
            $sheet->setCellValue('W' . $row, $total_kit_emergencia_sanitaria);
            $sheet->setCellValue('X' . $row, $total_kit_viajero);
            $sheet->setCellValue('Y' . $row, $total_kit_higiene_masculino);
            $sheet->setCellValue('Z' . $row, $total_kit_vestimenta);
            $sheet->setCellValue('AA' . $row, $total_kit_escolar_1_8);
            $sheet->setCellValue('AB' . $row, $total_kit_salud_sexual_reproductiva);
            $sheet->setCellValue('AC' . $row, $total_kit_material_didactico);
            $sheet->setCellValue('AD' . $row, $total_kit_escolar_9_17);
            $sheet->setCellValue('AE' . $row, $total_otro);
            $sheet->setCellValue('AF' . $row, $total_kit_bebe);
            $sheet->setCellValue('AG' . $row, $total_kit_dignidad);
            $sheet->setCellValue('AH' . $row, $total_kit_bolso_viajero);
            $sheet->setCellValue('AI' . $row, $transporte);
            $sheet->setCellValue('AJ' . $row, $alojamiento);
            $sheet->setCellValue('AK' . $row, $asistenciaLegal);

            $sheet->setCellValue('AL' . $row, $item->created_date);
            $sheet->setCellValue('AM' . $row, $item->organization_id_display);

            $totalKits = $total_canasta_fam + $total_higiene_fam + $total_kit_materno + $total_kit_alimento_individual + $total_kit_higiene_femenino + $total_kit_higiene_menstrual + $total_kit_emergencia_sanitaria + $total_kit_viajero + $total_kit_higiene_masculino + $total_kit_vestimenta + $total_kit_escolar_1_8 + $total_kit_salud_sexual_reproductiva + $total_kit_material_didactico + $total_kit_escolar_9_17 + $total_otro + $total_kit_bebe + $total_kit_dignidad + $total_kit_bolso_viajero;
            $sheet->setCellValue('K' . $row, $totalKits);

            $row++;
            $counter++;
        }

        // SEGUNDA TABLA
        $sheetTwo = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle('Familiares');

        $sheetTwo->setCellValue('A1', '#');
        $sheetTwo->setCellValue('B1', 'Edad');
        $sheetTwo->setCellValue('C1', 'Género miembro grupo familiar');
        $sheetTwo->setCellValue('D1', 'Nacionalidad miembro grupo familiar');
        $sheetTwo->setCellValue('E1', 'Tiene alguna discapacidad?');
        $sheetTwo->setCellValue('F1', 'Tipo de asistencia (miembro grupo familiar)');
        $sheetTwo->setCellValue('G1', 'Tipo de Kit (miembro grupo familiar)');
        $sheetTwo->setCellValue('H1', 'Tipo de transporte');
        $sheetTwo->setCellValue('I1', 'Dinámica de movilidad');
        $sheetTwo->setCellValue('J1', 'ID');
        $sheetTwo->setCellValue('K1', 'Fecha');

        $sheetTwo->getStyle('A1:K1')->applyFromArray($styleArray);
        foreach (range('A', 'K') as $columnID) {
            $sheetTwo->getColumnDimension($columnID)->setAutoSize(true);
        }

        $rowTwo = 2;
        $counterTwo = 1;

        foreach ($casesSelected  as $case) {
            foreach ($case->family_members as $family) {
                $sheetTwo->setCellValue('A' . $rowTwo, $counterTwo);
                $sheetTwo->setCellValue('B' . $rowTwo, $family->age_fam);
                $sheetTwo->setCellValue('C' . $rowTwo, $family->fam_gender_id_display);
                $sheetTwo->setCellValue('D' . $rowTwo, $family->fam_nationality_id_display);
                $sheetTwo->setCellValue('E' . $rowTwo, $family->fam_disability_display);
                $sheetTwo->setCellValue('F' . $rowTwo, $family->fam_assist_type_display);
                if ($family->fam_assist_type == 2)
                    $sheetTwo->setCellValue('F' . $rowTwo, "Entregado al solicitante:  $case->assistance_granted_display");

                $kits_fam = '';
                foreach ($family->fam_kit_type as $kit_ind_fam) {
                    $kits_fam .= $kit_ind_fam->name . ', ';
                }
                $kits_fam = rtrim($kits_fam, ', ');

                $sheetTwo->setCellValue('G' . $rowTwo, $kits_fam);
                $sheetTwo->setCellValue('H' . $rowTwo, $case->tranport_display);
                $sheetTwo->setCellValue('I' . $rowTwo, $case->mobility_dynamics_display);
                $sheetTwo->setCellValue('J' . $rowTwo, $family->parent_id);
                $sheetTwo->setCellValue('K' . $rowTwo, $case->created_date);

                $rowTwo++;
                $counterTwo++;
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks"  . DS . 'Reporte Asistencias Inmediatas' .  time() . '.xlsx';
        $writer->save($filename);

        return  $filename;
    }
}
