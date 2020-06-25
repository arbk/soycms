<?php

class MPFConfirmConfigPage extends WebPage {

	private $pluginObj;
	private $hash;

	function __construct(){}

	function doPost(){
		if(soy2_check_token()){
			$cnf = MultiplePageFormUtil::readJson($this->hash);

			$cnf["next"] = (isset($_POST["Config"]["next"])) ? $_POST["Config"]["next"] : "";
			$cnf["description"] = (isset($_POST["Config"]["description"])) ? $_POST["Config"]["description"] : "";

			MultiplePageFormUtil::savePageConfig($this->hash, $cnf);

			CMSPlugin::redirectConfigPage();
		}
	}

	function execute(){
		parent::__construct();

		$this->addLabel("page_name", array(
			"text" => MultiplePageFormUtil::getPageName($this->hash)
		));

		self::_buildConfigForm();
	}

	private function _buildConfigForm(){
		$cnf = MultiplePageFormUtil::readJson($this->hash);

		$this->addForm("form");

		$this->addLabel("page_type", array(
			"text" => MultiplePageFormUtil::getTypeText($cnf["type"])
		));

		$this->addTextArea("page_description", array(
			"name" => "Config[description]",
			"value" => (isset($cnf["description"])) ? $cnf["description"] : ""
		));

		$this->addSelect("next_page_type", array(
			"name" => "Config[next]",
			"options" => MultiplePageFormUtil::getPageItemList($this->hash),
			"selected" => (isset($cnf["next"])) ? $cnf["next"] : ""
		));
	}

	function setPluginObj($pluginObj){
		$this->pluginObj = $pluginObj;
	}

	function setHash($hash){
		$this->hash = $hash;
	}
}
