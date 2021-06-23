<?php

class PageConfigStage extends StageBase
{
    public function __construct()
    {
    }

    public function execute()
    {
        parent::__construct();

        $this->createAdd("name", "HTMLInput", array(
            "name"=>"name",
            "value"=>@$this->wizardObj->name
        ));

        $this->createAdd("url_prefix", "HTMLLabel", array(
            "text"=>UserInfoUtil::getSiteUrl()
        ));

        $this->createAdd("url", "HTMLInput", array(
            "name"=>"url",
            "value"=>@$this->wizardObj->url
        ));
    }

    public function checkNext()
    {
        $this->wizardObj->url = isset($_POST["url"])? $_POST["url"] : "";
        $this->wizardObj->name = isset($_POST["name"])? $_POST["name"] : "";
        return true;
    }

    public function checkBack()
    {
        return true;
    }

    public function getNextObject()
    {
        return "HTML.CreateStage";
    }

    public function getBackObject()
    {
        return "HTML.TemplateSelectStage";
    }
}
