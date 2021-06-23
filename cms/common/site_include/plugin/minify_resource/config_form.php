<?php

class MinifyResourcePlugin_FormPage extends WebPage
{
    private $pluginObj;

    public function __construct()
    {
    }

    public function doPost()
    {
        if (soy2_check_token()) {
            $this->pluginObj->minCss = isset($_POST["min_css"]);
            $this->pluginObj->genCss = isset($_POST["gen_css"]);
            $this->pluginObj->minJs = isset($_POST["min_js"]);
            $this->pluginObj->genJs = isset($_POST["gen_js"]);
            $this->pluginObj->minXml = isset($_POST["min_xml"]);
            $this->pluginObj->genXml = isset($_POST["gen_xml"]);
            $this->pluginObj->minHtml = isset($_POST["min_html"]);
            CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
            CMSPlugin::redirectConfigPage();
        }
    }

    public function execute()
    {
        WebPage::__construct();
        $this->createAdd("minify_resource_form", "HTMLForm", array());
        $this->createAdd("min_css", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"min_css",
        "value"=>1,
        "selected"=>$this->pluginObj->minCss,
        "isBool"=>true,
        "label"=>"CSSを軽量化する."
        ));
        $this->createAdd("gen_css", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"gen_css",
        "value"=>1,
        "selected"=>$this->pluginObj->genCss,
        "isBool"=>true,
        "label"=>"CSSファイル（実ファイル）を生成する."
        ));
        $this->createAdd("min_js", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"min_js",
        "value"=>1,
        "selected"=>$this->pluginObj->minJs,
        "isBool"=>true,
        "label"=>"JavaScriptを軽量化する."
        ));
        $this->createAdd("gen_js", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"gen_js",
        "value"=>1,
        "selected"=>$this->pluginObj->genJs,
        "isBool"=>true,
        "label"=>"JavaScriptファイル（実ファイル）を生成する."
        ));
        $this->createAdd("min_xml", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"min_xml",
        "value"=>1,
        "selected"=>$this->pluginObj->minXml,
        "isBool"=>true,
        "label"=>"XMLを軽量化する."
        ));
        $this->createAdd("gen_xml", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"gen_xml",
        "value"=>1,
        "selected"=>$this->pluginObj->genXml,
        "isBool"=>true,
        "label"=>"XMLファイル（実ファイル）を生成する."
        ));
        $this->createAdd("min_html", "HTMLCheckBox", array(
        "type"=>"checkbox",
        "name"=>"min_html",
        "value"=>1,
        "selected"=>$this->pluginObj->minHtml,
        "isBool"=>true,
        "label"=>"HTMLを軽量化する."
        ));
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }

    public function getTemplateFilePath()
    {
        return __DIR__ . "/config_form.html";
    }
}
