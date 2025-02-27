<?php 

// namespace App\Crons;

// use App\Core\Crons\BaseSyncOdkPreloads;

// class SamplePreloadlist extends BaseSyncOdkPreloads {

//     private $cronObj;

//     public function __construct($cronObj){
        
//         $this->cronObj = $cronObj;
        
//         parent::__construct($this->cronObj->db_connection_string_id);
//     }

//     public function index($id,$params)
//     {

//         // $headers = array('eoi_nid','view_field'); //Headers
//         // $data = // Fun to retrive data

//         $this->syncFromArray($id, $this->cronObj->preload_list_name, $data, $headers);
        
//     }

// }