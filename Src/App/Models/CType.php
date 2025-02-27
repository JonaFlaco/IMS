<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\Common\CTypeFieldHelper;

class CType
{

    private string $id;
    private string $name;

    private ?string $viewId;
    private bool $isSystemObject;
    private string $categoryId;

    private ?string $governorateFieldName;
    private ?string $formTypeFieldName;
    private ?string $statusWorkflowTemplate;


    private bool $hasBeforeSaveTrigger;
    private bool $hasAfterSaveTrigger;
    private bool $hasBeforeUpdateStatusTrigger;
    private bool $hasAfterUpdateStatusTrigger;
    private bool $hasBeforeDeleteTrigger;
    private bool $hasAfterDeleteTrigger;

    private ?string $unitFieldName;
    private string $displayFieldName;

    private bool $disableAdd;
    private bool $disableDelete;
    private bool $disableEdit;
    private bool $disableRead;

    private bool $justificationForEditIsRequired;

    private bool $useGenericStatus;
    private bool $redirectAfterSave;
    private bool $useCustomTpl;
    private ?string $parentCtypeId;
    private bool $allowDragDropToSort;
    private bool $isDeleted;
    private ?string $description;
    private bool $isFieldCollection;


    private ?int $moduleId;
    private ?string $moduleIcon;
    private ?string $moduleName;
    private ?string $moduleCode;
    
    private int $statusId;

    private array $fields;

    private ?string $tplExtends;

    private $ctypePermissionObj;

    public function __construct($item)
    {

        $this->id = $item->id;
        $this->name = $item->name;
        $this->viewId = $item->view_id;
        $this->isSystemObject = (bool)$item->is_system_object;
        $this->categoryId = $item->category_id;
        $this->governorateFieldName = $item->governorate_field_name;
        $this->formTypeFieldName = $item->form_type_field_name;
        $this->statusWorkflowTemplate = $item->status_workflow_tempalate; //TODO: Fix typo
        $this->unitFieldName = $item->unit_field_name;
        $this->displayFieldName = $item->display_field_name ?? "id";
        $this->disableAdd = (bool)$item->disable_add;
        $this->disableDelete = (bool)$item->disable_delete;
        $this->disableEdit = (bool)$item->disable_edit;
        $this->disableRead = (bool)$item->disable_read;
        $this->justificationForEditIsRequired = (bool)$item->justification_for_edit_is_required;
        $this->useGenericStatus = (bool)$item->use_generic_status;
        $this->redirectAfterSave = (bool)$item->redirect_after_save;
        $this->useCustomTpl = (bool)$item->use_custom_tpl;
        $this->parentCtypeId = $item->parent_ctype_id;
        $this->allowDragDropToSort = (bool)$item->allow_drag_drop_to_sort;
        $this->description = $item->description;
        $this->isFieldCollection = (bool)$item->is_field_collection;
        $this->moduleId = (int)$item->module_id;
        $this->moduleIcon = $item->module_icon;
        $this->moduleName = $item->module_name;
        $this->moduleCode = $item->module_code;
        $this->statusId = (int)$item->status_id;

        $this->loadTplExtends();
    }

    public function getCtypePermission() {
        if(!isset($this->ctypePermissionObj))
            $this->ctypePermissionObj = Application::getInstance()->user->getCtypePermission($this->id);

        return $this->ctypePermissionObj;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getViewId() : ?string
    {
        return $this->viewId;
    }

    public function getIsSystemObject() : bool
    {
        return $this->isSystemObject;
    }

    public function getCategoryId() : string
    {
        return $this->categoryId;
    }

    public function getGovernorateFieldName() : ?string
    {
        return $this->governorateFieldName;
    }

    public function getFormTypeFieldName() : ?string
    {
        return $this->formTypeFieldName;
    }

    public function getStatusWorkflowTemplate() : ?string
    {
        return $this->statusWorkflowTemplate;
    }
    
    public function getHasBeforeSaveTrigger() : bool
    {
        return $this->hasBeforeSaveTrigger;
    }

    public function getHasAfterSaveTrigger() : bool
    {
        return $this->hasAfterSaveTrigger;
    }

    public function getHasBeforeUpdateStatusTrigger() : bool
    {
        return $this->hasBeforeUpdateStatusTrigger;
    }

    public function getHasAfterUpdateStatusTrigger() : bool
    {
        return $this->hasAfterUpdateStatusTrigger;
    }

    public function getHasBeforeDeleteTrigger() : bool
    {
        return $this->hasBeforeDeleteTrigger;
    }

    public function getHasAfterDeleteTrigger() : bool
    {
        return $this->hasAfterDeleteTrigger;
    }

    public function getUnitFieldName() : ?string
    {
        return $this->unitFieldName;
    }

    public function getDisplayFieldName() : ?string
    {
        return $this->displayFieldName;
    }

    public function getDisableAdd() : bool
    {
        return $this->disableAdd;
    }

    public function getDisableDelete(): bool
    {
        return $this->disableDelete;
    }

    public function getDisableEdit(): bool
    {
        return $this->disableEdit;
    }

    public function getDisableRead(): bool
    {
        return $this->disableRead;
    }

    public function getJustificationForEditIsRequired(): bool
    {
        return $this->justificationForEditIsRequired;
    }

    public function getUseGenericStatus(): bool
    {
        return $this->useGenericStatus;
    }

    public function getRedirectAfterSave(): bool
    {
        return $this->redirectAfterSave;
    }

    public function getUseCustomTpl(): bool
    {
        return $this->useCustomTpl;
    }

    public function getParentCtypeId(): ?string
    {
        return $this->parentCtypeId;
    }

    public function getAllowDragDropToSort(): bool
    {
        return $this->allowDragDropToSort;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsFieldCollection(): bool
    {
        return $this->isFieldCollection;
    }

    public function getModuleId(): ?int
    {
        return $this->moduleId;
    }

    public function getModuleIcon(): ?string
    {
        return $this->moduleIcon;
    }

    public function getStatusId(): ?int
    {
        return $this->statusId;
    }


    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function getModuleCode(): ?string
    {
        return $this->moduleCode;
    }



    public function loadFields(): CType
    {
        $this->fields = CTypeFieldHelper::loadByCtypeId($this->getId());
        return $this;
    }


    /**
     * @return CTypeField[]
     */
    public function getFields(): array
    {
        if(empty($this->fields))
            $this->loadFields();
            
        return $this->fields;
    }

    public function getTplExtends()
    {
        return $this->tplExtends;
    }
    
    private function loadTplExtends() {
        
        $this->tplExtends = null;

        $file =  APP_ROOT_DIR . DS . "Views" . DS . "CustomTpls" . DS . toPascalCase($this->getId()) . "Extends.php";

        if (!is_file($file)) {
            $file =  EXT_ROOT_DIR . DS . "Views" . DS . "CustomTpls" . DS . toPascalCase($this->getId()) . "Extends.php";
        }

        if (is_file($file)) {
            ob_start();
            require $file;
            $this->tplExtends = ob_get_clean();
        }

        return null;
    }
}
