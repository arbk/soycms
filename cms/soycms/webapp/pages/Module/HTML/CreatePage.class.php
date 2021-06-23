<?php

class CreatePage extends CMSWebPageBase
{
    private $moduleId;
    private $moduleName;
    private $modulePath;
    private $iniPath;

    public function doPost()
    {
        $this->moduleId = (isset($_POST["Module"]["id"])) ? soy2_h($_POST["Module"]["id"]) : null;
        $this->moduleName = $_POST["Module"]["name"];
        if (strlen($this->moduleName) < 1) {
            $this->moduleName = $this->moduleId;
        }

        //
        if (!SOY2Logic::createInstance("logic.site.Module.ModuleCreateLogic")->validate($this->moduleName)) {
            $this->jump("Module.HTML.Create?invalid&moduleId=" . $this->moduleId);
        }

        $moduleDir = $this->getModuleDirectory();

        $this->modulePath = $moduleDir . str_replace(".", "/", $this->moduleId) . ".php";
        $this->iniPath = $moduleDir . str_replace(".", "/", $this->moduleId) . ".ini";

        if (soy2_check_token()) {
            if (preg_match('/^[a-zA-Z0-9_]+$/', $this->moduleId) && !file_exists($this->modulePath)) {
                if (!is_dir(dirname($this->modulePath))) {
                    mkdir(dirname($this->modulePath), F_MODE_DIR, true);
                }
                file_put_contents($this->modulePath, "<?php ?>");
                chmod($this->modulePath, F_MODE_FILE);
                file_put_contents($this->iniPath, "name=" . $this->moduleName);
                chmod($this->iniPath, F_MODE_FILE);

                $this->jump("Module.HTML.Editor?updated&moduleId=" . $this->moduleId);
            } else {
                ;
            }
        }
    }

    public function __construct()
    {
        parent::__construct();

        if ($this->moduleId) {
            DisplayPlugin::visible("failed");
        }

        $this->addForm("form");

        $this->addInput("module_id", array(
        "name" => "Module[id]",
        "value" => (isset($_GET["moduleId"])) ? soy2_h($_GET["moduleId"]) : $this->moduleId,
        "style" => "padding: 3px; width:300px;"
        ));

        $this->addInput("module_name", array(
        "name" => "Module[name]",
        "value" => $this->moduleName,
        "style" => "padding: 3px; width:300px;"
        ));
    }

    private function getModuleDirectory()
    {
        return UserInfoUtil::getSiteDirectory() . ".module/html/";
    }
}
