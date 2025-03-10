<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

abstract class ShowFieldAttachment extends ShowField implements IElementContainerItem
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }


    public function render(): string
    {
        return sprintf(
            '
            <div class="row">
                <div class="col-md-12 mt-1">
                    %s
                    %s
                </div>
            </div>
            <p class="card-p"></p>',
            $this->getLabel(),
            $this->getPreview(),
        );
    }

    protected abstract function getPreview();
}
