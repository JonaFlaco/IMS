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

class BusinessSummary extends BgTaskHandlers
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
        $zip = new \ZipArchive();
        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . "Business Profiles Summary" . time() . '.zip';

        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }

        foreach ($ids_array as $id) {
            $file_name = $this->GenerateReports($id);
            $edf_eoi_app = $this->coreModel->nodeModel("edf_eoi_full_application")
                ->id($id)
                ->loadFirstOrFail();
            $edf_eoi = $this->coreModel->nodeModel("edf_eoi")
                ->where("m.id = :id")
                ->bindValue("id", $edf_eoi_app->eoi_id)
                ->loadFirstOrFail();
            $zip->addFile(TEMP_DIR . DS . $file_name, "Business Profile Summary/{$edf_eoi->code}/{$file_name}");
        }

        $zip->close();

        return $filename;
    }
    public function afterCompletion() {}

    public function GenerateReports($id)
    {
        $edf_eoi_app = $this->coreModel->nodeModel("edf_eoi_full_application")
            ->id($id)
            ->loadFirstOrFail();
        $edf_eoi = $this->coreModel->nodeModel("edf_eoi")
            ->where("m.id = :id")
            ->bindValue("id", $edf_eoi_app->eoi_id)
            ->loadFirstOrFail();
        $edf_eoi_ver = $this->coreModel->nodeModel("edf_eoi_verification")
            ->where("m.business_id = :id")
            ->bindValue("id", $edf_eoi_app->eoi_id)
            ->loadFirstOrFail();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados y estilos 
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('data');
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('D1:I1');
        $sheet->mergeCells('J1:M1');
        $sheet->setCellValue('D1', "The International Organization for Migration (IOM) Enterprise Development Fund (EDF) Business Profile Summary");
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getStyle('D1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $styleArray = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '0033A0']]
        ];
        foreach (range('C', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Subtítulos

        $sheet->getStyle('A2:M2')->applyFromArray($styleArray);
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', "EDF Stage");
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('F2:M2');
        $sheet->setCellValue('F2', "Staff Name");
        $sheet->getStyle('F2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $row = 3;

        //EDF Stage				
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", "EOI Verification");
        $sheet->mergeCells("F{$row}:M{$row}");
        $sheet->setCellValue("F{$row}", $edf_eoi_ver->created_user_id_display);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Información general");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "EOI-Code");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->code);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Nombre comercial de la empresa");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->business_name);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Razón Social");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->legal_name);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Nombre del postulante");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->person_applying);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Nombre del representante legal");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->full_name_legal_rep);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Género del representante legal");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->gender_legal_rep_display);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Sector");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->sector_display);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Sub-Sector");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->subsector_display);

        $row = 5;

        //EDF Stage				
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "RUC");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->single_taxpayer_registry_number);
        $row++;

        $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Ciudad de residencia");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->residence_city_legal_rep_display);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Direccion Física");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->exact_address);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Email");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->aplicant_email);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Número de teléfono");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->contact_number);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "EOI Score");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->score);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "EOI-VER Score");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_ver->score);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Años de Operación");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->operation_years_company);
        $row++;

        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", "Monto Solicitado (USD)");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", "Monto de contribución (USD)");
        $sheet->getStyle("H{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "EOI");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->total_amount__iom);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Aplicación");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->amount_requested_fix);

        $row = 14;
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "EOI");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->contribution_value);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Aplicación");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_app->amount_business_fixed);
        $row++;

        $sheet->mergeCells("A{$row}:G{$row}");
        $sheet->setCellValue("A{$row}", "Empleados actuales");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", "Nuevos Empleados");
        $sheet->getStyle("H{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "EOI");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi->employees_number);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Aplicación");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->current_number_employees);

        $row = 17;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "EOI");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi->additional_staff_need);
        $row++;

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Aplicación");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_app->number_new_jobs);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Business Situation");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Tax compliance certificate");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", "Sí");

        $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Certificate of Compliance with Social Benefits");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", "Sí");
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Is the applicant the legal representative?");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", isset($edf_eoi->is_legal_represent) ? ($edf_eoi->is_legal_represent == 1 ? "SI" : "NO") : "N/A");

        $endorsement = 'No';
        if ($edf_eoi->auth_letter_name)
            $endorsement = 'Sí';

        $sheet->getStyle('H' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Letter of Endorsement");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $endorsement);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Legal Judgement?");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->legal_judgement_display);

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Explain");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_app->explain_legal_judgement);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->pending_debt_loands_display);

        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Explain");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_app->explain_pending_debt_loans);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Financial Analysis");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $styleArrayGray = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '787a7d']]
        ];
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->setCellValue("A{$row}", "Cost per Job");
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", "Business contribution as % of IOM grant");
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", "Return on Investment (ROI)");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", "Increase in salaries");
        $row++;

        //Net profit "6 Months After EDF Income Forecast"
        //Formula = ('Sales income' + 'Other business income') - ('Materials cost' + 'Salaries cost' + 'Utilities cost' + 'Other business expenses')

        $netProfit = ($edf_eoi_app->sales_income_forecast + $edf_eoi_app->other_business_forecast) - ($edf_eoi_app->material_cost_forecast + $edf_eoi_app->salaries_cost_forecast + $edf_eoi_app->utilies_cost_forecast + $edf_eoi_app->other_expenses_forecast);
        $netProfitLast = ($edf_eoi_app->sales_income + $edf_eoi_app->other_business) - ($edf_eoi_app->materials_cost + $edf_eoi_app->salaries_cost + $edf_eoi_app->utilies_cost + $edf_eoi_app->other_expenses);

        $sheet->mergeCells("A{$row}:C{$row}");
        $costPerJob = $edf_eoi_app->amount_requested_fix / $edf_eoi_app->number_new_jobs;
        $sheet->setCellValue("A{$row}", $costPerJob);
        $sheet->mergeCells("D{$row}:F{$row}");
        $bsnssPercentaje = ($edf_eoi_app->amount_business_fixed / $edf_eoi_app->amount_requested_fix) * 100;
        $sheet->setCellValue("D{$row}", $bsnssPercentaje);
        $sheet->mergeCells("G{$row}:I{$row}");

        //Return on Investment (ROI)
        //Formula = [('Net Profit 6 Months After EDF')/('IOM grant' + 'Business Contribution')]*100
        $returnInvestment = (($netProfit) / ($edf_eoi_app->amount_requested_fix + $edf_eoi_app->amount_business_fixed)) * 100;

        $sheet->setCellValue("G{$row}", $returnInvestment);

        $positionsNumber = 0;
        $totalInSalaries = 0;
        foreach ($edf_eoi_app->vacancies as $vacancies) {
            $positionsNumber += $vacancies->number_of_positions;
            $totalInSalaries += $vacancies->average_salary_fixed;
        }
        if ($positionsNumber !== 0)
            $averageSalary = $totalInSalaries / $positionsNumber;

        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $averageSalary);
        $row++;

        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->setCellValue("A{$row}", "Does current profit cover increase in salaries? (Last month profit - increase in salaries)");
        $sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", "Will future profit (6 months after grant) cover increase in salaries? (6 months after EDF profit - increase in salaries)");
        $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", "Sales increase (12 months after - 1 month after)");
        $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", "% of Sales Increase (from 1 month after to 12 months after)");
        $row++;

        //Formula = ('Last Month Income Statement Net Profit' - 'Increase in Salaries')
        $coverIcrease = ($netProfitLast - $averageSalary);
        $coverIcreaseFut = ($netProfit - $averageSalary);

        foreach ($edf_eoi_app->sales_forecast as $sales_forecast) {
            if ($sales_forecast->months == 1) {
                $oneMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 2) {
                $secondMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 3) {
                $thirdMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 4) {
                $fourthMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 5) {
                $fivethMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 6) {
                $sixthMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 7) {
                $sevenMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 8) {
                $eightMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 9) {
                $ninethMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 10) {
                $tenthMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 11) {
                $eleventhMonthAfter = $sales_forecast->sales_fixed;
            }
            if ($sales_forecast->months == 12) {
                $twelvethMonthAfter = $sales_forecast->sales_fixed;
            }
        }

        //Calculate from "Montly Sales Forecast after EDF grant"
        // Formula = ('12 months after' - '1 month after')
        $salesIncrease = $twelvethMonthAfter - $oneMonthAfter;
        //Calculate from "Montly Sales Forecast after EDF grant"
        // Formula = ('12 months after' - '1 month after')/('1 month after')
       

        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", $coverIcrease);
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", $coverIcreaseFut);
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", $salesIncrease);
        $sheet->mergeCells("J{$row}:M{$row}");
        if ($oneMonthAfter != 0) {
            $montlyForecast = ($twelvethMonthAfter - $oneMonthAfter) / ($oneMonthAfter);
            $sheet->setCellValue("J{$row}", $montlyForecast);
        }else{
            $sheet->setCellValue("J{$row}", "");
        }
        $row++;

        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->setCellValue("A{$row}", "Management Indicators");
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", "% of Profit invested on marketing");
        $sheet->getStyle('G' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", "% of Profit invested on innovation and transformation");
        $sheet->getStyle('J' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", "Tools or software used for sales processes");
        $row++;

        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", $edf_eoi_app->management_indicators);
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", "$edf_eoi_app->average_percentage %");
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", "$edf_eoi_app->on_average_fixed %");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", $edf_eoi_app->explain_tools);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Income Statement");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->setCellValue("A{$row}", "");
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", "3 years before");
        $sheet->mergeCells("G{$row}:I{$row}");
        $sheet->setCellValue("G{$row}", "2 years before");
        $sheet->mergeCells("J{$row}:M{$row}");
        $sheet->setCellValue("J{$row}", "1 years before");
        $row++;

        foreach ($edf_eoi_app->financial_analysis as $financial_analysis) {
            if ($financial_analysis->years == 2) {
                $threeYearsAgo = $financial_analysis->total_income_fixed;
                $threeYearsAgoEx = $financial_analysis->total_expenses_fixed;
                $threeYearsAgoNet = $financial_analysis->net_profit_fixed;
            }

            if ($financial_analysis->years == 3) {
                $twoYearsAgo = $financial_analysis->total_income_fixed;
                $twoYearsAgoEx = $financial_analysis->total_expenses_fixed;
                $twoYearsAgoNet = $financial_analysis->net_profit_fixed;
            }

            if ($financial_analysis->years == 4) {
                $pastYear = $financial_analysis->total_income_fixed;
                $pastYearEx = $financial_analysis->total_expenses_fixed;
                $pastYearNet = $financial_analysis->net_profit_fixed;
            }
        }
        $styleArrayLightGray = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_BLACK]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'dbdcdf']]
        ];
        if ($edf_eoi_app->financial_analysis) {

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", "Total income");
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", $threeYearsAgo);
            $sheet->mergeCells("G{$row}:I{$row}");
            $sheet->setCellValue("G{$row}", $twoYearsAgo);
            $sheet->mergeCells("J{$row}:M{$row}");
            $sheet->setCellValue("J{$row}", $pastYear);
            $row++;

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", "Total expenses");
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", $threeYearsAgoEx);
            $sheet->mergeCells("G{$row}:I{$row}");
            $sheet->setCellValue("G{$row}", $twoYearsAgoEx);
            $sheet->mergeCells("J{$row}:M{$row}");
            $sheet->setCellValue("J{$row}", $pastYearEx);
            $row++;

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", "Total net profit");
            $sheet->mergeCells("D{$row}:F{$row}");
            $sheet->setCellValue("D{$row}", $threeYearsAgoNet);
            $sheet->mergeCells("G{$row}:I{$row}");
            $sheet->setCellValue("G{$row}", $twoYearsAgoNet);
            $sheet->mergeCells("J{$row}:M{$row}");
            $sheet->setCellValue("J{$row}", $pastYearNet);
        }
        $row++;

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", "Last Month Income Statement");
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", "6 Months After EDF Income Forecast");
        $row++;

        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Sales income");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->sales_income);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->sales_income_forecast);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Other business income");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->other_business);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->other_business_forecast);
        $row++;

        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Materials cost");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->materials_cost);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->material_cost_forecast);
        $row++;

        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Salaries cost");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->salaries_cost);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->salaries_cost_forecast);
        $row++;

        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Utilies cost");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->utilies_cost);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->utilies_cost_forecast);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Other business expenses");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->other_expenses);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $edf_eoi_app->other_expenses_forecast);
        $row++;

        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Net Profit");
        $sheet->mergeCells("C{$row}:G{$row}");
        $sheet->setCellValue("C{$row}", $netProfitLast);
        $sheet->mergeCells("H{$row}:M{$row}");
        $sheet->setCellValue("H{$row}", $netProfit);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Monthly Sales Forecast after EDF Grant");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "1 month after");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", "2 months after");
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "3 months after");
        $sheet->mergeCells("G{$row}:H{$row}");
        $sheet->setCellValue("G{$row}", "4 months after");
        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", "5 months after");
        $sheet->mergeCells("K{$row}:M{$row}");
        $sheet->setCellValue("K{$row}", "6 months after");
        $row++;



        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", $oneMonthAfter);
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $secondMonthAfter);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", $thirdMonthAfter);
        $sheet->mergeCells("G{$row}:H{$row}");
        $sheet->setCellValue("G{$row}", $fourthMonthAfter);
        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", $fivethMonthAfter);
        $sheet->mergeCells("K{$row}:M{$row}");
        $sheet->setCellValue("K{$row}", $sixthMonthAfter);
        $row++;

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "7 month after");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", "8 months after");
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "9 months after");
        $sheet->mergeCells("G{$row}:H{$row}");
        $sheet->setCellValue("G{$row}", "10 months after");
        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", "11 months after");
        $sheet->mergeCells("K{$row}:M{$row}");
        $sheet->setCellValue("K{$row}", "12 months after");
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", $sevenMonthAfter);
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $eightMonthAfter);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", $ninethMonthAfter);
        $sheet->mergeCells("G{$row}:H{$row}");
        $sheet->setCellValue("G{$row}", $tenthMonthAfter);
        $sheet->mergeCells("I{$row}:J{$row}");
        $sheet->setCellValue("I{$row}", $eleventhMonthAfter);
        $sheet->mergeCells("K{$row}:M{$row}");
        $sheet->setCellValue("K{$row}", $twelvethMonthAfter);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Staff comment from EOI verification");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", $edf_eoi_ver->staff_comments);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "EOI Verification Staff Observation");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Is the location good for business?");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_ver->business_location_display);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "Explain");
        $sheet->mergeCells("G{$row}:M{$row}");
        $sheet->setCellValue("G{$row}", $edf_eoi_ver->describe_business);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Did you observe business activities?");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_ver->observe_business_display);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "Explain");
        $sheet->mergeCells("G{$row}:M{$row}");
        $sheet->setCellValue("G{$row}", $edf_eoi_ver->explain_business_activities);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Is the business environment safe and inclusive?");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_ver->business_safe_display);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "Explain");
        $sheet->mergeCells("G{$row}:M{$row}");
        $sheet->setCellValue("G{$row}", $edf_eoi_ver->business_safe);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Is the business compliant with labor standards?");
        $sheet->mergeCells("C{$row}:D{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_ver->standards_practices_display);
        $sheet->mergeCells("E{$row}:F{$row}");
        $sheet->setCellValue("E{$row}", "Explain");
        $sheet->mergeCells("G{$row}:M{$row}");
        $sheet->setCellValue("G{$row}", $edf_eoi_ver->explain_standar);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Employee Verification");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;


        foreach ($edf_eoi_ver->employee_ver as $employee_ver) {
            $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", "Full name");
            $sheet->setCellValue("C{$row}", "Average Monthly Salary");
            $sheet->mergeCells("D{$row}:E{$row}");
            $sheet->setCellValue("D{$row}", "Type of Contract");
            $sheet->setCellValue("F{$row}", "Social Benefits");
            $sheet->setCellValue("G{$row}", "Working hours/week");
            $sheet->setCellValue("H{$row}", "Working days/week");
            $sheet->setCellValue("I{$row}", "Salary paid on time?");
            $sheet->setCellValue("J{$row}", "Incentives?");
            $sheet->setCellValue("K{$row}", "Job sustainable?");
            $sheet->mergeCells("L{$row}:M{$row}");
            $sheet->setCellValue("L{$row}", "Good working environment?");
            $row++;

            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", $employee_ver->name);
            $sheet->setCellValue("C{$row}", $employee_ver->salary);
            $sheet->mergeCells("D{$row}:E{$row}");
            $sheet->setCellValue("D{$row}",  $employee_ver->contract_type_display);
            $sheet->setCellValue("F{$row}", $employee_ver->social_benefits_display);
            $sheet->setCellValue("G{$row}", $employee_ver->work_hours);
            $sheet->setCellValue("H{$row}", $employee_ver->work_days);
            $sheet->setCellValue("I{$row}", $employee_ver->time_pay_display);
            $sheet->setCellValue("J{$row}", $employee_ver->incentives_display);
            $sheet->setCellValue("K{$row}", $employee_ver->sustainable_job_display);
            $sheet->mergeCells("L{$row}:M{$row}");
            $sheet->setCellValue("L{$row}", $employee_ver->environment_work_display);
            $row++;

            $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(40);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", "Why no social benefits?");
            $sheet->mergeCells("C{$row}:F{$row}");
            $sheet->setCellValue("C{$row}", $employee_ver->explain_social_benefits);
            $sheet->getStyle("G{$row}:H{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("G{$row}:H{$row}");
            $sheet->setCellValue("G{$row}", "Why salary not on time?");
            $sheet->mergeCells("I{$row}:M{$row}");
            $sheet->setCellValue("I{$row}", $employee_ver->explain_pay_time);
            $row++;

            $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(40);
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", "What kind of incentives?");
            $sheet->mergeCells("C{$row}:F{$row}");
            $sheet->setCellValue("C{$row}", $employee_ver->kind_incentives);
            $sheet->getStyle("G{$row}:H{$row}")->applyFromArray($styleArrayLightGray);
            $sheet->mergeCells("G{$row}:H{$row}");
            $sheet->setCellValue("G{$row}", "Why not good working environment?");
            $sheet->mergeCells("I{$row}:M{$row}");
            $sheet->setCellValue("I{$row}", $employee_ver->explain_environment_work);
            $row++;
        }

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "Expansion Plan from Application");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", $edf_eoi_app->expansion_plan);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Time needed to implement the Expansion Plan (Months)");
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->time_months_expansion_plan);
        $sheet->getStyle("G{$row}:H{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("G{$row}:H{$row}");
        $sheet->setCellValue("G{$row}", "Number of Indirect Jobs");
        $sheet->mergeCells("I{$row}:M{$row}");
        $sheet->setCellValue("I{$row}", $edf_eoi_app->indirect_jobs);
        $row++;

        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension($row)->setRowHeight(40);
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($styleArrayLightGray);
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", "Type of Indirect Jobs");
        $sheet->mergeCells("C{$row}:M{$row}");
        $sheet->setCellValue("C{$row}", $edf_eoi_app->type_jobs);
        $row++;

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "List of the new vacancies");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Vacancy name");
        $sheet->mergeCells("D{$row}:G{$row}");
        $sheet->setCellValue("D{$row}", "Required skills");
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Contract Type");
        $sheet->mergeCells("J{$row}:K{$row}");
        $sheet->setCellValue("J{$row}", "Number of positions");
        $sheet->mergeCells("L{$row}:M{$row}");
        $sheet->setCellValue("L{$row}", "Average salary (USD)");
        $row++;

        foreach ($edf_eoi_app->vacancies as $vacancies) {
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", $vacancies->vacancy_title);
            $sheet->mergeCells("D{$row}:G{$row}");
            $sheet->setCellValue("D{$row}", $vacancies->skills_qua);
            $sheet->mergeCells("H{$row}:I{$row}");
            $sheet->setCellValue("H{$row}", $vacancies->type_contract);
            $sheet->mergeCells("J{$row}:K{$row}");
            $sheet->setCellValue("J{$row}", $vacancies->number_of_positions);
            $sheet->mergeCells("L{$row}:M{$row}");
            $sheet->setCellValue("L{$row}", $vacancies->average_salary_fixed);
            $row++;
        }

        $sheet->mergeCells("A{$row}:M{$row}");
        $sheet->setCellValue("A{$row}", "List of new purchases (in USD)");
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArray);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $row++;

        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($styleArrayGray);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Future purchase");
        $sheet->mergeCells("D{$row}:E{$row}");
        $sheet->setCellValue("D{$row}", "Quantity");
        $sheet->mergeCells("F{$row}:G{$row}");
        $sheet->setCellValue("F{$row}", "Unit Cost");
        $sheet->mergeCells("H{$row}:I{$row}");
        $sheet->setCellValue("H{$row}", "Total");
        $sheet->mergeCells("J{$row}:K{$row}");
        $sheet->setCellValue("J{$row}", "IOM contribution");
        $sheet->mergeCells("L{$row}:M{$row}");
        $sheet->setCellValue("L{$row}", "Owner's contribution");
        $row++;

        foreach ($edf_eoi_app->hitos as $hitos) {
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", $hitos->item);
            $sheet->mergeCells("D{$row}:E{$row}");
            $sheet->setCellValue("D{$row}", $hitos->quantity);
            $sheet->mergeCells("F{$row}:G{$row}");
            $sheet->setCellValue("F{$row}", $hitos->unit_cost_fixed);
            $sheet->mergeCells("H{$row}:I{$row}");
            $total = $hitos->unit_cost_fixed * $hitos->quantity;
            $sheet->setCellValue("H{$row}", $total);
            $sheet->mergeCells("J{$row}:K{$row}");
            $sheet->setCellValue("J{$row}", $hitos->oim_contribution_fixed);
            $sheet->mergeCells("L{$row}:M{$row}");
            $sheet->setCellValue("L{$row}", $hitos->business_contribution_fixed);
            $row++;
        }

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $highestRow = $sheet->getHighestRow();

        foreach (range('A', 'M') as $columnID) {
            $range = $columnID . '1:' . $columnID . $highestRow;
            $sheet->getStyle($range)->applyFromArray($borderStyle);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = TEMP_DIR . DS . "Business Profile Summary - {$edf_eoi->code}" . '.xlsx';
        $writer->save($filename);

        return basename($filename);
    }
}
