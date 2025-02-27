<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class GtplFieldButtonHtml implements IElementContainerItem
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
            '<button class="dropdown-item" @click="run_%s()">%s</button>',
            $this->data->getName(),
            $this->data->getTitle()
        );
    }
}
