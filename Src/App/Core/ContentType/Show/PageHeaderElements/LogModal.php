<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Application;
use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class LogModal implements IElementContainerItem
{

    private CType $ctype;

    public function __construct(CType $ctype)
    {
        $this->ctype = $ctype;
    }

    public function render(): string
    {
        
        $token = Application::getInstance()->csrfProtection->create("ctypes_logs");
        $logTitle = t("Log");
        $closeTitle = t("Cerrar");

        return <<<HTML

        <div id="logModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dark-header-modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-full-width modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-dark">
                        <h4 class="modal-title" id="dark-header-modalLabel"> $logTitle </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        
                        <log-component 
                            v-if="loadLog"
                            :ctype-id="ctypeId" 
                            :content-id="recordId"
                            csrf-token="$token"
                            >
                        </log-component>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">$closeTitle</button>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
    
}
