<?php 

namespace App\InternalApi;

use \App\Core\BaseInternalApi;
use App\Core\MarkDown as CoreMarkDown;
use PHP_CodeSniffer\Generators\Markdown;

class HelpParseMd extends BaseInternalApi {
    
    public function __construct(){
        parent::__construct();
    }

    public function index($id, $params = []){
        
        $text = $_POST['text'];

        $data = \App\Core\MarkDown::parse($text);
        
        $result = (object)[
            "status" => "success",
            "result" => $data
        ];

        return_json($result);

        
    }
}
