<?php

class TemplateRemoveAction extends SOY2Action
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if (null===$this->id) {
            return SOY2Action::FAILED;
        } else {
            $logic = SOY2Logic::createInstance("logic.site.EntryTemplate.TemplateLogic");
            if ($logic->deleteById($this->id)) {
                return SOY2Action::SUCCESS;
            } else {
                return SOY2Action::FAILED;
            }
        }
    }
}
