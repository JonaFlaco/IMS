<?php

namespace App\Core\Gviews\Components;

class KeyBindsComponent {

    public function __construct() {
        
    }

    public function generate(){
        
        ob_start(); ?>

        this._keyListener = function(e) {
            if (e.key === "s" && e.altKey && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();

                this.filter(); 
            }

            if (e.key === "t" && e.altKey && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();

                $('.collapse').collapse("toggle")
            
            }

            if (e.key === "n" && e.altKey && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();

                this.addRecord()
                
            }

        };

        document.addEventListener('keydown', this._keyListener.bind(this));


        <?php

        return ob_get_clean();
    }

}