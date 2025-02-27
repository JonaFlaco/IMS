<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class GtplFieldButtonScript implements IElementContainerItem
{

    private CTypeField $data;
    private CType $ctype;

    public function __construct(CType $ctype, CTypeField $data)
    {
        $this->ctype = $ctype;
        $this->data = $data;
    }

    public function render(): string
    {
        return sprintf(
            'run_%s(){
                window.open("/dataexport/exportindividual/" + this.recordId + "?ctype_id=%s", "_blank");  
            },',
            $this->data->getName(),
            $this->data->getParentId()
        );
    }
}
