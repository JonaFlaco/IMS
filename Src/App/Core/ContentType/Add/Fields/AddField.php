<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

abstract class AddField implements IElementContainerItem
{

    protected CType $ctype;
    protected CTypeField $data;
    protected string $dataObjectName;

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        $this->ctype = $ctype;
        $this->data = $data;
        $this->dataObjectName = $dataObjectName;
    }

    abstract public function render(): string;


    public static function create(CType $ctype, CTypeField $field, $dataObjectName) : ?AddField {
        switch ($field->getFieldTypeId()) {
            case "text":
                return new AddFieldText($ctype, $field, $dataObjectName);
            case "relation":
                if ($field->getIsMulti())
                    return new AddFieldComboboxMulti($ctype, $field, $dataObjectName);
                else
                    return new AddFieldComboboxSingle($ctype, $field, $dataObjectName);
            case "field_collection":
                return new AddFieldFieldCollection($ctype, $field, $dataObjectName);
            case "date":
                return new AddFieldDate($ctype, $field, $dataObjectName);
            case "media":
                if ($field->getIsMulti())
                    return new AddFieldAttachmentMulti($ctype, $field, $dataObjectName);
                else
                    return new AddFieldAttachmentSingle($ctype, $field, $dataObjectName);
            case "number":
                return new AddFieldNumber($ctype, $field, $dataObjectName);
            case "decimal":
                if ($field->getAppearanceId() == "7_map") {
                    if (substr($field->getName(), _strlen($field->getName()) - 3, 3) == "lat") {
                        // return new GtplFieldDecimalMap($ctype, $field, $dataObjectName);
                        return new AddFieldDecimalMapPin($ctype, $field, $dataObjectName);
                    } else
                        return null;
                } else
                    return new AddFieldDecimal($ctype, $field, $dataObjectName);
            case "boolean":
                return new AddFieldBoolean($ctype, $field, $dataObjectName);
            case "component":
                return new AddFieldComponent($ctype, $field, $dataObjectName);

        }

        return null;
    }


    
    protected function uniqueName() : string {
        
        if($this->data->getAppearanceId() == "7_map" && _strtolower(substr($this->data->getName(), _strlen($this->data->getName()) -3,_strlen($this->data->getName()))) == "lat"){
            $base_name = _strtolower(substr($this->data->getName(), 0,_strlen($this->data->getName()) - 4));
            return (empty($this->prefix) ? "" : $this->prefix . "_") . $base_name;
        }

        return (empty($this->prefix) ? "" : $this->prefix . "_") . $this->data->getName();

    }

    protected function getTitle(): string
    {
        return $this->data->getTitle();
    }

    protected function getSize() : int
    {
        return $this->data->getSize();
    }

    protected function getDataPath(): string
    {
        return $this->data->getName();
    }
}
