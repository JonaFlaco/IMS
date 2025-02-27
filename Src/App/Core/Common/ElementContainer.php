<?php

declare(strict_types=1);

namespace App\Core\Common;

use App\Core\Common\IElementContainerItem;

class ElementContainer
{

    /**
     * @var IElementContainerItem[]  
     */
    private array $elements = [];

    public function render(): string
    {
        return $this->getRenderElements();
    }

    protected function getRenderElements(): string
    {
        $result = "";

        foreach ($this->getElements() as $el) {
            $result .= $el->render();
        }

        return $result;
    }

    public function addElement(IElementContainerItem $item): void
    {
        $this->elements[] = $item;
    }

    /**
     * @return IElementContainerItem[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function getElementsCount(): int
    {
        return sizeof($this->elements);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }
}
