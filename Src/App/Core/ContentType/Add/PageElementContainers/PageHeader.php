<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;
use App\Core\ContentType\Add\PageHeaderElements\PageHeaderTitle;
use App\Models\CType;

class PageHeader extends ElementContainer implements IElementContainerItem
{

    public function __construct(CType $ctype)
    {

        $this->addElement(new PageHeaderTitle());

    }

}
