<?php

declare(strict_types=1);

namespace App\Core\Common;

use App\Models\CoreModel;
use App\Models\CType;

class CTypeLoader
{


    public static function load($id): ?CType
    {

        $result = CoreModel::getInstance()->getCtypes($id);
        
        if(!$result)
            return null;
        
        $result = new CType($result);

        return $result;
    }

}
