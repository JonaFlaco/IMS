<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add\Fields;

use App\Core\Common\CTypeFieldHelper;
use App\Core\Common\IElementContainerItem;
use App\Core\ContentType\Add\PageElementContainers\Group as PageElementContainersGroup;
use App\Core\ContentType\Add\PageElementContainers\Row as PageElementContainersRow;
use App\Core\ContentType\Add\PageElementContainers\Tab as PageElementContainersTab;
use App\Core\ContentType\Add\PageElementContainers\TabContainer;
use App\Core\Gtpl\PageElementContainers\Group;
use App\Core\Gtpl\PageElementContainers\MainContainer;
use App\Core\Gtpl\PageElementContainers\Row;
use App\Core\Gtpl\PageElementContainers\Tab;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class AddFieldFieldCollection extends AddField implements IElementContainerItem
{

    private array $fcFields;

    public function __construct(CType $ctype, CTypeField $data, string $dataObjectName)
    {
        parent::__construct($ctype, $data, $dataObjectName);

        $this->fcFields = CTypeFieldHelper::loadByCtypeId($this->data->getDataSourceId());
    }

    public function render(): string
    {

        $extends = $this->ctype->getTplExtends();

        if (_strlen($extends) > 0) {

            $componentName = to_snake_case($this->data->getName()) . '-list-component';

            if (_strpos($extends, 'id="tpl-' . $componentName . '"') !== false) {

                return '
                <div class="col-md-12 p-0">
                    <' . $componentName . ' ref="' . $this->data->getName() . 'Component" v-model="' . $this->getDataObjectName() . "." . $this->data->getName() . '" title="' . $this->data->getTitle() . '"></' .  $componentName . '>
                </div>

                ';
            }
        }




        $tabContainer = new TabContainer();

        foreach ($this->getUniqueTabs($this->fcFields) as $tabName) {

            $tab = new PageElementContainersTab($tabName);

            $row = new PageElementContainersRow();

            foreach ($this->getUniqueGroupsInTab($this->fcFields, $tabName) as $groupName) {

                $group = new PageElementContainersGroup($groupName, 6);

                foreach ($this->getFieldsInTabAndGroup($this->fcFields, $tabName, $groupName) as $field) {

                    if ($field->getIsHidden())
                        continue;


                    switch ($field->getFieldTypeId()) {
                        case "text":
                            $group->addElement(new AddFieldText($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "relation":
                            if ($field->getIsMulti())
                                $group->addElement(new AddFieldComboboxMulti($this->ctype, $field, $this->getFcItemName()));
                            else
                                $group->addElement(new AddFieldComboboxSingle($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "field_collection":
                            $group->addElement(new AddFieldFieldCollection($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "date":
                            $group->addElement(new AddFieldDate($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "media":
                            if ($field->getIsMulti())
                                $group->addElement(new AddFieldAttachmentMulti($this->ctype, $field, $this->getFcItemName()));
                            else
                                $group->addElement(new AddFieldAttachmentSingle($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "number":
                            $group->addElement(new AddFieldNumber($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "decimal":
                            $group->addElement(new AddFieldDecimal($this->ctype, $field, $this->getFcItemName()));
                            break;
                        case "boolean":
                            $group->addElement(new AddFieldBoolean($this->ctype, $field, $this->getFcItemName()));
                            break;
                        default:
                            $group->addElement(new AddField($this->ctype, $field, $this->getFcItemName()));
                            break;
                    }
                }

                $row->addElement($group);
            }

            $tab->addElement($row);

            $tabContainer->addElement($tab);
        }


        $elements[] = $tabContainer;

        $result = sprintf('<h2>%s</h2><div v-for="%s in %s">', $this->data->getTitle(), $this->getFcItemName(), $this->getDataPath());

        foreach ($elements as $el) {
            $result .= $el->render();
        }

        $result .= "</div>";
        return $result;
    }

    private function getFcItemName(): string
    {
        return "item";
    }


    private function getUniqueTabs(array $fields): array
    {

        return array_filter(array_unique(array_map(function ($o) {
            return $o->getTabName();
        }, $fields)));
    }

    private function getUniqueGroupsInTab(array $fields, string $tabName): array
    {

        return array_filter(array_unique(array_map(function ($o) use ($tabName) {
            if ($o->getTabName() == $tabName)
                return $o->getGroupName();
        }, $fields)));
    }

    private function getFieldsInTabAndGroup(array $fields, string $tabName, string $groupName): array
    {

        return array_filter(array_map(function ($o) use ($tabName, $groupName) {
            if ($o->getTabName() == $tabName && $o->getGroupName() == $groupName)
                return $o;
        }, $fields));
    }
}
