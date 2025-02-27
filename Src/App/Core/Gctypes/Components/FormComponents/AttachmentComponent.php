<?php

namespace App\Core\Gctypes\Components\FormComponents;

use App\Core\Gctypes\Ctype;

class AttachmentComponent extends BaseComponent {

    private $ctypeObj;
    public function __construct(object $mainCtypeObj, object $field,bool $isEditMode = false, bool $isSurvey = false, ?string $prefix = null) {
        parent::__construct($mainCtypeObj, $field, $isEditMode, $isSurvey, $prefix);
     
        $this->ctypeObj = (new Ctype)->load($field->parent_id);

    }

    public function generate() : ?string {

        $fieldFullName = $this->field->ctype_id . "_" . $this->field->name;
        ob_start(); ?>
                        
        <div 
            <?= $this->dependancy() ?> 
            class="col-md-<?= $this->field->size ?> pt-1 pb-1" 
            id="div_<?= $this->uniqueName() ?>">
            <label for="<?= $this->uniqueName() ?>" class="form-label <?= ($this->field->is_deprecated ? "text-line-through" : "") ?>">
                <?= $this->field->title . $this->requiredSign() ?>
                <?= $this->addDeprecatedFlag() ?>
                <span v-if="uploading_<?= $this->uniqueName() ?>" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </label>
            <input type="file" class="form-control"  
                <?= ($this->field->is_multi != true ? "v-if='" . $this->dataPath() . " == null || " . $this->dataPath() . ".length == 0'" : "") ?>
                :required="<?= $this->isRequired() ?>"
                ref="<?= $this->uniqueName() ?>" 
                id="<?= $this->uniqueName() ?>" 
                name="<?= $this->uniqueName() ?>" 
                v-on:input="validate"
                :disabled="<?= $this->isReadOnly() ?> || uploading_<?= $this->uniqueName() ?> == 1"
                v-on:change="prepareToUpload_<?= $fieldFullName ?>()" 
                <?= $this->field->is_multi == true ? "multiple" : "" ?>
            >
            <?= $this->descriptionPanel() ?>

            <div class="card-body pb-0 ps-0 pe-0 pt-1">

                <div class="row col-md-12 m-0 p-0">
                   
                    <div v-for="im in <?= $this->dataPath() ?>" class="col-md-12 p-0">
                    
                        <div class="card mb-1 mt-1 shadow-none border ps-2 pe-2">
                            <div class="p-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img @click="previewImage('<?= $this->field->ctype_id ?>','<?= $this->field->name ?>',im.name)" style="cursor:pointer;" v-if="im.extension == 'png' || im.extension == 'jpg' || im.extension == 'jpeg' || im.extension == 'gif'" 
                                            :src="'/filedownload?ctype_id=<?= $this->field->ctype_id ?>&field_name=<?= $this->field->name ?>&size=small&file_name=' + im.name" class="avatar-sm rounded" alt="file-image">
                                        <img v-else-if="im.extension == 'doc' || im.extension == 'docx'" src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                        <img v-else-if="im.extension == 'txt'" src="/assets/app/images/icons/doc.svg" class="avatar-sm rounded" alt="file-image">
                                        <img v-else-if="im.extension == 'xls' || im.extension == 'xlsx'" src="/assets/app/images/icons/xls.svg" class="avatar-sm rounded" alt="file-image">
                                        <img v-else-if="im.extension == 'pdf'" src="/assets/app/images/icons/pdf.svg" class="avatar-sm rounded" alt="file-image">

                                        <div v-else class="avatar-sm">
                                            <span class="avatar-title bg-primary-lighten text-primary rounded">
                                                .{{im.extension ? im.extension.toUpperCase() : 'N/A'}}
                                            </span>
                                        </div>

                                    </div>

                                    <div class="col ps-0">
                                        <a v-if="im.extension == 'png' || im.extension == 'jpg' || im.extension == 'jpeg' || im.extension == 'gif'" @click="previewImage('<?= $this->field->ctype_id ?>','<?= $this->field->name ?>',im.name)" href="javascript: void(0);" class="text-muted font-weight-bold">{{im.original_name}}</a>
                                        <a v-else :href="'/filedownload?ctype_id=<?= $this->field->ctype_id ?>&field_name=<?= $this->field->name ?>&file_name=' + im.name" class="text-muted font-weight-bold">{{im.original_name}}</a>
                                        <p class="mb-0">{{Number(im.size / 1024).toFixed(2)}} KB</p>
                                    </div>
                                    <div class="col-auto">
                                        
                                        <a target="_blank" :href="'/filedownload?ctype_id=<?= $this->field->ctype_id ?>&field_name=<?= $this->field->name ?>&file_name=' + im.name" class="btn btn-link btn-lg text-muted">
                                            <i class="dripicons-download"></i>
                                        </a>
                                        <button class="btn btn-link btn-lg text-muted" 
                                            :disabled="<?= $this->isReadOnly() ?>" 
                                            v-on:click="remove_<?= $fieldFullName ?>(im.name)">
                                            <i class="dripicons-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>
  
        <?php

        return ob_get_clean();

    }


}

