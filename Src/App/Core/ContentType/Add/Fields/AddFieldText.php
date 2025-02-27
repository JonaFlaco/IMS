<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldText extends AddField implements IElementContainerItem
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }


    public function render() : string {
        
        $result = "";

        ob_start()?>

        <div 
            
            >
            <label class="form-label"> <?= $this->getTitle() ?> </label>
            <input type="text" class="form-control" 
                v-model="<?= $this->getDataPath() ?>" 
                >
                

        </div>
        
        <?php

        $result = ob_get_clean();

        return $result;
    }

}
