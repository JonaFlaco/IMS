<?php 

/*
 * This controller use to create actions
 * can be access as /actions/${FUN_NAME}
 */


namespace App\Core;

use App\Core\Controller;
use App\Core\Application;
use App\Core\Node;
use App\Models\NodeModel;

class BgTask {

    private $id;
    private array $postData = [];
    private ?string $completionDate = null;
    private ?string $name = null;
    private ?string $action_name = null;
    private ?string $main_value = null;
    public $created_user_id = null;

    private ?bool $send_email_on_completion = false;
    
    private ?string $output_file_name = null;
    private ?string $output_file_original_name = null;
    private ?string $output_file_size = null;
    private ?string $output_file_type = null;
    private ?string $output_file_extension = null;

    private ?string $input_file_name = null;
    private ?string $input_file_original_name = null;
    private ?string $input_file_size = null;
    private ?string $input_file_type = null;
    private ?string $input_file_extension = null;

    public function __construct($task_id = null){

        if(!empty($task_id)) {
            $task = Application::getInstance()->coreModel->nodeModel("bg_tasks")
            ->id($task_id)
            ->loadFirst();

                
            if(Application::getInstance()->request->isCli()) {
                $user = Application::getInstance()->coreModel->nodeModel("users")->id($task->created_user_id)->fields(["id", "name","email"])->loadFirstOrDefault();

                Application::getInstance()->user->switchToUser($user->name);
            }
            
            
            $this->id = $task->id;
            $this->name = $task->name;
            $this->action_name = $task->action_name;
            $this->main_value = $task->main_value;
            $this->created_user_id = $task->created_user_id;

            $this->send_email_on_completion = $task->send_email_on_completion;
            
            $this->output_file_name = $task->output_file_name;
            $this->output_file_original_name = $task->output_file_original_name;
            $this->output_file_size = $task->output_file_size;
            $this->output_file_type = $task->output_file_type;
            $this->output_file_extension = $task->output_file_extension;

            $this->input_file_name = $task->input_file_name;
            $this->input_file_original_name = $task->input_file_original_name;
            $this->input_file_size = $task->input_file_size;
            $this->input_file_type = $task->input_file_type;
            $this->input_file_extension = $task->input_file_extension;

            $this->postData = (array)json_decode($task->post_data);

        }
    }

    
    
    public function setPostData(array $value){
        $this->postData = $value;
        return $this;
    }

    public function setCompletionDate(\DateTime $value){
        $this->completionDate = $value;
        return $this;
    }

    public function setName(string $value){
        $this->name = $value;
        return $this;
    }

    public function setActionName(string $value){
        $this->action_name = $value;
        return $this;
    }
 
    
    public function setMainValue(string $value){
        $this->main_value = $value;
        return $this;
    }

    public function getPostData(){
        return $this->postData;
    }

    public function getCompletionDate(){
        return $this->completionDate;
    }

    public function getName(){
        return $this->name;
    }

    public function getActionName(){
        return $this->action_name;
    }
 
    
    public function getMainValue(){
        return $this->main_value;
    }
 
    public function hasInputFile() {
        return isset($this->input_file_name);
    }
    
    public function getInputFileName(){
        return $this->input_file_name;
    }
    
    public function addToQueue($run_now = true) {

        $data = new Node("bg_tasks");
        $data->name = $this->name;
        $data->post_data = json_encode($this->postData);
        $data->action_name = $this->action_name;
        $data->main_value = $this->main_value;
        $data->input_file_name = $this->input_file_name;
        $data->input_file_original_name = $this->input_file_original_name;
        $data->input_file_extension = $this->input_file_extension;
        $data->input_file_type = $this->input_file_type;
        $data->input_file_size = $this->input_file_size;
        
        $this->id = $data->save();
        
        if($run_now)
            self::execInBg($this->id);
        
    }

    public static function execInBg($task_id) {
        //Run the task in background
        $ims_file = ROOT_DIR . DS . "console" . DS . "ims.php";
        $cmd = "php \"$ims_file\" run_bg_tasks " . $task_id;
        
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r")); 
        }
        else {
            exec($cmd . " > /dev/null &");  
        }
    }


    private function getClass() {
        $className = toPascalCase($this->action_name);

        $classToRun = sprintf('\App\BgTaskHandlers\%s', $className);
        if(!class_exists($classToRun)){
            $classToRun = sprintf('\Ext\BgTaskHandlers\%s', $className);
        }
        if(class_exists($classToRun)){
            return $classToRun;
        }

        return null;

        
    }

    public static function handleError($task_id, $exc) {
        
        $data = new Node("bg_tasks");
        $data->id = $task_id;
        $data->last_error = $exc->getMessage();
        $data->status_id = 73;
        $data->save();

        \App\Core\ErrorHandler::handle($exc);
    }

    public function run() {
       
        $classToRun = $this->getClass();
        if($classToRun){
            $classObj = new $classToRun($this);

            $this->beforeRun();

            $outputFileName = $classObj->run();

            $this->afterCompletion($outputFileName);

        } else {
            throw new \App\Exceptions\NotFoundException("Class not found: " . $classToRun);
        }

    }

    private function beforeRun() {
        
        $data = new Node("bg_tasks");
        $data->id = $this->id;
        $data->output_file_name = null;
        $data->output_file_original_name = null;
        $data->output_file_extension = null;
        $data->output_file_type = null;
        $data->output_file_size = null;
        $data->status_id = 28;
        $data->completion_date = null;
        $data->start_date = (new \DateTime())->format('d/m/Y H:i:s');
        $data->last_error = null;
        $data->save();
        
    }

    private function afterCompletion($outputFileName) {
       
        $this->completionDate = (new \DateTime())->format('d/m/Y H:i:s');
        if(!empty($outputFileName)) {
            $this->SetOutputFile($outputFileName);
        }
        
        $this->saveOutput();

        $classToRun = $this->getClass();
        if($classToRun){
            $classObj = new $classToRun($this);

            $classObj->afterCompletion();

        } else {
            throw new \App\Exceptions\NotFoundException("Class not found: " . $classToRun);
        }

    }



    private function SetOutputFile($outputFileName) {

        $info = pathinfo($outputFileName);

        $this->output_file_name = $info["basename"];
        $this->output_file_original_name = $info["basename"];
        $this->output_file_extension = $info["extension"];
        $this->output_file_type = mime_content_type($outputFileName);
        $this->output_file_size = filesize($outputFileName);

    }


    private function saveOutput() {
        $data = new Node("bg_tasks");
        $data->id = $this->id;
        $data->output_file_name = $this->output_file_name;
        $data->output_file_original_name = $this->output_file_original_name;
        $data->output_file_extension = $this->output_file_extension;
        $data->output_file_type = $this->output_file_type;
        $data->output_file_size = $this->output_file_size;
        $data->status_id = 22;
        $data->completion_date = $this->completionDate;
        $data->last_error = null;
        $data->save();
    }

    public function setInputFile($file) {
        $old_file_name = $file['name'];
        
        $file_name = sprintf("%s %s %s %s.xlsx",
            date("yy.m.d"),
            Application::getInstance()->user->getFullName(),
            $old_file_name,
            time()
        );

        //Check if temp folder exist, if not create it
        if(!file_exists(UPLOAD_DIR_FULL . DS . "bg_tasks")){
            mkdir(UPLOAD_DIR_FULL . DS . "bg_tasks", 0777, true);
        }
        
        //now double check if temp folder exist, maybe the web app user does not have permission to create folder
        if(!file_exists(UPLOAD_DIR_FULL . DS . "bg_tasks")){
            throw new \App\Exceptions\UnableToCreateFileException("Unable to create temp folder inside Uploaded files dir");
        }

        //create full path for destination file
        $dest_path = UPLOAD_DIR_FULL . DS . "bg_tasks" . DS . $file_name;

        $input_file_name = $file['tmp_name'];
        if(substr(_strtolower($input_file_name), 0, _strlen(PHP_UPLOAD_TMP_FOLDER)) !== _strtolower(PHP_UPLOAD_TMP_FOLDER)){
            throw new \App\Exceptions\FileOperationFailedException("Invalid file path");
        }

        //move the file to destination folder
        if(!move_uploaded_file($input_file_name , $dest_path)){
            throw new \App\Exceptions\FileOperationFailedException("unable to copy file to temp folder");
        }

        $info = pathinfo($dest_path);

        $this->input_file_name = $info["basename"];
        $this->input_file_original_name = $info["basename"];
        $this->input_file_extension = $info["extension"];
        $this->input_file_type = mime_content_type($dest_path);
        $this->input_file_size = filesize($dest_path);

        $this->saveOutput();

    }

}