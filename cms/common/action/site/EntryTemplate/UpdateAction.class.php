<?php
SOY2::import("domain.cms.Template");
class UpdateAction extends SOY2Action
{
    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if ($form->hasError()) {
            return SOY2Action::FAILED;
        }
        $logic = SOY2Logic::createInstance("logic.site.EntryTemplate.TemplateLogic");
        $entity = SOY2::cast("EntryTemplate", $form);
        $entity->setId($form->template_id);

        if (strlen($form->template_id) == 0) {
            //新規作成
            $return = $logic->insert($entity);
            $this->setAttribute("mode", "create");
        } else {
            //update
            $return = $logic->update($entity);
            $this->setAttribute("mode", "update");
        }
        if ($return) {
            return SOY2Action::SUCCESS;
        } else {
            return SOY2Action::FAILED;
        }
    }
}

class UpdateActionForm extends SOY2ActionForm
{
    public $template_id = null;
    public $name;
    public $description;
    public $templates;
    public $labelRestrictionPositive = array();

    public function setTemplate_id($template_id)
    {
        $this->template_id = $template_id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setTemplates($template)
    {
        $this->templates = $template;
    }
    public function setLabelRestrictionPositive($labelRestrictionPositive)
    {
        $this->labelRestrictionPositive = $labelRestrictionPositive;
    }
}
