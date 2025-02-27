<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;
use App\Core\ContentType\Add\VueData;

class Page extends ElementContainer implements IElementContainerItem
{

    public VueData $vueData;
    public TabContainer $tabContainer;

    public function __construct()
    {
        $this->vueData = new VueData();
        $this->tabContainer = new TabContainer();
    }

    public function render(): string
    {

        $renderResult = $this->getRenderElements();
        $renderVueData = $this->vueData->render();
        $tabContainerResult = $this->tabContainer->render();

        return <<<HTML
            <template id="tpl-main">
                <div>
                    $renderResult
                    $tabContainerResult
                </div>
            </div>
        </template> 
        HTML

        .

        <<<JS
        <script>
            var vm = new Vue({
                el: '#vue-cont',
                template: '#tpl-main',
                data: $renderVueData,
                mounted() {
                },
                methods: {
                },
            })
        </script>
        JS;


        
    }

}
