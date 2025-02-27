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

class ServiceSummary extends BgTaskHandlers
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
            $casesMe = $this->coreModel->nodeModel("beneficiaries")
                ->fields(['id', 'code', 'full_name', 'national_id_no', 'created_date', 'created_user_id', 'status_id', 'case_worker', 'province'])
                ->where("m.id = :ids")
                ->bindValue("ids", $id)
                ->load();
            $casesSelected = array_merge($casesSelected, $casesMe);
        }

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('Solicitantes');

        // Encabezados
        $sheet->setCellValue('A1', 'Fecha de creación');
        $sheet->setCellValue('B1', 'Código de perfil');
        $sheet->setCellValue('C1', 'Oficina');
        $sheet->setCellValue('D1', 'Provincia');
        $sheet->setCellValue('E1', 'Status del Perfil');
        $sheet->setCellValue('F1', 'Gestor Asignado');
        $sheet->setCellValue('G1', 'Fecha de Evaluación');
        $sheet->setCellValue('H1', 'Fecha de Asignación');

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
        $sheet->getStyle('A1:H1')->applyFromArray($styleArray);

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $row = 2;
        $counter = 1;
        foreach ($casesSelected as $item) {
            // Cargar datos del beneficiario
            $bnf_id = $item->id;

            $sheet->getRowDimension($row)->setRowHeight(20);

            $sheet->setCellValue('A' . $row, $item->created_date);
            $sheet->setCellValue('B' . $row, $item->code);
            // Asignar nombre de la oficina en base a la provincia del case worker
            if (in_array($item->province, [24, 8, 7, 18, 2])) {
                $sheet->setCellValue('C' . $row, "C-AMOR");
            }
            if (in_array($item->province, [4])) {
                $sheet->setCellValue('C' . $row, "Huaquillas");
            }
            if (in_array($item->province, [5, 6, 13])) {
                $sheet->setCellValue('C' . $row, "Tulcán");
            }
            if (in_array($item->province, [11, 16, 20, 22])) {
                $sheet->setCellValue('C' . $row, "Lago Agrio");
            }
            if (in_array($item->province, [9, 10, 17])) {
                $sheet->setCellValue('C' . $row, "Guayaquil");
            }
            if (in_array($item->province, [15, 19])) {
                $sheet->setCellValue('C' . $row, "Manta");
            }
            if (!in_array($item->province, [24, 8, 7, 18, 2, 4, 5, 6, 13, 11, 16, 20, 22, 9, 10, 17, 15, 19])) {
                $sheet->setCellValue('C' . $row, $item->province_display);
            }
            $sheet->setCellValue('D' . $row, $item->province_display);
            if ($item->status_id == 2)
                $status_id_display = 'Aprobado';

            if ($item->status_id == 3)
                $status_id_display = 'Rechazado';

            if ($item->status_id == 96)
                $status_id_display = 'Gestor asignado';

            if ($item->status_id == 95)
                $status_id_display = 'Pendiente';

            if ($item->status_id == 88)
                $status_id_display = 'Verificado';

            $sheet->setCellValue('E' . $row, $status_id_display);
            if ($item->case_worker) {

                $caseWorker = $this->coreModel->nodeModel("users")
                    ->where("m.id = :ids")
                    ->bindValue("ids", $item->case_worker)
                    ->loadFirst();

                $sheet->setCellValue('F' . $row, $caseWorker->full_name);
            } else {
                $sheet->setCellValue('F' . $row, "Sin Gestor asignado");
            }

            $evaluation = $this->coreModel->nodeModel("evaluation")
                ->fields(['created_date'])
                ->where("m.beneficiary_id  = :id")
                ->bindValue("id", $bnf_id)
                ->loadFirstOrDefault();

            if ($evaluation) {
                $sheet->setCellValue('G' . $row, $evaluation->created_date);
            } else {
                $sheet->setCellValue('G' . $row, "Sin evaluación registrada");
            }

            $logs = $this->coreModel->nodeModel("ctypes_logs")
                ->where("m.content_id = :ids")
                ->bindValue("ids", (string) $item->id)
                ->load();

            if ($logs) {
                foreach ($logs as $log) {
                    if (!is_null($log->justification) && strpos($log->justification, "Asignó a") === 0) {
                        $assignament_date = $log->created_date;
                    } else {
                        $assignament_date = "Sin Fecha de asignación registrada";
                    }
                }
            }

            $sheet->setCellValue('H' . $row, $assignament_date);


            $row++;
            $counter++;
        }

        //tab 2
        $sheetTwo = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle('Asistencias');

        // Encabezados
        $sheetTwo->setCellValue('A1', '#');
        $sheetTwo->setCellValue('B1', 'Estado de asistencia');
        $sheetTwo->setCellValue('C1', 'Código de beneficiario');
        $sheetTwo->setCellValue('D1', 'Tipo de servicio');
        $sheetTwo->setCellValue('E1', 'Fecha de creación del servicio');

        $sheetTwo->getStyle('A1:E1')->applyFromArray($styleArray);

        foreach (range('A', 'E') as $columnID) {
            $sheetTwo->getColumnDimension($columnID)->setAutoSize(true);
        }

        $rowTwo = 2;
        $counterTwo = 1;
        foreach ($casesSelected as $itemTwo) {
            $bnf_id_two = $itemTwo->id;
            $services = $this->coreModel->nodeModel("b_services")
                ->fields(['created_date', 'sub_service', 'health_referral_new', 'status_id'])
                ->where("m.bnf_id  = :id")
                ->bindValue("id", $bnf_id_two)
                ->load();

            if ($services) {
                foreach ($services as $service) {
                    $sheetTwo->setCellValue('A' . $rowTwo, $counterTwo);

                    if ($service->status_id == 2)
                        $status_display = 'Aprobado';

                    if ($service->status_id == 3)
                        $status_display = 'Rechazado';

                    if ($service->status_id == 95)
                        $status_display = 'Pendiente';

                    if ($service->status_id == 91)
                        $status_display = 'Cerrado';

                    $sheetTwo->setCellValue('B' . $rowTwo, $status_display);
                    $sheetTwo->setCellValue('C' . $rowTwo, $itemTwo->code);

                    if ($service->health_referral_new)
                        $assisted = $service->health_referral_new_display;

                    if ($service->sub_service)
                        $assisted = $service->sub_service_display;

                    $sheetTwo->setCellValue('D' . $rowTwo, $assisted);
                    $sheetTwo->setCellValue('E' . $rowTwo, $service->created_date);

                    $rowTwo++;
                    $counterTwo++;
                }
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks"  . DS . 'Reporte Asistencias' .  time() . '.xlsx';
        $writer->save($filename);

        return  $filename;
    }
}
