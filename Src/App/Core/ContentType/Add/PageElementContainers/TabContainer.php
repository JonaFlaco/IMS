<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;
use App\Core\ContentType\Add\VueData;

class TabContainer extends ElementContainer implements IElementContainerItem
{

    public function render(): string
    {

        $renderTabButtons = $this->renderTabButtons();
        $renderTabContents = $this->renderTabContents();

        return <<<HTML
            <div class="row">
                $renderTabButtons
                $renderTabContents
            </div>
        HTML;
    }

    private function renderTabButtons(): string
    {
        $renderResult = "";
        $i = 0;
        foreach ($this->getElements() as $el) {

            $renderResult .= sprintf(
                '
                <li class="nav-item">
                    <a href="#tab_%s" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 %s">
                        <i class="mdi mdi-home-variant d-md-none d-block"></i>
                        <span class="d-none d-md-block"> %s </span>
                    </a>
                </li>',
                $el->getMachineName(),
                ($i++ == 0 ? "active" : ""),
                $el->getTitle()
            );
        }

        return <<<HTML
            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                $renderResult
            </ul>
        HTML;
    }

    private function renderTabContents(): string
    {

        $renderResult = "";
        $i = 0;
        foreach ($this->getElements() as $el) {

            $renderResult .= sprintf(
                '<div class="tab-pane %s" id="tab_%s"> %s </div>',
                ($i++ == 0 ? "active" : ""),
                $el->getMachineName(),
                $el->render()
            );
        }

        return <<<HTML
            <div class="tab-content">
                $renderResult                
            </div>
        HTML;
    }

}
