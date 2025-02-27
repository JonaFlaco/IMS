<?php

/*
* This is base class for updating ODK preloadlist, contains some helper classes
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Exceptions\ForbiddenException;
use App\Models\CTypeLog;

class BaseSyncOdkPreloads extends BaseCron
{

    public $odkDb;
    private $cronObj;
    
    public function __construct($dbId = null)
    {
        parent::__construct();

        if($dbId != null)
            $this->odkDb = \App\Helpers\DbHelper::getMySQLDbObj($dbId);

        if (Application::getInstance()->user->isNotAdmin() && Application::getInstance()->request->isLocal() != true && Application::getInstance()->request->isCli() != true) {
            throw new ForbiddenException();
        }

    }

    public function syncFromArray($cron_id, $csv_file_name, $data, $header)
    {

        $value = "";

        $r = 0;
        foreach ($header as $item) {

            if ($r++ > 0) {
                $value .= ",";
            }
            $item = _str_replace("\n", " ", $item);
            $value .= $item;
        }

        $value .= "\n";


        foreach ($data as $row) {
            $r = 0;
            foreach ($row as $item) {

                if ($r++ > 0) {
                    $value .= ",";
                }
                $item = _str_replace("\n", " ", $item);
                $value .= $item;
            }

            $value .= "\n";
        }

        $this->Sync($cron_id, $csv_file_name, $value);

        if(sizeof($data) > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s record(s) updated", sizeof($data)));
        }
    }

    public function Sync($cron_id, $csv_file_name, $value)
    {

        if ($this->odkDb == null) {
            throw new \App\Exceptions\MissingDataFromRequesterException("Error: ODK database is not provided");
        }

        if (empty($csv_file_name)) {
            throw new \App\Exceptions\MissingDataFromRequesterException("CSV file name is not provided");
        }

        if (Application::getInstance()->request->getParam("show_dep") != 1) {

            $value = _str_replace("'", "", $value);
            $value = _str_replace("/", "", $value);
            $value = _str_replace("\\", "", $value);
            $value = _str_replace("`", "", $value);
            $value = _str_replace('"', "", $value);

            $hash = md5($value);
            $size = _strlen($value);

            $query = "  UPDATE 
                                        _form_info_manifest_ref ref 
                            left join   _form_info_manifest_blb blb on blb._URI = ref._SUB_AURI 
                            left join   _form_info_manifest_bin bn on bn._URI=ref._DOM_AURI 
                        SET 
                            CONTENT_LENGTH = :size,
                            CONTENT_HASH = :hash,
                            VALUE = :value
                        WHERE 
                            UNROOTED_FILE_PATH = :fileName;";

            $this->odkDb->query($query);
            $this->odkDb->bind(':size', $size);
            $this->odkDb->bind(':hash', "md5:$hash");
            $this->odkDb->bind(':value', $value);
            $this->odkDb->bind(':fileName', "$csv_file_name.csv");

            $this->odkDb->execute();

        } else {

            $query = "  select
                            f.FORM_ID AS form_name,
                            ref._CREATOR_URI_USER AS created_user,
                            ref._CREATION_DATE AS created_date
                        FROM _form_info_manifest_ref ref
                        LEFT JOIN _form_info f ON f._URI = ref._TOP_LEVEL_AURI
                        left join   _form_info_manifest_blb blb on blb._URI = ref._SUB_AURI 
                        left join   _form_info_manifest_bin bn on bn._URI=ref._DOM_AURI 
                        WHERE 
                            UNROOTED_FILE_PATH ='$csv_file_name.csv';
                            ";

            $this->odkDb->query($query);
            $result = $this->odkDb->resultSet();

            echo "<h1>Forms depend on $csv_file_name.csv</h1>";
            echo "<table class=\"table\"><thead><tr><th>Form</th><th>User</th><th>Date</th></tr></thead><tbody>";
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . e($row->form_name) . "</td>";
                echo "<td>" . e($row->created_user) . "</td>";
                echo "<td>" . e($row->created_date) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table><br><br>";
        }
    }



    public function index($id, $params = [])
    {
        $this->id = $id;

        $this->params = $params;

        $this->ukey = \App\Helpers\MiscHelper::randomString(25);

        Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "started", "Started");

        try {

            $this->run();

        } catch (\Exception $exc) {

            Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "failed", $exc->getMessage());

            throw $exc;
        }

        Application::getInstance()->coreModel->addCronLog($this->ukey, $id, "finished", "Cron ran successfuly");
    }
}
