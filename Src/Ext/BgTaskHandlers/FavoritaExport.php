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

class FavoritaExport extends BgTaskHandlers
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

            throw new \App\Exceptions\MissingDataFromRequesterException("ID is required , but not provided");
        }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $filename = $this->GenerateOneReport($ids_array,  $spreadsheet);

        return $filename;
    }
    public function afterCompletion()
    {
    }

    public function GenerateOneReport($ids_array, $spreadsheet)
    {
        $allCasesFavorita = [];
        foreach ($ids_array as $id) {
            $casesFavorita = $this->coreModel->nodeModel("b_services")
                ->where("m.id = :ids and m.sub_service = :favorita")
                ->bindValue("ids", $id)
                ->bindValue("favorita", 25)
                ->load();
            $allCasesFavorita = array_merge($allCasesFavorita, $casesFavorita);
        }

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('data');

        //Encabezados
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'ACCION');
        $sheet->setCellValue('C1', 'TITULAR / ADICIONAL');
        $sheet->setCellValue('D1', 'CEDULA DEL TITULAR CUANDO ES ADICIONAL');
        $sheet->setCellValue('E1', 'TIPO IDENTIFICACION');
        $sheet->setCellValue('F1', 'CEDULA / PASAPORTE');
        $sheet->setCellValue('G1', 'CODIGO DE EMPLEADO');
        $sheet->setCellValue('H1', 'Primer Apellido');
        $sheet->setCellValue('I1', 'Segundo Apellido');
        $sheet->setCellValue('J1', 'Primer Nombre');
        $sheet->setCellValue('K1', 'Segundo Nombre');
        $sheet->setCellValue('L1', 'FECHA DE NACIMIENTO');
        $sheet->setCellValue('M1', 'GENERO');
        $sheet->setCellValue('N1', 'ACTIVIDAD');
        $sheet->setCellValue('O1', 'DIRECCIÓN DOMICILIO');
        $sheet->setCellValue('P1', 'PROVINCIA');
        $sheet->setCellValue('Q1', 'CIUDAD');
        $sheet->setCellValue('R1', 'TELÉFONO CONVENCIONAL');
        $sheet->setCellValue('S1', 'TELÉFONO CELULAR');
        $sheet->setCellValue('T1', 'CORREO ELECTRÓNICO');
        $sheet->setCellValue('U1', 'ESTADO CIVIL');
        $sheet->setCellValue('V1', 'CEDULA CONYUGE');
        $sheet->setCellValue('W1', 'NOMBRE CONYUGE');
        $sheet->setCellValue('X1', 'SUCURSAL ENTREGA TARJETA');
        $sheet->setCellValue('Y1', 'CUPO CORRIENTE');
        $sheet->setCellValue('Z1', 'GESTOR ASIGNADO'); //STAFF RESPONSABLE
        $sheet->setCellValue('AA1', 'CODIGO DE CASO');
        $sheet->setCellValue('AB1', 'N° PERSONAS ADULTAS (H)');
        $sheet->setCellValue('AC1', 'N° PERSONAS ADULTAS (M)');
        $sheet->setCellValue('AD1', 'N° NNA (H)');
        $sheet->setCellValue('AE1', 'N° NNA (M)');


        $styleArrayBlue = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '0033A0']
            ]
        ];
        $styleArrayGray = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '787A7D']
            ]
        ];
        $sheet->getStyle('A1:V1')->applyFromArray($styleArrayBlue);
        $sheet->getStyle('W1:AE1')->applyFromArray($styleArrayGray);


        foreach (range('A', 'Z') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $counter = 1;
        foreach ($allCasesFavorita as $item) {
            //carga datos del beneficiario
            $bnf_id = $item->bnf_id;
            $beneficiary = $this->coreModel->nodeModel("beneficiaries")
                ->where("m.id = :bnf_id")
                ->bindValue("bnf_id", $bnf_id)
                ->loadFirstOrFail();

            $family_members = $this->coreModel->nodeModel("beneficiaries_family_information")
                ->where("m.parent_id = :id")
                ->bindValue("id", $beneficiary->id)
                ->load();
            
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->setCellValue('A' . $row, $counter);

            $sheet->setCellValue('B' . $row, "N");
            $sheet->setCellValue('C' . $row, "T");
            $sheet->setCellValue('E' . $row, "P");

            if ($item->physical_document == 1) {
                $sheet->setCellValue('F' . $row, $beneficiary->passport_no);
            } else if ($item->physical_document == 5) {
                $sheet->setCellValue('F' . $row, $beneficiary->national_id_no);
            } else {
                $sheet->setCellValue('F' . $row, $beneficiary->national_id_no);
            }

            $sheet->setCellValue('H' . $row, $item->first_last_name);
            $sheet->setCellValue('I' . $row, $item->second_last_name);
            $sheet->setCellValue('J' . $row, $item->first_name);
            $sheet->setCellValue('K' . $row, $item->second_name);
            $birth_date = (new DateTime($beneficiary->birth_date))->format('Y-m-d') ;
            $sheet->setCellValue('L' . $row, $birth_date);
            if ($beneficiary->gender_id == 1) {
                $gender = 'M';
            } elseif ($beneficiary->gender_id == 2) {
                $gender = 'F';
            } else {
                $gender = ''; // Valor vacío por defecto si no es 1 ni 2
            }

            $sheet->setCellValue('M' . $row, $gender);
            $sheet->setCellValue('N' . $row, "EMPLEADO");
            $sheet->setCellValue('O' . $row, "JULIO ALARCON Y ALFONSO PEREIRA");
            $sheet->setCellValue('P' . $row, "PICHINCHA");
            $sheet->setCellValue('Q' . $row, "QUITO");
            $sheet->setCellValue('R' . $row, "022123456");
            $sheet->setCellValue('U' . $row, "S");
            $sheet->setCellValue('Y' . $row, $item->bnf_amount);
            $sheet->setCellValue('Z' . $row, $beneficiary->case_worker_display);
            $sheet->setCellValue('AA' . $row, $beneficiary->code);

            // Variables para contar adultos y NNA por género
            $male_adults = 0;
            $female_adults = 0;
            $male_nna = 0;
            $female_nna = 0;

            // Calcular edad y categorizar al titular
            $current_date = new DateTime();
            $birth_date = new DateTime($beneficiary->birth_date);
            $age = $current_date->diff($birth_date)->y;

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

            // Procesar cada miembro de la familia
            foreach ($family_members as $member) {
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

            $sheet->setCellValue('AB' . $row, $male_adults);
            $sheet->setCellValue('AC' . $row, $female_adults);
            $sheet->setCellValue('AD' . $row, $male_nna);
            $sheet->setCellValue('AE' . $row, $female_nna);

            $row++;
            $counter ++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks"  . DS . 'Favorita reporte' .  time() . '.xlsx';
        $writer->save($filename);

        return  $filename;
    }
}
?>
