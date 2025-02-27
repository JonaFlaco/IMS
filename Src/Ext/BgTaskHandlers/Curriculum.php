<?php

/*
 * Home controller
 */

namespace Ext\BgTaskHandlers;

use App\Core\BgTaskHandlers;
use PhpOffice\PhpWord\Shared\Converter;
use DateTime;


class Curriculum extends BgTaskHandlers
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
        $filename = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . "CV APLICANTES" . time() . '.zip';

        if ($zip->open($filename, \ZipArchive::CREATE) !== TRUE) {
            exit("cannot open <$filename>\n");
        }


        if ($download_Mode == 2) {
            foreach ($ids_array as $id) {
                $file_name = $this->GenerateReports($id);

                $lab_match_applicant = $this->coreModel->nodeModel("labour_matching")
                    ->id($id)
                    ->loadFirstOrFail();
                if ($lab_match_applicant->senescyt_register == 1 ) {
                    // Ruta del archivo adjunto
                    $attachment_path = UPLOAD_DIR_FULL . DS . 'labour_matching' . DS . $lab_match_applicant->degree_file_name;

                    // Verificar si el archivo adjunto existe
                    if (file_exists($attachment_path)) {
                        // Leer el contenido del archivo adjunto
                        $attachment_content = file_get_contents($attachment_path);

                        // Agregar el contenido del archivo adjunto al archivo ZIP
                        if ($attachment_content !== false) {
                            $zip->addFromString("CVs de aplicantes/{$lab_match_applicant->name}/{$lab_match_applicant->degree_file_name}", $attachment_content);
                        } else {
                            echo "Error: No se pudo leer el contenido del archivo adjunto.";
                        }
                    }

                    // foreach ($lab_match_applicant->fourth_fiveth_degrees as $fourth_degree) {
                    //     $document_name = $fourth_degree->fourth_level_degree_name;
                    //     $document_path = UPLOAD_DIR_FULL . DS . 'labour_matching' . DS . $document_name;

                    //     // Verificar si el documento existe
                    //     if (file_exists($document_path)) {
                    //         // Leer el contenido del documento
                    //         $document_content = file_get_contents($document_path);

                    //         // Agregar el contenido del documento al archivo ZIP
                    //         if ($document_content !== false) {
                    //             $zip_path = "CVs de aplicantes/{$lab_match_applicant->name}/{$document_name}";
                    //             $zip->addFromString($zip_path, $document_content);
                    //             echo "Documento adjunto agregado al ZIP: $zip_path";
                    //         } else {
                    //             echo "Error: No se pudo leer el contenido del documento.";
                    //         }
                    //     } else {
                    //         echo "El documento no existe: $document_path";
                    //     }
                    // }
                } 



                // Agregar el archivo Word generado al archivo ZIP
                $zip->addFile(TEMP_DIR . DS . $file_name, "CVs de aplicantes/{$lab_match_applicant->name}/{$file_name}");
            }
        }


        $zip->close();

        return $filename;
    }
    public function afterCompletion()
    {
    }

    public function GenerateReports($id)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $lab_match_applicant = $this->coreModel->nodeModel("labour_matching")
            ->id($id)
            ->loadFirstOrFail();
        $section = $phpWord->addSection();
        $header = $section->addHeader();
        // Crear una tabla en el encabezado y establecer su ancho al 100% del documento
        $table = $header->addTable(array('width' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21))); // Establece el ancho de la tabla

        $table->addRow();

        // Primera celda: imagen alineada a la izquierda
        $cell1 = $table->addCell(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(10.5));
        $cell1->addImage(
            PUBLIC_DIR_FULL . "/assets/ext/images/logo.png",
            array(
                'width'  => Converter::cmToPixel(3),
                'height' => Converter::cmToPixel(1),
                'align'  => 'left'
            )
        );

        // Segunda celda (vacía) para actuar como espaciador
        $cell2 = $table->addCell(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));

        // Tercera celda: imagen alineada a la derecha
        $cell3 = $table->addCell(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(10.5));
        $cell3->addImage(
            PUBLIC_DIR_FULL . "/assets/ext/images/logos/edf_main_logo.png",
            array(
                'width'  => Converter::cmToPixel(3),
                'height' => Converter::cmToPixel(1),
                'align'  => 'right'
            )
        );
        $section->addText(
            "$lab_match_applicant->name",
            array(
                'bold' => true,
                'size' => 14,
                'align' => 'centered'
            )
        );

        $section->addText(
            'Detalles personales',
            array(
                'name' => 'Arial',
                'size' => 14,
                'bold' => true,
                'color' => 'FFFFFF',
                'shading' => array('fill' => '0033A0 ')
            ),
        );

        $textrun = $section->addTextRun();

        $textrun->addText(
            "Nombres y apellidos: ",
            array(
                'size' => 12,
                'bold' => true,
                'color' => '000000',
            )
        );
        $textrun->addText(
            $lab_match_applicant->name,
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );
        $textrun->addTextBreak();  // Salto de línea

        $textrun->addText(
            "Ciudad de domicilio: ",
            array(
                'size' => 12,
                'bold' => true,
                'color' => '000000',
            )
        );
        $textrun->addText(
            "$lab_match_applicant->residence_country_display, $lab_match_applicant->province_display, $lab_match_applicant->city_display",
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );
        $textrun->addTextBreak();  // Salto de línea

        $textrun->addText(
            "Nacionalidad: ",
            array(
                'size' => 12,
                'bold' => true,
                'color' => '000000',
            )
        );
        $textrun->addText(
            $lab_match_applicant->nationality_display,
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );
        $textrun->addTextBreak();  // Salto de línea

        $textrun->addText(
            "Número de contacto: ",
            array(
                'size' => 12,
                'bold' => true,
                'color' => '000000',
            )
        );
        $textrun->addText(
            $lab_match_applicant->phone,
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );
        $textrun->addTextBreak();  // Salto de línea

        $textrun->addText(
            "Correo electrónico: ",
            array(
                'size' => 12,
                'bold' => true,
                'color' => '000000',
            )
        );
        $textrun->addText(
            $lab_match_applicant->email,
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );


        $section->addText(
            'Perfil',
            array(
                'name' => 'Arial',
                'size' => 14,
                'bold' => true,
                'color' => 'FFFFFF',
                'shading' => array('fill' => '0033A0 ')
            ),
        );
        $section->addText(
            $lab_match_applicant->profesional_profile,
            array(
                'size' => 12,
                'bold' => false,
                'color' => '000000',
            )
        );

        $textrun->addTextBreak();  // Salto de línea

        $section->addText(
            'Educación',
            array(
                'name' => 'Arial',
                'size' => 14,
                'bold' => true,
                'color' => 'FFFFFF',
                'shading' => array('fill' => '0033A0 ')
            ),
        );

        if ($lab_match_applicant->secondary_study == 0) {
            $section->addText(
                "N/A",
                array(
                    'size' => 12,
                    'bold' => false,
                    'color' => '000000',
                )
            );
        }
        if ($lab_match_applicant->secondary_study == 1 && $lab_match_applicant->third_level_study == 0) {
            $section->addText(
                "Estudios secundarios completos",
                array(
                    'size' => 12,
                    'bold' => false,
                    'color' => '000000',
                )
            );
        }
        if ($lab_match_applicant->secondary_study == 1 && $lab_match_applicant->third_level_study == 1) {
            $section->addText(
                "$lab_match_applicant->study_status_display",
                array(
                    'size' => 12,
                    'bold' => false,
                    'color' => '000000',
                )
            );
            //si el tercer nivel de estudio esta en curso
            if ($lab_match_applicant->study_status == 1) {
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Area de estudios: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->degree_area_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Año previsto de graduación: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->estimated_graduation_year,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
            //si el tercer nivel de estudio esta en graduado 
            if ($lab_match_applicant->study_status == 2) {
                $textrun = $section->addTextRun();
                $textrun->addText(
                    $lab_match_applicant->degree,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Año de graduación: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->end_study,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Institución: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->institution,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Área de estudio: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->degree_area_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
                $textrun->addTextBreak();  // Salto de línea

            }
            //si el tercer nivel de estudio esta en abandono 
            if ($lab_match_applicant->study_status == 3) {
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Area de estudios que estudiaba: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->area_used_to_study_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
            if ($lab_match_applicant->fourth_level_study  == 1) {
                $section->addText(
                    'Estudios de cuarto nivel',
                    array(
                        'name' => 'Arial',
                        'size' => 14,
                        'bold' => true,
                        'color' => 'FFFFFF',
                        'shading' => array('fill' => '0033A0 ')
                    ),
                );
                foreach ($lab_match_applicant->fourth_fiveth_degrees  as $fourth_fiveth_degree) {
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Tipo: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $fourth_fiveth_degree->study_level_type_display,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Titulo obtenido: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $fourth_fiveth_degree->degree_name,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );
                }
            }

            $section->addText(
                'Certificación de competencias laborales',
                array(
                    'name' => 'Arial',
                    'size' => 14,
                    'bold' => true,
                    'color' => 'FFFFFF',
                    'shading' => array('fill' => '0033A0 ')
                ),
            );
            if ($lab_match_applicant->labour_certificate == 1) {
                foreach ($lab_match_applicant->competences_certificate as $certificate) {
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Descripción del certificado obtenido: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $certificate->certificate_description,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Año de certificación: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $certificate->certificate_year,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );
                    $textrun->addTextBreak();  // Salto de línea 
                }
            } else {
                $section->addText(
                    "N/A",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
            $section->addText(
                'Capacitaciones',
                array(
                    'name' => 'Arial',
                    'size' => 14,
                    'bold' => true,
                    'color' => 'FFFFFF',
                    'shading' => array('fill' => '0033A0 ')
                ),
            );
            if ($lab_match_applicant->training_last_years == 1) {
                foreach ($lab_match_applicant->last_training as $training) {
                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Tipo de capacitación o formación: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $training->c_trainning_type_display,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );

                    $textrun = $section->addTextRun();
                    $textrun->addText(
                        "Detalle de la capacitación: ",
                        array(
                            'size' => 12,
                            'bold' => true,
                            'color' => '000000',
                        )
                    );
                    $textrun->addText(
                        $training->training_description,
                        array(
                            'size' => 12,
                            'bold' => false,
                            'color' => '000000',
                        )
                    );
                    $textrun->addTextBreak();  // Salto de línea 

                }
            } else {
                $section->addText(
                    "N/A",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
            $section->addText(
                'Experiencia laboral',
                array(
                    'name' => 'Arial',
                    'size' => 14,
                    'bold' => true,
                    'color' => 'FFFFFF',
                    'shading' => array('fill' => '0033A0 ')
                ),
            );
            foreach ($lab_match_applicant->profesional_expirience as $experience) {
                $started_date = new DateTime($experience->started_date);
                $end_date = new DateTime($experience->end_date);

                // Formatear las fechas para obtener mes y año
                $started_month_year = $started_date->format('F Y');
                $end_month_year = $end_date->format('F Y');

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "$experience->position, $experience->company - $experience->country ($started_month_year - $end_month_year)",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "$experience->functions_description",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
                $textrun->addTextBreak();  // Salto de línea 
            }

            $section->addText(
                'Idiomas',
                array(
                    'name' => 'Arial',
                    'size' => 14,
                    'bold' => true,
                    'color' => 'FFFFFF',
                    'shading' => array('fill' => '0033A0 ')
                ),
            );
            foreach ($lab_match_applicant->languages  as $language) {
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "$language->language_display",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Nivel escrito: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $language->writting_level_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Nivel hablado: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $language->speaking_level_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
            $section->addText(
                'Habilidades',
                array(
                    'name' => 'Arial',
                    'size' => 14,
                    'bold' => true,
                    'color' => 'FFFFFF',
                    'shading' => array('fill' => '0033A0 ')
                ),
            );

            if ($lab_match_applicant->office_skills == 1) {
                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Habilidad en Excel: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->excel_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Habilidad en Word: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->word_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Habilidad en oneDrive: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->one_drive_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Habilidad en Outlook: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->outlook_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );

                $textrun = $section->addTextRun();
                $textrun->addText(
                    "Habilidad en Project: ",
                    array(
                        'size' => 12,
                        'bold' => true,
                        'color' => '000000',
                    )
                );
                $textrun->addText(
                    $lab_match_applicant->project_display,
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            } else {
                $section->addText(
                    "N/A",
                    array(
                        'size' => 12,
                        'bold' => false,
                        'color' => '000000',
                    )
                );
            }
        }

        $filename = TEMP_DIR . "\\CV-" . $lab_match_applicant->name . '.docx';
        $phpWord->save($filename);
        return basename($filename);
    }

    public function GenerateOneReport($id, $phpWord)
    {
    }
}
