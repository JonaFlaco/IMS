<?php

/**
 * This classes handles $_SESSION. Get/Set/Remove
 */
namespace App\Core;

class Session {

    protected const FLASH_KEY = 'flash_message';

    public function __construct() {
        
    }

    public function set($key, $value) {
      session_start();
      $_SESSION[$key] = $value;
      session_write_close();
    }

    public function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public function remove($key) {
      session_start();
      unset($_SESSION[$key]);
      session_write_close();
    }

    public function exist($key) {
      $value = $_SESSION[$key] ?? null;
 
      return isset($value);
    }

    public function flash($name = '', $message = ''){

        $name = _strtolower($name);
        $return_value = "";
    
        if(!empty($message)){
    
          if($name == "flash_success" || $name == "flash_danger" || $name == "flash_warning" || $name == "flash_info"){
            if($this->get($name) == null){
              $this->set($name, array());
            }
    
    
            $arr = $this->get($name);
            array_push($arr, $message);
            $this->set($name, $arr);
            
          } 
          
        } elseif(empty($message)){
          
          if($this->get('flash_success') !== null){
            $return_value .= "<div class=\"alert alert-success alert-dismissible fade show \" id=\"msg-flash\">
            <button type=\"button\" class=\"btn-close\" data-dismiss=\"alert\" aria-label=\"Close\"></button>
            ";
    
    
            foreach($this->get('flash_success') as $itm){
              $return_value .= "<i class=\"dripicons-checkmark me-2\"></i>$itm<br>";
            }
          
            $return_value .= '</div>';
            $this->remove("flash_success");
          }
            
    
          if($this->get('flash_info') !== null){
            $return_value .= "<div class=\"alert alert-info alert-dismissible fade show \" id=\"msg-flash\">
            <button type=\"button\" class=\"btn-close\" data-dismiss=\"alert\" aria-label=\"Close\">
            </button>
            ";
    
    
            foreach($this->get('flash_info') as $itm){
              $return_value .= "<i class=\"dripicons-information  me-2\"></i>$itm<br>";
            }
          
            $return_value .= '</div>';
            $this->remove("flash_info");
          }
    
    
          if($this->get('flash_danger') !== null){
            $return_value .= "<div class=\"alert alert-danger alert-dismissible fade show \" id=\"msg-flash\">
            <button type=\"button\" class=\"btn-close\" data-dismiss=\"alert\" aria-label=\"Close\">
            </button>
            ";
    
    
            foreach($this->get('flash_danger') as $itm){
              $return_value .= "<i class=\"dripicons-wrong   me-2\"></i>$itm<br>";
            }
          
            $return_value .= '</div>';
            $this->remove("flash_danger");
          }
    
          if($this->get('flash_warning') !== null){
            $return_value .= "<div class=\"alert alert-warning alert-dismissible fade show \" id=\"msg-flash\">
            <button type=\"button\" class=\"btn-close\" data-dismiss=\"alert\" aria-label=\"Close\">
            </button>
            ";
    
    
            foreach($this->get('flash_warning') as $itm){
              $return_value .= "<i class=\"dripicons-warning    me-2\"></i>$itm<br>";
            }
          
            $return_value .= '</div>';
            $this->remove("flash_warning");
          }
    
        }
    
        return $return_value;
      }

}