<?php

declare(strict_types = 1);

namespace App\Models\CTypeFields;


class CTypeField implements ICTypeField {
    
    protected string $id;
    protected string $name;
    protected string $title;
    
    protected string $parentId;
    protected string $parentName;
    protected string $parentTitle;

    protected string $fieldTypeId;
    protected string $fieldTypeName;
    
    protected bool $isMulti;
    protected int $sort;
    
    protected bool $isPrimaryColumn;
    
    protected string $tabName;
    protected int $tabSort;
    protected string $groupName;
    protected int $groupSort;
    protected string $location;
    protected int $size;

    protected bool $isFieldCollection;
    protected ?string $fcName;

    protected string $strLength;
    protected ?string $method;
    
    protected bool $useParentPermissions;
    
    
    protected bool $hideInFcSummary;
    protected bool $isSystemField;
    
    protected ?string $description;
    
    protected ?string $fileTypeId;
    protected ?string $fileTypeExtension;

    protected bool $allowBasicHtmlTags;
    protected bool $isUnique;

    protected ?string $deleteRule;

    protected bool $isRequired;
    

    protected ?string $dependencies;
    protected ?string $readOnlyCondition;
    protected ?string $requiredCondition;
    protected ?string $validationPattern;
    protected ?string $validationCondition;
    protected ?string $validationMessage;
    
    protected ?string $appearanceId;
    
    protected ?string $dataSourceId;
    protected ?string $dataSourceTableName;
    protected ?string $dataSourceDisplayColumn;
    protected ?string $dataSourceValueColumn;
    protected ?string $dataSourceSortColumn;
    protected ?string $dataSourceFixedWhereCondition;
    protected ?string $dataSourceFromString;
    protected bool $dataSourceValueColumnIsText;
    protected ?string $dataSourceFilterByFieldName;
    protected ?string $dataSourceFilterByFieldNameInDb;

    protected ?string $defaultValue;
    protected ?string $defaultValueUpdated;

    protected bool $isReadOnly;
    protected bool $isReadOnlyUpdatedAdd;
    protected bool $isReadOnlyUpdatedEdit;
    
    protected bool $isHidden;
    protected bool $isHiddenUpdatedAdd;
    protected bool $isHiddenUpdatedEdit;
    protected bool $isHiddenUpdatedRead;
    
    public function __construct($field) {
        
        $this->id = $field->id;
        $this->name = $field->name;
        $this->title = $field->title;
        $this->parentId = $field->parent_id;
        $this->parentName = $field->ctype_name; //todo: change field alias
        $this->fieldTypeId = $field->field_type_id;
        $this->fieldTypeName = $field->data_type_name; //todo: change field alias
        $this->isMulti = (bool)$field->is_multi;
        $this->sort = (int)$field->sort ?? 99999;
        $this->tabName = $field->tab_name ?? "General";
        $this->tabSort = (int)$field->tab_sort ?? 99999;
        $this->groupName = $field->group_name ?? "General";
        $this->groupSort = (int)$field->group_sort ?? 99999;
        $this->location = $field->location ?? "top";
        $this->size = (int)$field->size ?? 12;
        $this->isFieldCollection = (bool)$field->is_field_collection;
        $this->fcName = $field->fc_name;
        $this->strLength = $field->str_length ?? "250";
        $this->method = $field->method;
        $this->useParentPermissions = (bool)$field->use_parent_permissions;
        $this->hideInFcSummary = (bool)$field->hide_in_fc_summary;
        $this->isSystemField = (bool)$field->is_system_field;
        $this->description = $field->description;
        $this->fileTypeId = $field->file_type_id;
        $this->fileTypeExtension = $field->file_type_extension;
        $this->allowBasicHtmlTags = (bool)$field->allow_basic_html_tags;
        $this->isUnique = (bool)$field->is_unique;
        $this->deleteRule = $field->delete_rule;
        $this->isRequired = (bool)$field->is_required;
        $this->dependencies = $field->dependencies;
        $this->readOnlyCondition = $field->read_only_condition;
        $this->requiredCondition = $field->required_condition;
        $this->validationPattern = $field->validation_pattern;
        $this->validationCondition = $field->validation_condition;
        $this->validationMessage = $field->validation_message;
        $this->appearanceId = $field->appearance_id;
        $this->dataSourceId = $field->data_source_id;
        $this->dataSourceTableName = $field->data_source_table_name;
        $this->dataSourceDisplayColumn = $field->data_source_display_column;
        $this->dataSourceValueColumn = $field->data_source_value_column;
        $this->dataSourceSortColumn = $field->data_source_sort_column;
        $this->dataSourceFixedWhereCondition = $field->data_source_fixed_where_condition;
        $this->dataSourceFromString = $field->data_source_from_string;
        $this->dataSourceValueColumnIsText = (bool)$field->data_source_value_column_is_text;
        $this->dataSourceFilterByFieldName = $field->data_source_filter_by_field_name;
        $this->dataSourceFilterByFieldNameInDb = $field->data_source_filter_by_field_name_in_db;
        $this->defaultValue = $field->default_value;
        $this->defaultValueUpdated = $field->default_value_updated;
        $this->isReadOnly = (bool)$field->is_read_only;
        $this->isReadOnlyUpdatedAdd = (bool)$field->is_read_only_updated_add;
        $this->isReadOnlyUpdatedEdit = (bool)$field->is_read_only_updated_edit;
        $this->isHidden = (bool)$field->is_hidden;
        $this->isHiddenUpdatedAdd = (bool)$field->is_hidden_updated_add;
        $this->isHiddenUpdatedEdit = (bool)$field->is_hidden_updated_edit;
        $this->isHiddenUpdatedRead = (bool)$field->is_hidden_updated_read;

    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function getParentName(): string
    {
        return $this->parentName;
    }

    public function getFieldTypeId(): string
    {
        return $this->fieldTypeId;
    }

    public function getFieldTypeName(): string
    {
        return $this->fieldTypeName;
    }

    public function getIsMulti(): bool
    {
        return $this->isMulti;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function getIsPrimaryColumn(): bool
    {
        return $this->isPrimaryColumn;
    }

    public function getTabName(): string
    {
        return $this->tabName;
    }

    public function getTabSort(): int
    {
        return $this->tabSort;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupSort(): int
    {
        return $this->groupSort;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getIsFieldCollection(): bool
    {
        return $this->isFieldCollection;
    }

    public function getFcName(): ?string
    {
        return $this->fcName;
    }


    public function getStrLength(): string
    {
        return $this->strLength;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getUseParentPermissions(): bool
    {
        return $this->useParentPermissions;
    }

    public function getHideInFcSummary(): bool
    {
        return $this->hideInFcSummary;
    }

    public function getIsSystemField(): bool
    {
        return $this->isSystemField;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFileTypeId(): ?int
    {
        return $this->fileTypeId;
    }

    public function getFileTypeExtension(): ?string
    {
        return $this->fileTypeExtension;
    }

    public function getAllowBasicHtmlTags(): bool
    {
        return $this->allowBasicHtmlTags;
    }

    public function getIsUnique(): bool
    {
        return $this->isUnique;
    }


    public function getDeleteRule(): ?string
    {
        return $this->deleteRule;
    }

    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }

    public function getDependencies(): ?string
    {
        return $this->dependencies;
    }

    public function getReadOnlyCondition(): ?string
    {
        return $this->readOnlyCondition;
    }

    public function getRequiredCondition(): ?string
    {
        return $this->requiredCondition;
    }

    public function getValidationPattern(): ?string
    {
        return $this->validationPattern;
    }

    public function getValidationCondition(): ?string
    {
        return $this->validationCondition;
    }

    public function getValidationMessage(): ?string
    {
        return $this->validationMessage;
    }

    public function getAppearanceId(): ?string
    {
        return $this->appearanceId;
    }

    public function getDataSourceId(): ?string
    {
        return $this->dataSourceId;
    }

    public function getDataSourceTableName(): ?string
    {
        return $this->dataSourceTableName;
    }

    public function getDataSourceDisplayColumn(): ?string
    {
        return $this->dataSourceDisplayColumn;
    }

    public function getDataSourceValueColumn(): ?string
    {
        return $this->dataSourceValueColumn;
    }

    public function getDataSourceSortColumn(): ?string
    {
        return $this->dataSourceSortColumn;
    }

    public function getDataSourceFixedWhereCondition(): ?string
    {
        return $this->dataSourceFixedWhereCondition;
    }

    public function getDataSourceFromString(): ?string
    {
        return $this->dataSourceFromString;
    }

    public function getDataSourceValueColumnIsText(): bool
    {
        return $this->dataSourceValueColumnIsText;
    }

    public function getDataSourceFilterByFieldName(): ?string
    {
        return $this->dataSourceFilterByFieldName;
    }

    public function getDataSourceFilterByFieldNameInDb(): ?string
    {
        return $this->dataSourceFilterByFieldNameInDb;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    public function getDefaultValueUpdated(): ?string
    {
        return $this->defaultValueUpdated;
    }

    public function getIsReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    public function getIsReadOnlyUpdatedAdd(): bool
    {
        return $this->isReadOnlyUpdatedAdd;
    }

    public function getIsReadOnlyUpdatedEdit(): bool
    {
        return $this->isReadOnlyUpdatedEdit;
    }

    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }

    public function getIsHiddenUpdatedAdd(): bool
    {
        return $this->isHiddenUpdatedAdd;
    }

    public function getIsHiddenUpdatedEdit(): bool
    {
        return $this->isHiddenUpdatedEdit;
    }

    public function getIsHiddenUpdatedRead(): bool
    {
        return $this->isHiddenUpdatedRead;
    }

}