<?php

/*
* This is base class for updating ODK preloadlist, contains some helper classes
 */

namespace App\Core\Crons;

use App\Core\Application;
use App\Exceptions\ForbiddenException;
use App\Models\CTypeLog;

class SyncOdkPreloads extends BaseSyncOdkPreloads
{

    public $odkDb;
    private $cronObj;
    
    public function __construct($dbId)
    {
        parent::__construct($dbId);
    }

    public function run() //SyncFromGview
    {
        $this->cronObj = $this->coreModel->nodeModel("crons")
        ->id($this->id)
        ->loadFirstOrFail();
        
        $spreadsheet = (new \App\Core\Gviews\Export($this->cronObj->preload_list_view_id, ["returnTheFile" => true]))->main();

        $data = $spreadsheet->getSheet(0)->toArray();

        $value = "'";

        foreach ($data as $row) {
            $r = 0;
            foreach ($row as $item) {

                if ($r == sizeof($row) - 1) {
                    continue;
                }

                if(isset($item)){
					$item = preg_replace('~[\r\n]+~', ' ', $item);
				}	
                
                if ($r++ > 0) {
                    $value .= ",";
                }

                $value .= $item;
            }
            $value .= "\n";
        }

        $this->Sync($this->id, $this->cronObj->preload_list_name, $value);

        if(sizeof($data) > 0) {
            Application::getInstance()->coreModel->addCronLog($this->ukey, $this->id, "data_synced", sprintf("%s record(s) updated", sizeof($data)));
        }
    }

    
}
