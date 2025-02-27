<?php


function fnNumToWord($num){
        
    switch($num){
        case 1:
            return "first";
        case 2: 
            return "second";
        case 3:
            return "third";
        case 4:
            return "fourth";
        case 5:
            return "fifth";
        case 6:
            return "sixth";
        case 7:
            return "seventh";
        case 8:
            return "eighth";
        case 9:
            return "ninth";
        case 10:
            return "tenth";
        
    }
}

    

function prepareURL($url){

    //$url = _str_replace("[URLROOT]",URLROOT,$url);
    $url = _str_replace("[URLROOT]","",$url);

    return $url;
}

   
function generateMenuLeft($menu, $level){

        
    $return_value = "";

    if(isset($menu->sub_menu) && $menu->sub_menu != array() ){
    
        if($level > 1){
            $return_value .= "
            <li class=\"side-nav-item\">
                <a data-bs-toggle=\"collapse\" href=\"#" . get_machine_name($menu->name) . "\" aria-expanded=\"false\" aria-controls=\"" . get_machine_name($menu->name) . "\">
            
                    ";

                    if(_strlen($menu->code) > 0) {
                        $return_value .= "<img width=\"24\" height=\"24\" avatar=\"{$menu->code}\">";
                    }

                    $return_value .= "
                    <span> {$menu->name} </span>
                    <span class=\"menu-arrow\"></span>
                </a>
                <div class=\"collapse\" id=\"" . get_machine_name($menu->name) . "\">
                    <ul class=\"side-nav-" . fnNumToWord(++$level) . "-level\">

                ";

                foreach($menu->sub_menu as $sub){

                    $return_value .= generateMenuLeft($sub, $level);
                }
                $return_value .= "
                </ul>
                </div>   

            </li>
            ";
        } else {
            $return_value .= "
            <li class=\"side-nav-item\">
            ";

            if(empty($menu->url))
                $return_value .= "<a data-bs-toggle=\"collapse\" href=\"#" . get_machine_name($menu->name) . "\" aria-expanded=\"false\" aria-controls=\"" . get_machine_name($menu->name) . "\" class=\"side-nav-link\">";
            else 
                $return_value .= "<a href=\"" . $menu->url . "\" class=\"side-nav-link\">";

            $return_value .= "
                    <img width=\"24\" height=\"24\" avatar=\"" . (isset($menu->code) ? $menu->code : $menu->name) . "\">
                
                    <span> {$menu->name} </span>
                    <span class=\"menu-arrow\"></span>
                </a>
                <div class=\"collapse\" id=\"" . get_machine_name($menu->name) . "\">
                    <ul class=\"side-nav-" . fnNumToWord(++$level) . "-level\">

                ";

                foreach($menu->sub_menu as $sub){

                    $return_value .= generateMenuLeft($sub, $level);
                }
                $return_value .= "
                </ul>
                </div>  

            </li>
            ";
        }

    } else {
        
        if($level > 1){
            $return_value .= "
                <li>
                    <a href=" . (isset($menu) && isset($menu->url) ? prepareURL($menu->url) : "#") . ">
                    
                    ";
                    if(_strlen($menu->code) > 0) {
                        $return_value .= "<img width=\"24\" height=\"24\" avatar=\"{$menu->code}\">";
                    }
                    $return_value .= "
                        <span> {$menu->name} </span>
                    </a>
                </li>
                ";
        } else {
            $return_value .= "<li class=\"side-nav-item\">";

            if(empty($menu->url))
                $return_value .= "<a data-bs-toggle=\"collapse\" href=\"#" . get_machine_name($menu->name) . "\" aria-expanded=\"false\" aria-controls=\"" . get_machine_name($menu->name) . "\" class=\"side-nav-link\">";
            else 
                $return_value .= "<a href=\"" . $menu->url . "\" class=\"side-nav-link\">";

            $return_value .= "<img width=\"24\" height=\"24\" avatar=\"" . (isset($menu->code) ? $menu->code : $menu->name) . "\">";

                $return_value .= "<span> {$menu->name} </span> 
                </a>
                
            </li>
                ";
        }
    
    }

    return $return_value;
}



function generateMenuTop($menu, $level){
    
    $return_value = "";

    if(isset($menu->sub_menu) && $menu->sub_menu != array() ){
    
        if($level > 1){
            $return_value .= "
            <div class=\"dropdown\">
                <a class=\"dropdown-item dropdown-toggle arrow-none\" href=\"javascript: void(0);\" id=\"topnav-project\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    $menu->name <div class=\"arrow-down\"></div>
                </a>
                <div class=\"dropdown-menu\" aria-labelledby=\"topnav-$menu->name\">
                ";

                foreach($menu->sub_menu as $sub){

                    $return_value .= generateMenuTop($sub, $level);
                }
                $return_value .= "
                </div>
            </div>
            ";
        } else {
            $return_value .= "


            <li class=\"nav-item dropdown\">
                <a class=\"nav-link dropdown-toggle arrow-none\" href=\"javascript: void(0);\" id=\"topnav-" . _strtolower(_str_replace(" ","_",$menu->name)) . "\" role=\"button\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    <!--<img width=\"24\" height=\"24\" avatar=\"" . (isset($menu->code) ? $menu->code : $menu->name) . "\"> -->
                    ";
                    $return_value .= " $menu->name <div class=\"arrow-down\"></div>
                </a>
                <div class=\"dropdown-menu\" aria-labelledby=\"topnav-" . _strtolower(_str_replace(" ","_",$menu->name)) . "\">
                ";

                foreach($menu->sub_menu as $sub){

                    $return_value .= generateMenuTop($sub, ++$level);
                }
                $return_value .= "
                </div>
            </li>
            ";
        }

    } else {
        
        if($level > 1){
            $return_value .= "
            <a href=\"" . (isset($menu) && isset($menu->url) ? prepareURL($menu->url) : "#") . "\" class=\"dropdown-item\">";
                if(isset($menu->icon) && _strlen($menu->icon) > 0){
                    $return_value .= "<img height=\"" . MENU_ICON_SIZE . "\" width=\"" . MENU_ICON_SIZE . "\" src=\"$menu->icon\">";
                }
                $return_value .= (isset($menu) && isset($menu->name) ? prepareURL($menu->name) : "") . "
            </a>
                ";
        } else {
            $return_value .= "
            <li class=\"nav-item dropdown\">
                <a class=\"nav-link dropdown-toggle arrow-none\" href=\"" . prepareURL($menu->url) . "\" id=\"topnav-" . _strtolower(_str_replace(" ","_",$menu->name)) . "\" role=\"button\">
                    <!-- <i class=\"mdi mdi-speedometer me-1\"></i>  -->";
                    if(isset($menu->icon) && _strlen($menu->icon) > 0){
                        $return_value .= "<img height=\"" . MENU_ICON_SIZE . "\" width=\"" . MENU_ICON_SIZE . "\" src=\"$menu->icon\">";
                    }
                    $return_value .= " $menu->name
                </a>
            </li>
                ";
        }
    
    }

    return $return_value;
}

