<?php

class ReplaceStringPlugin_FormPage extends WebPage
{
    private $pluginObj;  // ReplaceStringPlugin Obj

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
            if (isset($_POST["replace_string_com_save"])) {
                if (isset($_POST["replace_string_com"])) {
                    $this->pluginObj->setComStringSet($_POST["replace_string_com"]);
                }
            } elseif (isset($_POST["replace_string_target_save"])) {
                if (isset($_POST["replace_string_entry_field_ids"])) {
                    $this->pluginObj->setEntryFieldIds($_POST["replace_string_entry_field_ids"]);
                }
            } elseif (isset($_POST["replace_string_labelid_save"])) {
                if (isset($_POST["replace_string_labelid"])) {
                    $this->pluginObj->setLabelId($_POST["replace_string_labelid"]);
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

        $this->addForm("replace_string_form");

        $this->addTextarea("replace_string_com", array(
        "name"=>"replace_string_com",
        "value"=>$this->pluginObj->getComStringSet()
        ));

        $this->addInput("replace_string_entry_field_ids", array(
        "name"=>"replace_string_entry_field_ids",
        "value"=>$this->pluginObj->getEntryFieldIds()
        ));

        $this->addInput("replace_string_labelid", array(
        "name"=>"replace_string_labelid",
        "value"=>$this->pluginObj->getLabelId()
        ));
    }
}
