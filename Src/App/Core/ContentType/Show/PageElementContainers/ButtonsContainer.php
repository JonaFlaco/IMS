<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;

class ButtonsContainer extends ElementContainer implements IElementContainerItem
{

    public function render(): string
    {

        if ($this->isEmpty())
            return "";

        $renderResult = $this->getRenderElements();

        return <<<HTML

            <div class="btn-group mb-2">
                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu">
                    $renderResult
                </div>
            </div>
        HTML;
    }
}
