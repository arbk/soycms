<?php
class TemplateSelectStage extends StageBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $list = $this->run("Template.TemplateListAction")->getAttribute("list");
        foreach ($list as $key => $tmp) {
            if ($tmp->getPageType() != $this->wizardObj->pageType) {
                unset($list[$key]);
            }
        }

        $this->createAdd("template_list", "TemplateList", array(
        "list"=>$list,
        "selected"=>@$this->wizardObj->template_id
        ));
    }

    public function checkNext()
    {
        if (isset($_POST["template_id"])) {
            $this->wizardObj->template_id = $_POST["template_id"];
            return true;
        } else {
            $this->addErrorMessage("WIZARD_NO_SELECT_TEMPLATE");
            return false;
        }
    }

    public function checkBack()
    {
        return true;
    }

    public function getNextObject()
    {
        return "BLOG.PageConfigStage";
    }

    public function getBackObject()
    {
        return "SelectTopStage";
    }
}

class TemplateList extends HTMLList
{
    private $selected;

    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    public function populateItem($entity)
    {
        $this->createAdd("tempalte_radio", "HTMLCheckBox", array(
        "value"=>$entity->getId(),
        "label"=>$entity->getName(),
        "selected"=>($entity->getId() == $this->selected)
        ));

        $this->createAdd("description", "HTMLLabel", array(
        "text"=>$entity->getDescription()
        ));
    }
}
