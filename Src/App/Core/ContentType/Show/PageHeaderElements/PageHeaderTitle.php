<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Application;
use App\Core\Common\IElementContainerItem;
use App\Models\CType;

class PageHeaderTitle implements IElementContainerItem
{
    private CType $ctype;

    public function __construct(CType $ctype)
    {
        $this->ctype = $ctype;
        
    }

    public function render(): string
    {

        $recordTitle = t("Record");
        $logTitle = t("Show Log");
        $allow_view_log = $this->ctype->getCtypePermission()->allow_view_log;
       
       return <<<HTML
            <div class="col-sm-6 mb-2">
                <h4 class="header-title"> {{ ctype.name }} </h4>
            </div>
            <div class="col-sm-6 mb-2">
                <div class="text-sm-end">
                    <a v-if="nodeData.created_user_id_display" target="_blank" class="text-white" :href="'/users/show/' + nodeData.created_user_id">
                        <i class="mdi mdi-account"></i>
                        {{ nodeData.created_user_id_display }}
                    </a>
                    <span class="mx-1">&#183;</span>
                    <i v-if="nodeData.created_date" class="mdi mdi-calendar"></i>
                    {{ nodeData.created_date | formatDate }}

                    
                    <button v-if="<?= $allow_view_log ?>" @click="showLog" class="btn btn-link text-white">
                        <i class="mdi mdi-format-list-bulleted-triangle"></i>
                        $logTitle
                    </button>
                </div>

            </div>
            <div class="col-sm-6 mb-1">
                $recordTitle: {{ nodeData[ctype.display_field_name] }}
            </div>
            
        HTML;
    }
}
