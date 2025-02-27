<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldComboboxMulti extends AddFieldCombobox
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }

    public function render(): string
    {
        return sprintf(
            '
            <p class="mb-0">
                %s
                <ul v-if="%s_display">
                    <li v-for="item in %s_display.split(\'\\n\')">
                        {{ item }}
                    </li>
                </ul>
            <p class="card-p"></p>',
            $this->getLabel(),
            $this->getDataPath(),
            $this->getDataPath()
        );
    }
}
