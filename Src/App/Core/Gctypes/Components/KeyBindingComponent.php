<?php

namespace App\Core\Gctypes\Components;

use App\Core\Application;

class KeyBindingComponent {

    private $ctypeObj;
    private $isEditMode;

    public function __construct($ctypeObj, $isEditMode) {
        $this->ctypeObj = $ctypeObj;
        $this->isEditMode = $isEditMode;
    }

    public function generate() : ?string {
        
        ob_start(); ?>


        this._keyListener = function(e) {
            if (e.key === "s" && e.altKey && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();

                if($("#editJustificationModal").hasClass('show') != true){
                    this.postData();
                } else {
                    this.postDataAction();
                }
            }

            if (e.key === "m" && e.altKey && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();

                if($("#editJustificationModal").hasClass('show') != true){
                    this.postData(1);
                } else {
                    this.postDataAction(1);
                }
                
            }

            <?php if($this->isEditMode) : ?>
            if (e.key === "n" && e.ctrlKey && e.altKey) {
                e.preventDefault();
                this.addRecord();
                
            }
            <?php endif; ?>
        };
        document.addEventListener('keydown', this._keyListener.bind(this));

        <?php

        return ob_get_clean();


    }

    public function destroy() {
        ob_start(); ?>

        document.removeEventListener('keydown', this._keyListener);
        
        <?php

        return ob_get_clean();
    }


}