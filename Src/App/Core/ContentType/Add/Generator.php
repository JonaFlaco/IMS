<?php

declare(strict_types=1);

namespace App\Core\ContentType\Add;

use App\Core\Application;
use App\Core\Common\CTypeFieldHelper;
use App\Core\Common\CTypeLoader;
use App\Core\Common\ElementContainer;
use App\Core\ContentType\Add\Fields\AddField;
use App\Core\ContentType\Add\PageElementContainers\Group;
use App\Core\ContentType\Add\PageElementContainers\PageHeader;
use App\Core\ContentType\Add\PageElementContainers\Page;
use App\Core\ContentType\Add\PageElementContainers\Row;
use App\Core\ContentType\Add\PageElementContainers\Tab;
use App\Core\ContentType\Add\PageElementContainers\TabContainer;
use App\Core\Gctypes\CtypesHelper;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class Generator
{

    private CType $ctype;
    private string $ctypeId;
    private ?int $recordId;

    public function __construct(string $ctypeId, ?int $recordId = null)
    {
        $this->ctypeId = $ctypeId;
        $this->recordId = $recordId;

        $this->ctype = CTypeLoader::load($ctypeId);
    }

    public function createTemplate() 
    {
        $page = new Page();
        $page->addElement(new PageHeader($this->ctype));
        $page->tabContainer = $this->addFields($this->ctype, $page->tabContainer);
        $page->vueData->addElement("pageTitle", $this->ctype->getName());


        $page->vueData->addElement("id", null);
        $page->vueData->addElement("isAddMode", true); //TODO:
        $page->vueData->addElement("isEditMode",false); //TODO:
        $page->vueData->addElement("SaveButtonLoading", false);

        foreach($this->ctype->getFields() as $field)
        {

            $page->vueData->addElement($field->getName(), null);

            foreach(CTypeFieldHelper::loadByCtypeId($field->getParentId()) as $fc)
            {
                $page->vueData->addElement($field->getName() . "_" . $fc->getName(), null);
            }

        }

        $data = [
            "title" => $this->ctype->getName()
        ];

        Application::getInstance()->view->renderView('inc/defaultTemplate', (array)$data);
        echo $page->render();
        exit;
    }


    /**
     * @return MainContainer 
     */
    private function addFields(CType $ctype, TabContainer $tabContainer): TabContainer
    {

        foreach (CTypeFieldHelper::getUniqueTabs($ctype->getFields()) as $tabName) {

            $tab = new Tab($tabName);

            $row = new Row();

            foreach (CTypeFieldHelper::getUniqueGroupsInTab($ctype->getFields(), $tabName) as $groupName) {

                $group = new Group($groupName, 6);

                foreach (CTypeFieldHelper::getFieldsInTabAndGroup($ctype->getFields(), $tabName, $groupName) as $field) {

                    if ($field->getIsHidden())
                        continue;

                    $field = AddField::create($ctype, $field, "");
                    
                    if($field != null){
                        $group->addElement($field);
                    }
                }

                $row->addElement($group);
            }

            $tab->addElement($row);

            $tabContainer->addElement($tab);
        }

        return $tabContainer;
    }
    
}
