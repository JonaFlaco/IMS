<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Application;
use App\Core\Common\IElementContainerItem;
use App\Models\CTypeFields\CTypeField;
use App\Models\CType;

class CtypeStatusAlert implements IElementContainerItem
{
    private CType $ctype;

    public function __construct(CType $ctype)
    {
        $this->ctype = $ctype;
    }

    public function render(): string
    {

        if($this->ctype->getStatusId() == 20) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type aun no ha sido publicado
                </div>
            HTML;

        } else if($this->ctype->getStatusId() == 72) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type ha sido archivado
                </div>
            HTML;

        } else if($this->ctype->getStatusId() == 83) {

            return <<<HTML
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Advertencia - </strong> Este Content-Type ha sido abandonado
                </div>
            HTML;

        }

        return "";
        
    }
}
