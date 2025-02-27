<?php

/**
 * This class contains some helpers methods for Git
 */


namespace App\Core;

class Git {

    //This method gets current branch name
    public function getCurrentBranch(){
        
        if(!is_file(ROOT_DIR . '/.git/HEAD')){
            return "N/A";
        }
        
        $stringfromfile = file(ROOT_DIR . '/.git/HEAD', FILE_USE_INCLUDE_PATH);

        $firstLine = $stringfromfile[0]; //get the string from the array

        $explodedstring = _explode("/", $firstLine, 3); //seperate out by the "/" in the string

        $branchname = "N/A";
        if(sizeof($explodedstring) > 2) {
            $branchname = $explodedstring[2]; //get the one that is always the branch name
        }

        return _trim($branchname);
    }


}