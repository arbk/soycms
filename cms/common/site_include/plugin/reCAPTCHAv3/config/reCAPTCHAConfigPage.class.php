<?php

class reCAPTCHAConfigPage extends WebPage
{
    private $pluginObj;

    public function __construct()
    {
        //サイトマップから持ってくる
        SOY2::imports("site_include.plugin.sitemap.component.*");
    }

    public function doPost()
    {
        if (soy2_check_token()) {
            $this->pluginObj->setSiteKey(trim($_POST["site_key"]));
            $this->pluginObj->setSecretKey(trim($_POST["secret_key"]));

            CMSPlugin::savePluginConfig(reCAPTCHAv3Plugin::PLUGIN_ID, $this->pluginObj);
            CMSPlugin::redirectConfigPage();
        }
    }

    public function execute()
    {
        parent::__construct();

        $this->addForm("form");

        $this->addInput("site_key", array(
            "name" => "site_key",
            "value" => $this->pluginObj->getSiteKey()
        ));

        $this->addInput("secret_key", array(
            "name" => "secret_key",
            "value" => $this->pluginObj->getSecretKey()
        ));
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }
}
