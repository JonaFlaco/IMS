<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\UpdateStatusComponent;

use App\Core\Common\IElementContainerItem;

class UpdateStatusComponentMethods implements IElementContainerItem
{

    public function render(): string
    {

        return <<<JS

            updateStatus(){

                this.updateStatusItems = [];

                this.updateStatusItems.push({
                        id: this.nodeData.id, 
                        title: this.ctype.display_field_name ?? "id", 
                    });

            },
            afterUpdateStatus(item) {
                this.nodeData.status.id = item.status.id;
                this.nodeData.status.name = item.status.name;
                this.nodeData.status.style = item.status.style;
            },

        JS;
    }
}
