<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;

class Group extends ElementContainer implements IElementContainerItem
{

    private string $title;
    private int $size;

    public function __construct(string $title, int $size = 12)
    {
        $this->title = $title;
        $this->size = $size;
    }

    public function render(): string
    {

        if ($this->isEmpty())
            return "";

        $title = t($this->title);
        $renderResult = $this->getRenderElements();

        return <<<HTML
            <div class="col-md-$this->size">
                <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-primary mb-3">$title</h4>
                    $renderResult
                </div>
                </div>
            </div>
        HTML;
    }
}
