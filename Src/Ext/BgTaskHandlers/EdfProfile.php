<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;

class EdfProfile extends BgTaskHandlers
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

        $file_names = [];

        foreach ($ids_array as $id) {

            $file_names[] = $this->GenerateReport($id);
        }

        $zip = new \ZipArchive();
        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . "EDF EOI" . time() . '.zip';

        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }

        foreach ($ids_array as $id) {
            $file_name = $this->GenerateReport($id);
            $eoi = $this->coreModel->nodeModel("edf_eoi")->id($id)->loadFirstOrFail();

            $pdf_path = UPLOAD_DIR_FULL . DS . $eoi->identification_document_rep_name;

            if (file_exists($pdf_path)) {
                $pdf_content = file_get_contents($pdf_path);

                if ($pdf_content !== false) {
                    // Add pdf
                    $zip->addFromString("Reports" . DS . $eoi->business_name . DS . $eoi->identification_document_rep_name, $pdf_content);
                }
            } else {
                // Add empty folder if attatchment was not added
                $zip->addEmptyDir("Reports" . DS . $eoi->business_name . DS . "PDF_no_agregado");
            }

            // Add generated excel
            $zip->addFile(TEMP_DIR . DS . $file_name, "Reports/{$eoi->business_name}/{$file_name}");
        }

        $zip->close();

        return $filename;
    }
    public function afterCompletion()
    {
    }

    public function GenerateReport($id)
    {

        $fileObj = $this->coreModel->get_document_file_attachments("edf_summary", "summary");

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileObj->full_path);

        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $edf = $this->coreModel->nodeModel("edf_eoi")
            ->fields(["person_applying"])
            ->id($id)
            ->loadFirstOrFail();


        $spreadsheet->getActiveSheet()->setTitle('data');
        $sheet->setCellValue('F4', $edf->person_applying ?? 'N/A');


        $filename = TEMP_DIR . DS . $edf->code . '-' . time() . '.xlsx';
        $writer->save($filename);
        $without_extension = basename($filename, '.xlsx');
        $new_file_name =  $without_extension . '.xlsx';
        return  $new_file_name;
    }
}
