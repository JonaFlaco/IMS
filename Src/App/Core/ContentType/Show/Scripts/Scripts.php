<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Scripts;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;

class Scripts extends ElementContainer implements IElementContainerItem
{

    public function render(): string
    {

        $renderResult = "";

        foreach ($this->getElements() as $el) {
            $renderResult .= $el->render();
        }

        return $renderResult;
    }
}
