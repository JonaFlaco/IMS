<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\UpdateStatusComponent;

use App\Core\Common\IElementContainerItem;

class UpdateStatusComponentButton implements IElementContainerItem
{

    public function render(): string
    {

        $statusTitle = t("Status");
        $changeTitle = t("Change");

        return <<<HTML
            <div class="col-sm-6 mb-1">
                <div class="text-sm-end">
                    <span class="p-1" :class="nodeData.status.style">
                        $statusTitle: 
                        {{ nodeData.status.name }}
                        <a 
                            href="javascript: void(0);"
                            class="ms-2 hide_on_print"
                            @click="updateStatus()" 
                            >
                            <span class="text-white">
                                <strong>
                                    <i class="mdi mdi-format-list-bulleted"> </i> 
                                    $changeTitle
                                </strong>
                            </span>
                        </a>
                    </span>
                </div>
            </div>
        HTML;
    }
}
