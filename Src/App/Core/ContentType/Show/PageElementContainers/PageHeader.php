<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageElementContainers;

use App\Core\Common\ElementContainer;
use App\Core\Common\IElementContainerItem;
use App\Core\ContentType\Show\PageHeaderElements\CtypeStatusAlert;
use App\Core\ContentType\Show\PageHeaderElements\PageHeaderBreadCrumb;
use App\Core\ContentType\Show\PageHeaderElements\PageHeaderTitle;
use App\Core\ContentType\Show\UpdateStatusComponent\UpdateStatusComponentButton;
use App\Models\CType;

class PageHeader extends ElementContainer implements IElementContainerItem
{

    private CType $ctype;

    public function __construct(CType $ctype)
    {
        
        
        $this->ctype = $ctype;

        $this->addElement(new PageHeaderTitle($this->ctype));

        if ($ctype->getUseGenericStatus())
            $this->addElement(new UpdateStatusComponentButton());
    }

    public function render(): string
    {
        $breadCrumb = (new PageHeaderBreadCrumb())->render();
        $ctypeStatusAlert = (new CtypeStatusAlert($this->ctype))->render();

        $renderResult = $this->getRenderElements();

        return <<<HTML
            
            $breadCrumb
            
            $ctypeStatusAlert

            <div class="row">
                <div class="col-lg-12 pt-3">
                    <div class="card">
                        <div class="card-body bg-primary text-white">
                            <div class="row">
                                $renderResult
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        HTML;
    }
}
