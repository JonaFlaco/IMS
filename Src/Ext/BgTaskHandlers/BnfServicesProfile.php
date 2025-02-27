<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;
use PhpOffice\PhpWord\Shared\Converter;
use DateTime;


class BnfServicesProfile extends BgTaskHandlers
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
        $download_Mode = $_POST['download_mode'];
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $ids_array = _explode(',', $id);
        } else {

            throw new \App\Exceptions\MissingDataFromRequesterException("ID is required , but not provided");
        }

        $zip = new \ZipArchive();
        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . "BNF PROFILES" . time() . '.zip';

        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }

        if ($download_Mode == 1) {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();

            foreach ($ids_array as $id) {
                $file_name = $this->GenerateOneReport($id, $phpWord);
                $bnf_pool = $this->coreModel->nodeModel("beneficiaries")
                    ->id($id)
                    ->loadFirstOrFail();

                // Add generated word
                $zip->addFile(TEMP_DIR . DS . $file_name, "Perfil de beneficiarios" . ".docx");
            }
        }

        if ($download_Mode == 2) {
            foreach ($ids_array as $id) {
                $file_name = $this->GenerateReports($id);

                $bnf_pool = $this->coreModel->nodeModel("beneficiaries")
                    ->id($id)
                    ->loadFirstOrFail();
                // Add generated word
                $zip->addFile(TEMP_DIR . DS . $file_name, "Reporte de beneficiarios/{$bnf_pool->code}/{$file_name}");
            }
        }


        $zip->close();

        return $filename;
    }
    public function afterCompletion()
    {
    }
    public function LoadBeneficiaryData($id)
    {
        $bnf_pool = $this->coreModel->nodeModel("beneficiaries")
            ->id($id)
            ->loadFirstOrFail();
        if ($bnf_pool->id) {
            $bnf_services = $this->coreModel->nodeModel("b_services")
                ->fields(["service_id", "code", "bnf_id", "status_id", "status_id_display"])
                ->where("m.bnf_id = :id")
                ->bindValue("id", $bnf_pool->id)
                ->load();
        }

        return [$bnf_services, $bnf_pool];
    }
    public function GenerateReports($id)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // get data from the function
        list($bnf_services, $bnf_pool) = $this->LoadBeneficiaryData($id);

        $section = $phpWord->addSection();
        $header = $section->addHeader();
        $header->addImage(
            PUBLIC_DIR_FULL . "\assets\\ext\images\logo.png",
            array(
                'width'         => Converter::cmToPixel(3), // Ancho de la imagen en cm
                'height'        => Converter::cmToPixel(1), // Alto de la imagen en cm
                'align'         => 'center'
            )
        );
        $section->addText(
            "PERFIL DE BENEFICIARIO $bnf_pool->code",
            array(
                'bold' => true,
                'size' => 14,
                'align' => 'centered'
            )
        );
        $section->addTextBreak();

        $section->addText("INFORMACION PERSONAL", ['bold' => true]);
        $textRun = $section->addTextRun();
        $textRun->addText("NOMBRE Y APELLIDO: ", ['bold' => true, 'color' => '#00309F']);
        $textRun->addText("$bnf_pool->full_name");



        if ($bnf_pool->national_id_photo_front_name && $bnf_pool->national_id_photo_back_name) {
            $section->addText("Foto frontal CI:", ['bold' => true]);
            $section->addImage(
                UPLOAD_DIR_FULL . DS . "beneficiaries" . DS . $bnf_pool->national_id_photo_front_name,
                array(
                    'width'         => 250,
                    'height'        => 170,
                    'marginTop'     => -1,
                    'marginLeft'    => -1,
                    'wrappingStyle' => 'inline'
                )
            );
            $section->addText("Foto Posterior CI:", ['bold' => true]);
            $section->addImage(
                UPLOAD_DIR_FULL . DS . "beneficiaries" . DS . $bnf_pool->national_id_photo_back_name,
                array(
                    'width'         => 250,
                    'height'        => 170,
                    'marginTop'     => -1,
                    'marginLeft'    => -1,
                    'wrappingStyle' => 'inline'
                )
            );
        } else {
            $section->addText("Foto frontal CI: N/A", ['bold' => true]);
            $section->addText("Foto Posterior CI: N/A", ['bold' => true]);
        }
        if ($bnf_services) {
            $services = \App\Core\Application::getInstance()->coreModel->nodeModel('b_services')
                ->where("m.bnf_id = :bnf_id")
                ->bindValue(":bnf_id", $bnf_pool->id)
                ->load();
        }

        $phpWord->addTableStyle('miTabla', array(
            'cellMargin' => 100, 
            'borderSize' => 2, 
            'borderColor' => '000000',
        ));
        $parent_table = $section->addTable('miTabla');

        $parent_table_row_4 = $parent_table->addRow();
        $parent_table_row_4_cell_1 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_1->addText("Código de servicio", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_2 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_2->addText("Servicio", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_3 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_3->addText("Estado", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_4 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_4->addText("Provincia", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_5 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_5->addText("Fecha", ['bold' => true, 'color' => '#00309F']);

        if ($bnf_services && count($services) > 0) {
            foreach ($services as $service) {

                $parent_table_row_5 = $parent_table->addRow(); // add row for each service

                $parent_table_row_5_cell_1 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_1->addText($service->code);

                $parent_table_row_5_cell_2 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_2->addText($service->service_id_display);

                $parent_table_row_5_cell_3 = $parent_table_row_5->addCell();
                if ($service->status_id == 95)
                    $status = "pending";
                if ($service->status_id == 91)
                    $status = "Fully Closed";
                if ($service->status_id == 2)
                    $status = "Approved";
                if ($service->status_id == 3)
                    $status = "Rejected";
                if ($service->status_id == 89)
                    $status = "Case Worker Approved";

                $parent_table_row_5_cell_3->addText($status);

                $parent_table_row_5_cell_4 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_4->addText($service->province_id_display);

                $parent_table_row_5_cell_5 = $parent_table_row_5->addCell();
                $date_object = new DateTime($service->created_date);
                $parent_table_row_5_cell_5->addText($date_object->format('Y-m-d'));
            }
        } else {
            $parent_table_row_5 = $parent_table->addRow();
            $parent_table_row_5_cell_1 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_1->addText("N/A");
            $parent_table_row_5_cell_2 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_2->addText("N/A");
            $parent_table_row_5_cell_3 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_3->addText("N/A");
            $parent_table_row_5_cell_4 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_4->addText("N/A");
        }

        $filename = TEMP_DIR . "\\Beneficiary profile-" . $bnf_pool->code . '.docx';
        $phpWord->save($filename);

        return basename($filename);
    }

    public function GenerateOneReport($id, $phpWord)
    {

        // Get data
        list($bnf_services, $bnf_pool) = $this->LoadBeneficiaryData($id);

        $section = $phpWord->addSection();

        // Add header with image
        $header = $section->addHeader();
        $header->addImage(
            PUBLIC_DIR_FULL . "\assets\\ext\images\logo.png",
            array(
                'width'         => Converter::cmToPixel(3), //  width in cm
                'height'        => Converter::cmToPixel(1), // height in cm
                'align'         => 'center'
            )
        );

        $section->addText(
            "PERFIL DE BENEFICIARIO $bnf_pool->code",
            array(
                'bold' => true,
                'size' => 14,
                'align' => 'centered'
            )
        );
        $section->addTextBreak();

        $section->addText("INFORMACION PERSONAL", ['bold' => true]);
        $textRun = $section->addTextRun();
        $textRun->addText("NOMBRE Y APELLIDO: ", ['bold' => true, 'color' => '#00309F']);
        $textRun->addText("$bnf_pool->full_name");



        if ($bnf_pool->national_id_photo_front_name && $bnf_pool->national_id_photo_back_name) {
            //Add a cell to that parent table row
            $section->addText("Foto frontal CI:", ['bold' => true]);
            $section->addImage(
                UPLOAD_DIR_FULL . DS . "beneficiaries" . DS . $bnf_pool->national_id_photo_front_name,
                array(
                    'width'         => 250,
                    'height'        => 170,
                    'marginTop'     => -1,
                    'marginLeft'    => -1,
                    'wrappingStyle' => 'inline'
                )
            );
            $section->addText("Foto Posterior CI:", ['bold' => true]);
            $section->addImage(
                UPLOAD_DIR_FULL . DS . "beneficiaries" . DS . $bnf_pool->national_id_photo_back_name,
                array(
                    'width'         => 250,
                    'height'        => 170,
                    'marginTop'     => -1,
                    'marginLeft'    => -1,
                    'wrappingStyle' => 'inline'
                )
            );
        } else {
            $section->addText("Foto frontal CI: N/A", ['bold' => true]);
            $section->addText("Foto Posterior CI: N/A", ['bold' => true]);
        }
        $phpWord->addTableStyle('miTabla', array(
            'cellMargin' => 100, // add margin betwen cells
            'borderSize' => 2, // Border size
            'borderColor' => '000000',
        ));
        $parent_table = $section->addTable('miTabla');

        $services = \App\Core\Application::getInstance()->coreModel->nodeModel('b_services')
            ->where("m.bnf_id = :bnf_id")
            ->bindValue(":bnf_id", $bnf_pool->id)
            ->load();


        $parent_table_row_4 = $parent_table->addRow();
        $parent_table_row_4_cell_1 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_1->addText("Código de servicio", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_2 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_2->addText("Servicio", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_3 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_3->addText("Estado", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_4 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_4->addText("Provincia", ['bold' => true, 'color' => '#00309F']);

        $parent_table_row_4_cell_5 = $parent_table_row_4->addCell();
        $parent_table_row_4_cell_5->addText("Fecha", ['bold' => true, 'color' => '#00309F']);

        if ($bnf_services && count($services) > 0) {
            foreach ($services as $service) {

                $parent_table_row_5 = $parent_table->addRow(); // add row for each service

                $parent_table_row_5_cell_1 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_1->addText($service->code);

                $parent_table_row_5_cell_2 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_2->addText($service->service_id_display);

                $parent_table_row_5_cell_3 = $parent_table_row_5->addCell();
                if ($service->status_id == 95)
                    $status = "pending";
                if ($service->status_id == 91)
                    $status = "Fully Closed";
                if ($service->status_id == 2)
                    $status = "Approved";
                if ($service->status_id == 3)
                    $status = "Rejected";
                if ($service->status_id == 89)
                    $status = "Case Worker Approved";

                $parent_table_row_5_cell_3->addText($status);

                $parent_table_row_5_cell_4 = $parent_table_row_5->addCell();
                $parent_table_row_5_cell_4->addText($service->province_id_display);

                $parent_table_row_5_cell_5 = $parent_table_row_5->addCell();
                $date_object = new DateTime($service->created_date);
                $parent_table_row_5_cell_5->addText($date_object->format('Y-m-d'));
            }
        } else {
            $parent_table_row_5 = $parent_table->addRow();
            $parent_table_row_5_cell_1 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_1->addText("N/A");
            $parent_table_row_5_cell_2 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_2->addText("N/A");
            $parent_table_row_5_cell_3 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_3->addText("N/A");
            $parent_table_row_5_cell_4 = $parent_table_row_5->addCell();
            $parent_table_row_5_cell_4->addText("N/A");
        }


        $filename = TEMP_DIR . "\\Beneficiaries-profiles" . '.docx';
        $phpWord->save($filename);

        return basename($filename);
    }
}
