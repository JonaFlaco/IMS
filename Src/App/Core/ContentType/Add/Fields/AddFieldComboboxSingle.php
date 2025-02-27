<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldComboboxSingle extends AddFieldCombobox
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }

    public function renderDisplayData(): string
    {

        return sprintf(
            '<span> {{ %s_display }} </span>',
            $this->getDataPath()
        );
    }
}
