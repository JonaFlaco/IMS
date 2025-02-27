<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\UpdateStatusComponent;

use App\Core\Common\IElementContainerItem;

class UpdateStatusComponent implements IElementContainerItem
{

    public function render(): string
    {

        return <<<HTML
            <update-status-component 
                v-if="updateStatusItems.length > 0" 
                :ctype-id="ctype.id"
                :records="updateStatusItems"
                @clean-up="updateStatusItems = []"
                @after-update="afterUpdateStatus"
                >
            </update-status-component>
        HTML;
    }
}
