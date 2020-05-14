<?php
class SOYInquiryConnectorConfig extends SOYShopConfigPageBase{

	/**
	 * @return string
	 */
	function getConfigPage(){

		$form = SOY2HTMLFactory::createInstance("SOYInquiryConnectorConfigFormPage");
		$form->setConfigObj($this);
		$form->execute();
		return $form->getObject();
	}

	/**
	 * @return string
	 * 拡張設定に表示されたモジュールのタイトルを表示する
	 */
	function getConfigPageTitle(){
		return "SOY Inquiry連携プラグインの設定";
	}
}
SOYShopPlugin::extension("soyshop.config","soyinquiry_connector","SOYInquiryConnectorConfig");

class SOYInquiryConnectorConfigFormPage extends WebPage{

	private $config;

	function __construct(){
		SOY2DAOFactory::importEntity("SOYShop_DataSets");
		include_once(dirname(__FILE__) . "/common.php");
	}

	function doPost(){

		if(soy2_check_token()&&isset($_POST["Config"])){
			$config = $_POST["Config"];
			SOYShop_DataSets::put("soyinquiry_connector_config", $config);
			$this->config->redirect("updated");
		}
	}

	function execute(){

		$config = SOYInquiryConnectorCommon::getConfig();

		parent::__construct();

		$this->addForm("form");

		$this->addInput("url", array(
			"name" => "Config[url]",
			"value" => (isset($config["url"])) ? $config["url"] : ""
		));
	}

	function getTemplateFilePath(){
		return dirname(__FILE__) . "/soyshop.config.html";
	}

	function setConfigObj($obj) {
		$this->config = $obj;
	}
}
?>
