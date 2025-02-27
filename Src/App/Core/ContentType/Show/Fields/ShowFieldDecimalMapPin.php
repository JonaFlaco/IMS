<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class ShowFieldDecimalMapPin extends ShowFieldDecimalGps
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }

    protected function renderDisplayData(): string
    {
        return sprintf(
            '
            <span v-if="%s && %s"> 
                {{ %s }}, {{ %s }} 
                <a :href="\'http://www.google.com/maps/place/\' + %s + \',\' + %s" target="_blank"> 
                    <i v-tooltip="\'See on Map\'" class="text-primary mdi mdi-google-maps"></i> 
                </a> 
            </span>
            <span v-else>
                N/A
            </span>',
            $this->getLat(),
            $this->getLng(),
            $this->getLat(),
            $this->getLng(),
            $this->getLat(),
            $this->getLng()
        );
    }
}
