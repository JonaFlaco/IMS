<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldAttachmentMulti extends AddFieldAttachment
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }


    public function getPreview(): string
    {
        return sprintf(
            '
            <div class="ms-2 mt-1" v-if="%s">
                <p v-for="item in %s">
                    <a 
                        class="text-normal"
                        :href="\'/filedownload?ctype_id=%s&field_name=%s&size=orginal&file_name=\' + item.name" 
                        target="_blank">

                        <img height=32 width=32 
                            :alt="item.original_name" 
                            :src="getFileThumbnail(ctype.id, \'%s\', item.name)">
                        {{ item.original_name }}
                    </a>
                </p>
            </div>',
            $this->getDataPath(),
            $this->getDataPath(),
            $this->data->getParentName(),
            $this->data->getName(),
            $this->data->getName(),
        );
    }
}
