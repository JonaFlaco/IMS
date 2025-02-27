<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\PageHeaderElements;

use App\Core\Common\IElementContainerItem;

class PageHeaderBreadCrumb implements IElementContainerItem
{

    public function render(): string
    {

        $homeTitle = t("Home");

        return <<<HTML
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right mt-0">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="/"> $homeTitle </a></li>
                                <li v-if="ctype.module.id" class="breadcrumb-item"><a :href="'/' + ctype.module.id"> {{ ctype.module.name }} </a></li>
                                <li class="breadcrumb-item"><a :href="'/' + ctype.id"> {{ ctype.name }} </a></li>
                                <li class="breadcrumb-item active"> {{ nodeData[ctype.display_field_name] }} </li>
                            </ol>
                        </div>
                        <h4 class="page-title"> {{ nodeData[ctype.display_field_name] }} </h4>
                    </div>
                </div>
            </div> 
        HTML;
    }
}
