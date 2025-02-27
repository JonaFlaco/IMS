<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldDecimalMap extends AddFieldDecimalGps
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
                <gps-map-component
                    name="%s"
                    :lat="%s"
                    :lng="%s"
                    ></gps-map-component>
            <p class="card-p"></p>',
            $this->getLabel(),
            $this->getRootName(),
            $this->getLat(),
            $this->getLng()
        );
    }
}
