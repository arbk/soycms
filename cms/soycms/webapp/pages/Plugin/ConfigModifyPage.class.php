<?php

class ConfigModifyPage extends CMSWebPageBase
{
    public function doPost()
    {
        if (soy2_check_token()) {
            if (isset($_POST["toggle_active"])) {
                $id = $_POST["plugin_id"];
                $this->run("Plugin.ToggleActiveAction", array("pluginId"=>$id));
                SOY2PageController::redirect($_POST["back_url"]);
            } elseif (isset($_POST["toggle_nonactive"])) {
                $id = $_POST["plugin_id"];
                $this->run("Plugin.ToggleActiveAction", array("pluginId"=>$id));
                SOY2PageController::redirect($_POST["back_url"]);
            } elseif (isset($_POST["category_modify"])) {
                //TODO 実装
                $this->run("Plugin.CategoryApplyAction");
                SOY2PageController::redirect($_POST["back_url"]);
            } else {
                //do nothing
            }
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->jump("Plugin");
    }
}
