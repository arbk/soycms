<?php

class ReplaceKeywordPlugin_FormPage extends WebPage
{
    private $pluginObj;  // ReplaceKeywordPlugin Obj

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
            if (isset($_POST["replace_keyword_com_save"])) {
                if (isset($_POST["replace_keyword_com"])) {
                    $this->pluginObj->setComKeywordSet($_POST["replace_keyword_com"]);
                }
            } elseif (isset($_POST["replace_keyword_entry_save"])) {
                $this->pluginObj->setUseEntryKeyword(isset($_POST["replace_keyword_use_entry"]));
                if (isset($_POST["replace_keyword_labelid"])) {
                    $this->pluginObj->setLabelId($_POST["replace_keyword_labelid"]);
                }
            }
            CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
        } catch (Exception $e) {
            error_log($e->getMessage() . " : " . __METHOD__);
        }

        CMSPlugin::redirectConfigPage();
    }

    public function execute()
    {
        WebPage::__construct();

        $this->addForm("replace_keyword_form");

        $this->addTextarea("replace_keyword_com", array(
        "name"=>"replace_keyword_com",
        "value"=>$this->pluginObj->getComKeywordSet(),
        ));

        $this->addCheckBox("replace_keyword_use_entry", array(
        "name"=>"replace_keyword_use_entry",
        "value"=>1,
        "selected"=>$this->pluginObj->getUseEntryKeyword(),
        ));

        $this->addInput("replace_keyword_labelid", array(
        "name"=>"replace_keyword_labelid",
        "value"=>$this->pluginObj->getLabelId(),
        ));
    }
}
