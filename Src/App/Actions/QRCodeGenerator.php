<?php 

/**
 * This class generates QR Code
 * You will pass the text as text parameter and it will return the QR Code
 * 
 * /actions/QRCodeGenerator/?text=${PUT_THE_TEXT_HERE} => /actions/QRCodeGenerator/?text=RR-51400
 * 
 */

namespace App\Actions;

use App\Core\Controller;
use chillerlan\QRCode\QRCode; 

class QRCodeGenerator extends Controller {
    
    public function __construct(){
        parent::__construct();
    
        //Check if user is logged in or on local
        $this->app->user->checkAuthentication();
    }

    /**
     * index
     *
     * @param  string $id
     * @param  array $params
     * @return void
     */
    public function index($id = null, $params = []){

        $text = isset($params['text']) ? $params['text'] : null;
        
        if(_strlen($text) == 0){
            throw new \App\Exceptions\MissingDataFromRequesterException("Text is empty");
        }
        
        $options = new \chillerlan\QRCode\QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_JPG,
            'eccLevel'   => QRCode::ECC_L,
        ]);
        
        $qrcode = new QRCode($options);
        
        $file_name = sprintf("qr_code_%s.jpg", time());

        $file = TEMP_DIR . DS . $file_name;
        
        // and dump the output
        $qrcode->render($text,$file);

        header('Content-type: image/jpg');
        header('Content-Disposition: inline; filename=qr_code.jpg');
        header('Content-Length: ' . filesize($file));
        @readfile($file);

        unlink($file);
        
    }
}
