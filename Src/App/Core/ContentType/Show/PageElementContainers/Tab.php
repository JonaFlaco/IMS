<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;

class Tab extends ElementContainer implements IElementContainerItem
{

    private string $title;
    private int $size;
    private ?string $prefix;

    public function __construct(string $title, int $size = 12, ?string $prefix = null)
    {
        $this->title = $title;
        $this->size = $size;
        $this->prefix = $prefix;
    }

    public function render(): string
    {

        $row = new Row();

        foreach ($this->getElements() as $el) {
            $row->addElement($el);
        }

        $result = $row->render();

        return $result;
    }

    public function getMachineName(): string
    {
        return get_machine_name($this->prefix . "_" . $this->title);
    }
    public function getTitle(): string
    {
        return $this->title;
    }
}
