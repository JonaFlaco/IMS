<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldBoolean extends AddField implements IElementContainerItem
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }

    protected function renderDisplayData(): string
    {
        return sprintf(
            '<span> {{ %s ? "Yes" : "No" }} </span>',
            $this->getDataPath()
        );
    }
}
