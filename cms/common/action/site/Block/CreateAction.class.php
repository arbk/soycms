<?php

class CreateAction extends SOY2Action
{
    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        SOY2::import("domain.cms.Block");
        $block = SOY2::cast("Block", $form);
        $block->setObject($block->getBlockComponent());
        $logic = SOY2Logic::createInstance("logic.site.Block.BlockLogic");
        $id = $logic->create($block);
        $this->setAttribute("insertedId", $id);
        return SOY2Action::SUCCESS;
    }
}

class CreateActionForm extends SOY2ActionForm
{
    public $class;
    public $pageId;
    public $soyId;

    /**
     * @validator string {"require":true}
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function setSoyId($soyId)
    {
        $this->soyId = $soyId;
    }
}
