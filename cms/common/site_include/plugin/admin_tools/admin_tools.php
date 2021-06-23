<?php
//ini_set("display_errors", 1);

class AdminToolsPlugin
{
    const PLUGIN_ID = "admin_tools";

    public $msg  = array();

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name" => "サイト管理ツールプラグイン",
        "description" => "サイト管理機能を提供するプラグインです. キャッシュ／ファイル／DB 管理.",
        "author" => "arbk",
        "url" => "https://aruo.net/",
        "mail" => "",
        "version" => "1.2",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this, "config_page"));
    }

    public static function register()
    {
      //このプラグインは管理モードでのみ動作する
        if (!CMSPlugin::adminModeCheck()) {
            return;
        }

        require_once(__DIR__ . "/config_form.php");
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new AdminToolsPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
    }

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public function config_page($message)
    {
        $form = SOY2HTMLFactory::createInstance("AdminToolsPlugin_FormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }
}

AdminToolsPlugin::register();
