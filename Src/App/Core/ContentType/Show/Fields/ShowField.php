<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show\Fields;

use App\Core\Common\IElementContainerItem;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class ShowField implements IElementContainerItem
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


    public function render(): string
    {

        $extends = $this->ctype->getTplExtends();

        if (_strlen($extends) > 0) {

            $componentName = to_snake_case($this->data->getName()) . '-component';

            if (_strpos($extends, 'id="tpl-' . $componentName . '"') !== false) {

                return '
                <div class="col-md-12 p-0">
                    <' . $componentName . ' ref="' . $this->data->getName() . 'Component" title="' . $this->data->getTitle() . '"></' .  $componentName . '>
                </div>

                ';
            }
        }

        return sprintf(
            '<p class="card-p">
                %s
                %s
            </p>',
            $this->getLabel(),
            $this->renderDisplayData()
        );
    }

    protected function renderDisplayData(): string
    {
        return sprintf('<span> {{ %s}} </span>', $this->getDataPath());
    }

    protected function getLabel(): string
    {
        return sprintf('<span class="me-1"><strong>%s</strong></span>', $this->getTitle());
    }

    protected function getTitle(): string
    {

        $title = $this->data->getTitle();

        if (!in_array(mb_substr($title, - 1), ["?","؟",":"]))
           $title .= ":";

        return $title;
    }

    protected function getDataPath(): string
    {
        return sprintf(
            '%s.%s',
            $this->getDataObjectName(),
            $this->data->getName()
        );
    }

    protected function getDataObjectName(): string
    {
        return $this->dataObjectName;
    }


    public static function create(CType $ctype, CTypeField $field, $dataObjectName) : ?ShowField {
        
        switch ($field->getFieldTypeId()) {
            case "text":
                return new ShowFieldText($ctype, $field, $dataObjectName);
            case "relation":
                if ($field->getIsMulti())
                    return new ShowFieldComboboxMulti($ctype, $field, $dataObjectName);
                else
                    return new ShowFieldComboboxSingle($ctype, $field, $dataObjectName);
            case "field_collection":
                return new ShowFieldFieldCollection($ctype, $field, $dataObjectName);
            case "date":
                return new ShowFieldDate($ctype, $field, $dataObjectName);
            case "media":
                if ($field->getIsMulti())
                    return new ShowFieldAttachmentMulti($ctype, $field, $dataObjectName);
                else
                    return new ShowFieldAttachmentSingle($ctype, $field, $dataObjectName);
            case "number":
                return new ShowFieldNumber($ctype, $field, $dataObjectName);
            case "decimal":
                if ($field->getAppearanceId() == "7_map") {
                    if (substr($field->getName(), _strlen($field->getName()) - 3, 3) == "lat") {
                        // return new GtplFieldDecimalMap($ctype, $field, $dataObjectName);
                        return new ShowFieldDecimalMapPin($ctype, $field, $dataObjectName);
                    } else
                        return null;
                } else
                    return new ShowFieldDecimal($ctype, $field, $dataObjectName);
            case "boolean":
                return new ShowFieldBoolean($ctype, $field, $dataObjectName);
            case "component":
                return new ShowFieldComponent($ctype, $field, $dataObjectName);

        }

        return null;
    }
}
