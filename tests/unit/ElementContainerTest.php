<?php

namespace Tests\Unit;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;
use PHPUnit\Framework\TestCase;

class ElementContainerTest extends TestCase
{
    /**
     * @test
     */
    public function ElementContainer_getElement_Test(): void
    {
        $container = new ElementContainer();

        $item = $this->createMock(IElementContainerItem::class);

        $size = 5;
        $elements = [];
        for ($i = 0; $i < $size; $i++) {
            $elements[] = $item;
            $container->addElement($item);
        }

        $this->assertEquals($elements, $container->getElements());
    }

    /**
     * @test
     */
    public function ElementContainer_Count_Test(): void
    {
        $container = new ElementContainer();

        $item = $this->createMock(IElementContainerItem::class);

        $size = 5;
        for ($i = 0; $i < $size; $i++) {
            $container->addElement($item);
        }


        $this->assertEquals($size, $container->getElementsCount());
    }

    /**
     * @test
     */
    public function ElementContainer_IsEmpty_Test(): void
    {
        $container = new ElementContainer();

        $this->assertTrue($container->isEmpty());

        $item = $this->createMock(IElementContainerItem::class);

        $container->addElement($item);

        $this->assertFalse($container->isEmpty());
    }

    /**
     * @test
     */
    public function ElementContainer_Render_Test(): void
    {
        $renderResult = "[Result]";

        $container = new ElementContainer();

        $item = $this->createMock(IElementContainerItem::class);

        $item->method("render")->willReturn($renderResult);

        $size = 5;
        for ($i = 0; $i < $size; $i++) {
            $container->addElement($item);
        }


        $this->assertEquals(str_repeat($renderResult, $size), $container->render());
    }
}
