<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\PageHeaderElements;

use App\Core\Common\IElementContainerItem;

class PageHeaderTitle implements IElementContainerItem
{

    public function render(): string
    {

        return <<<HTML
        <page-title-row-component :title="pageTitle"></page-title-row-component>
        HTML;
    }
}
