<?php

declare(strict_types=1);

namespace App\Core\ContentType\Show;

use App\Core\Application;
use App\Core\Common\CTypeFieldHelper;
use App\Core\Common\CTypeLoader;
use App\Core\Common\ElementContainer;
use App\Core\ContentType\Show\Fields\ShowField;
use App\Core\ContentType\Show\PageElementContainers\ButtonsContainer;
use App\Core\ContentType\Show\PageElementContainers\Group;
use App\Core\ContentType\Show\PageElementContainers\MainContainer;
use App\Core\ContentType\Show\PageElementContainers\PageHeader;
use App\Core\ContentType\Show\PageElementContainers\Row;
use App\Core\ContentType\Show\PageElementContainers\Tab;
use App\Core\ContentType\Show\PageHeaderElements\CtypeStatusAlert;
use App\Core\ContentType\Show\PageHeaderElements\GtplFieldButtonHtml;
use App\Core\ContentType\Show\PageHeaderElements\GtplFieldButtonScript;
use App\Core\ContentType\Show\PageHeaderElements\LogModal;
use App\Core\ContentType\Show\Scripts\Scripts;
use App\Core\ContentType\Show\Scripts\VueMethods;
use App\Core\ContentType\Show\UpdateStatusComponent\UpdateStatusComponent;
use App\Core\ContentType\Show\UpdateStatusComponent\UpdateStatusComponentMethods;
use App\Models\CType;
use App\Models\CTypeFields\CTypeField;

class Generator
{

    private string $DATA_OBJECT_NAME = "nodeData";

    public function createTemplate($ctype): string
    {

        $page = new ElementContainer();
        $vueMethods = new VueMethods();
        $script = new Scripts();

        if ($ctype->getUseGenericStatus()) {
            $page->addElement(new UpdateStatusComponent());
            $vueMethods->addElement(new UpdateStatusComponentMethods());
        }

        // Disable button temporarily, Todo: fix it
        // $buttonsContainer = new ButtonsContainer();
        // foreach (CTypeFieldHelper::getButtons($ctype->getFields()) as $button) {
        //     $buttonsContainer->addElement(new GtplFieldButtonHtml($ctype, $button));
        //     $vueMethods->addElement(new GtplFieldButtonScript($ctype, $button));
        // }

        $page->addElement(new PageHeader($ctype));


        if($ctype->getCtypePermission()->allow_view_log)
            $page->addElement(new LogModal($ctype));
        
        // $page->addElement($buttonsContainer);

        $mainContainer = new MainContainer();

        $mainContainer = $this->addFields($ctype, $mainContainer);

        $page->addElement($mainContainer);


        $scripts = $ctype->getTplExtends();

        $scripts .= $script->render();

        //get the template
        $content = $this->getNodeShowFileTemplate();

        $content = _str_replace("//%%HTML_CONTENT%%", $page->render(), $content);

        $content = _str_replace("//%%METHODS%%", $vueMethods->render(), $content);

        $content = _str_replace("//%%SCRIPTS%%", $scripts, $content);

        return $content;
    }



    /**
     * @return MainContainer 
     */
    private function addFields(CType $ctype, MainContainer $mainContainer): MainContainer
    {

        foreach (CTypeFieldHelper::getUniqueTabs($ctype->getFields()) as $tabName) {

            $tab = new Tab($tabName);

            $row = new Row();

            foreach (CTypeFieldHelper::getUniqueGroupsInTab($ctype->getFields(), $tabName) as $groupName) {

                $group = new Group($groupName, 12);

                foreach (CTypeFieldHelper::getFieldsInTabAndGroup($ctype->getFields(), $tabName, $groupName) as $field) {

                    if ($field->getIsHidden() || $field->getIsHiddenUpdatedRead())
                        continue;

                    $field = ShowField::create($ctype, $field, $this->DATA_OBJECT_NAME);
                    
                    if($field != null){
                        $group->addElement($field);
                    }
                }

                $row->addElement($group);
            }

            $tab->addElement($row);

            $mainContainer->addElement($tab);
        }

        return $mainContainer;
    }


    private function getNodeShowFileTemplate(): string
    {
        return file_get_contents(APP_ROOT_DIR . DS . "views" . DS . "node" . DS . "newShow.php");
    }
}
