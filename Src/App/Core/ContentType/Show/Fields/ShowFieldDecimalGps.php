<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class ShowFieldDecimalGps extends ShowFieldDecimal
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }


    protected function getRootName(): string
    {
        return substr($this->data->getName(), 0, _strlen($this->data->getName()) - 4);
    }

    protected function getLat(): string
    {
        return sprintf(
            '%s.%s_lat',
            $this->getDataObjectName(),
            $this->getRootName(),
        );
    }

    protected function getLng(): string
    {
        return sprintf(
            '%s.%s_lng',
            $this->getDataObjectName(),
            $this->getRootName(),
        );
    }
}
