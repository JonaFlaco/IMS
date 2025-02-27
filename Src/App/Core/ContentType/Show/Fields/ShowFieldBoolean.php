<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class ShowFieldBoolean extends ShowField implements IElementContainerItem
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }

    protected function renderDisplayData(): string
    {
        return sprintf(
            "<span> {{ %s == true ? '%s' : '%s' }} </span>",
            $this->getDataPath(),
            t("Si"),
            t("No")
        );
    }
}
