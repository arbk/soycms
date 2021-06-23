<?php

class DisplayNonePlugin_FormPage extends WebPage
{
    private $pluginObj;  // DisplayNonePlugin Obj

    public function __construct()
    {
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }

    public function getTemplateFilePath()
    {
        return __DIR__ . "/config_form.html";
    }

    public function doPost()
    {
        if (!soy2_check_token()) {
            error_log("Token check error. : " . __METHOD__);
            return;
        }

        try {
            if (isset($_POST["display_none_save"])) {
                if (isset($_POST["display_none_labelid"])) {
                    $this->pluginObj->setLabelId($_POST["display_none_labelid"]);
                }
                CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . " : " . __METHOD__);
        }

        CMSPlugin::redirectConfigPage();
    }

    public function execute()
    {
        WebPage::__construct();

        $this->addForm("display_none_form");

        $this->addInput("display_none_labelid", array(
        "name"=>"display_none_labelid",
        "value"=>$this->pluginObj->getLabelId()
        ));
    }
}
