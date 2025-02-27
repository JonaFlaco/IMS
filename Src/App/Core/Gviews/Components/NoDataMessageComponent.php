<?php

namespace App\Core\Gviews\Components;

use App\Core\Application;

class NoDataMessageComponent {

    public function __construct() {
    }

    public function generate(){
        
        ob_start(); ?>

        <div class="mt-1 alert alert-secondary" v-if="is_loading != 1 && records && records.length == 0">
            <?= t("No data found, click filter or try other filtration") ?>
        </div>

        <?php

        return ob_get_clean();

    }

}