<?php

class ConfirmStage extends StageBase
{
    public function __construct()
    {
    }

    public function execute()
    {
        parent::__construct();
        $page = $this->run("Page.DetailAction", array("id"=>$this->wizardObj->pageId))->getAttribute("Page");
        $this->createAdd("page_link", "HTMLLink", array(
            "link"=>UserInfoUtil::getSiteURL().$page->getUri(),
            "text"=>UserInfoUtil::getSiteURL().$page->getUri()
        ));
    }

    public function checkNext()
    {
        return true;
    }

    public function checkBack()
    {
        return true;
    }

    public function getNextObject()
    {
        return "EndStage";
    }


    public function getNextString()
    {
        return "終了";
    }

    public function getBackString()
    {
        return "";
    }
}
