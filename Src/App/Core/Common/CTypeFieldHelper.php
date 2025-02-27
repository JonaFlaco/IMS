<?php

declare(strict_types=1);

namespace App\Core\Common;

use App\Exceptions\CriticalException;
use App\Models\CoreModel;
use App\Models\CTypeFields\CTypeFieldAttachment;
use App\Models\CTypeFields\CTypeField;
use App\Models\CTypeFields\CTypeFieldBoolean;
use App\Models\CTypeFields\CTypeFieldButton;
use App\Models\CTypeFields\CTypeFieldComboBox;
use App\Models\CTypeFields\CTypeFieldComponent;
use App\Models\CTypeFields\CTypeFieldDate;
use App\Models\CTypeFields\CTypeFieldDecimal;
use App\Models\CTypeFields\CTypeFieldFieldCollection;
use App\Models\CTypeFields\CTypeFieldNote;
use App\Models\CTypeFields\CTypeFieldNumber;
use App\Models\CTypeFields\CtypeFieldText;

class CTypeFieldHelper
{

    /**
     * @return CTypeField[]
     */
    public static function loadByCtypeId(string $id): array
    {

        $result = CoreModel::getInstance()->getFields($id);

        for ($i = 0; $i < sizeof($result); $i++) {

            $result[$i] = self::map($result[$i]);
        }

        return $result;
    }




    private static function map(object $field): CTypeField
    {

        switch ($field->field_type_id) {

            case "text":
                return new CtypeFieldText($field);
            case "relation":
                return new CTypeFieldComboBox($field);
            case "field_collection":
                return new CTypeFieldFieldCollection($field);
            case "date":
                return new CTypeFieldDate($field);
            case "media":
                return new CTypeFieldAttachment($field);
            case "number":
                return new CTypeFieldNumber($field);
            case "decimal":
                return new CTypeFieldDecimal($field);
            case "boolean":
                return new CTypeFieldBoolean($field);
            case "button":
                return new CTypeFieldButton($field);
            case "note":
                return new CTypeFieldNote($field);
            case "component":
                return new CTypeFieldComponent($field);
            default:
                throw new CriticalException("Field Type not defined");
        }
    }

    /**
     * @param CTypeField[] $fields
     * @return CTypeField[]
     */
    public static function getButtons(array $fields) : array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->getFieldTypeId() != "button")
                continue;
            $result[] = $field;
        }

        return $result;
    }


    /**
     * @param CTypeField[] $fields
     * @return string[]
     */
    public static function getUniqueTabs(array $fields): array
    {
        $result = array_map(function ($o) {
                return $o->getTabName();
            }, $fields
        );

        return array_filter(array_unique($result));
    }

    /**
     * @param CTypeField[] $fields
     * @return string[]
     */
    public static function getUniqueGroupsInTab(array $fields, string $tabName): array
    {
        $result = array_map(function ($o) use ($tabName) {
                if ($o->getTabName() == $tabName)
                    return $o->getGroupName();
            }, $fields
        );

        return array_filter(array_unique($result));
    }



    /**
     * @param CTypeField[] $fields
     * @return CTypeField[]
     */
    public static function getFieldsInTabAndGroup(array $fields, string $tabName, string $groupName): array
    {
        $result = array_map(function ($o) use ($tabName, $groupName) {
                if ($o->getTabName() == $tabName && $o->getGroupName() == $groupName)
                    return $o;
            }, $fields
        );

        return array_filter($result);
    }

}
