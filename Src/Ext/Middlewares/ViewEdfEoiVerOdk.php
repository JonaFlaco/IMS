<?php
 
namespace Ext\Middlewares;
 
use App\Core\Controller;
use App\Core\Application;
 
class ViewEdfEoiVerOdk extends Controller {
   
    public function __construct(){
        parent::__construct();
    }
 
    public function addExtraConditionToGetData(){
 
       
        return "edf_eoi.id not in (select business_id  from edf_eoi_verification where status_id != 3 ) and edf_eoi.status_id = 2";       
    }
 
   
}    