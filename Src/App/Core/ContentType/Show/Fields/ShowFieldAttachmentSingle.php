<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class ShowFieldAttachmentSingle extends ShowFieldAttachment
{

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);
    }


    public function getPreview(): string
    {
        return sprintf(
            '
            <div >
                <p class="ms-2 mt-1" v-if="%s_original_name">
                    <a 
                        class="text-dark"
                        :href="\'/filedownload?ctype_id=%s&field_name=%s&size=orginal&file_name=\' + %s_name" 
                        target="_blank">

                        <img height=32 width=32 
                            :alt="%s_original_name" 
                            :src="getFileThumbnail(\'%s\', \'%s\', %s_name)">
                        {{ %s_original_name }}
                    </a>
                </p>
            </div>',
            $this->getDataPath(),
            $this->data->getParentId(),
            $this->data->getName(),
            $this->getDataPath(),
            $this->getDataPath(),
            $this->data->getParentId(),
            $this->data->getName(),
            $this->getDataPath(),
            $this->getDataPath()
        );
    }
}
