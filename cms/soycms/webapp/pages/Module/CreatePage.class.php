<?php

class CreatePage extends CMSWebPageBase
{
    private $moduleId;
    private $moduleName;

    public function doPost()
    {
        if (isset($_POST["Module"])) {
            $moduleId = (isset($_POST["Module"]["id"])) ? str_replace("/", ".", $_POST["Module"]["id"]) : null;
            $this->moduleId = soy2_h($moduleId);
            $this->moduleName = $_POST["Module"]["name"];

            if (strlen($this->moduleName) < 1) {
                $this->moduleName = $this->moduleId;
            }

            //禁止文字が含まれているか？
            if (!SOY2Logic::createInstance("logic.site.Module.ModuleCreateLogic")->validate($this->moduleName)) {
                $this->jump("Module.Create?invalid&moduleId=" . $this->moduleId);
            }

            $moduleDir = $this->getModuleDirectory();

            $modulePath = $moduleDir . str_replace(".", "/", $this->moduleId) . ".php";
            $iniPath =$moduleDir . str_replace(".", "/", $this->moduleId) . ".ini";

            if (soy2_check_token()) {
                if (preg_match('/^[a-zA-Z0-9\._]+$/', $this->moduleId) &&
                 strpos($this->moduleId, ".") &&
                 !preg_match("/^common./", $this->moduleId) &&
                 !preg_match("/^html./", $this->moduleId) &&
                 !file_exists($modulePath)
                ) {
                    if (!is_dir(dirname($modulePath))) {
                        mkdir(dirname($modulePath), F_MODE_DIR, true);
                    }
                    file_put_contents($modulePath, "<?php ?>");
                    chmod($modulePath, F_MODE_FILE);
                    file_put_contents($iniPath, "name=" . $this->moduleName);
                    chmod($iniPath, F_MODE_FILE);

                    $this->jump("Module.Editor?updated&moduleId=" . $this->moduleId);
                }
            }
        }
    }

    public function __construct()
    {
        //PHPモジュールの使用が許可されていない場合はモジュール一覧に遷移
        if (!SOYCMS_ALLOW_PHP_MODULE) {
            SOY2PageController::jump("Module");
        }

        parent::__construct();

        DisplayPlugin::visible("updated");
        if ($this->moduleId) {
            DisplayPlugin::visible("failed");
        }

        $this->addForm("form");

        $this->addInput("module_id", array(
        "name" => "Module[id]",
        "value" => (isset($_GET["moduleId"])) ? str_replace("/", ".", soy2_h($_GET["moduleId"])) : $this->moduleId,
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
        return UserInfoUtil::getSiteDirectory() . ".module/";
    }
}
