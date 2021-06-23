<?php

class IndexPage extends CMSWebPageBase
{
    const TYPE_PHP = "php";
    const TYPE_HTML = "html";

    public function __construct()
    {
        //ディレクトリの作成
        if (!$this->checkHasModuleDirectory()) {
            $this->createModuleDirectory();
        }

        parent::__construct();

        //PHPモジュールの使用が許可されているか？
        $this->addModel("allow_php_module", array(
            "visible" => SOYCMS_ALLOW_PHP_MODULE
        ));

        $modules = $this->getModules();

        $this->addModel("has_module", array(
            "visible" => (count($modules))
        ));

        $this->addModel("no_module", array(
            "visible" => (count($modules) === 0)
        ));

        $this->createAdd("module_list", "_component.Module.ModuleListComponent", array(
            "list" => $modules,
            "editorLink" => SOY2PageController::createLink("Module.Editor?moduleId="),
            "removeLink" => SOY2PageController::createLink("Module.Remove?moduleId=")
        ));

        $modules = $this->getModules(self::TYPE_HTML);

        $this->addModel("no_html_module", array(
            "visible" => (count($modules) === 0)
        ));

        $this->addModel("has_html_module", array(
            "visible" => (count($modules))
        ));

        $this->createAdd("html_module_list", "_component.Module.ModuleListComponent", array(
            "list" => $modules,
            "editorLink" => SOY2PageController::createLink("Module.HTML.Editor?moduleId="),
            "removeLink" => SOY2PageController::createLink("Module.HTML.Remove?moduleId=")
        ));
    }

    /**
     * モジュール用のディレクトリがあるか？
     * @return boolean
     */
    private function checkHasModuleDirectory()
    {
        $dir = $this->getModuleDirectory();
        return (file_exists($dir) && is_dir($dir));
    }

    private function createModuleDirectory()
    {
        mkdir($this->getModuleDirectory(), F_MODE_DIR_HDN);
        CMSUtil::makeHtaccess($this->getModuleDirectory());
        mkdir($this->getModuleDirectory(self::TYPE_HTML), F_MODE_DIR);
    }

    private function getModules($t = self::TYPE_PHP)
    {
        $res = array();
        $moduleDir = $this->getModuleDirectory();

        $files = soy2_scanfiles($moduleDir);

        foreach ($files as $file) {
            if (!preg_match('/\.php$/', $file)) {
                continue;
            }
            $moduleId = preg_replace('/^.*\.module\//', "", $file);

            if ($t == self::TYPE_PHP) {
                if (!$this->checkModuleDir($moduleId)) {
                    continue;
                }
            } else {
                if ($this->checkModuleDir($moduleId)) {
                    continue;
                }
            }


            //一個目の/より前はカテゴリ
            $moduleId = preg_replace('/\.php$/', "", $moduleId);
            $moduleId = str_replace("/", ".", $moduleId);
            $name = $moduleId;

            //ini
            $iniFilePath = preg_replace('/\.php$/', ".ini", $file);
            if (file_exists($iniFilePath)) {
//          $array = parse_ini_file($iniFilePath);
                $array = parse_ini_file($iniFilePath, false, INI_SCANNER_RAW);
                if (isset($array["name"])) {
                    $name = $array["name"];
                }
            }

            $res[] = array(
                "name" => $name,
                "moduleId" => $moduleId,
            );
        }

        array_multisort(array_column($res, "name"), SORT_NATURAL, $res);

        return $res;
    }

    //モジュール群からcommonディレクトリにあるモジュールを除く
    private function checkModuleDir($dir)
    {
        return (preg_match("/^common./", $dir) || preg_match("/^html./", $dir)) ? false : true;
    }

    private function getModuleDirectory($t = self::TYPE_PHP)
    {
        if (isset($t) && $t == self::TYPE_HTML) {
            return UserInfoUtil::getSiteDirectory() . ".module/html/";
        } else {
            return UserInfoUtil::getSiteDirectory() . ".module/";
        }
    }
}
