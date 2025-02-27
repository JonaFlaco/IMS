<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;

class Row extends ElementContainer implements IElementContainerItem
{

    private int $size;

    public function __construct(int $size = 12)
    {
        $this->size = $size;
    }

    public function render(): string
    {

        $renderResult = $this->getRenderElements();

        return <<<HTML
            <div class="row col-md-$this->size m-0 p-0">
                $renderResult
            </div>
        HTML;
    }
}
