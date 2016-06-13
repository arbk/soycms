<?php
class CustomSearchFieldConfig extends SOYShopConfigPageBase{

	/**
	 * @return string
	 */
	function getConfigPage(){
		//通常の設定画面
		if(isset($_GET["eximport"])){
			include_once(dirname(__FILE__) . "/config/CustomSearchExImportPage.class.php");
			$form = SOY2HTMLFactory::createInstance("CustomSearchExImportPage");
		//一括設定画面
		}elseif(isset($_GET["collective"])){
			include_once(dirname(__FILE__) . "/config/collective/SettingPage.class.php");
			$form = SOY2HTMLFactory::createInstance("SettingPage");
		}else{
			include_once(dirname(__FILE__) . "/config/CustomSearchFieldConfigFormPage.class.php");
			$form = SOY2HTMLFactory::createInstance("CustomSearchFieldConfigFormPage");
		}
			
		$form->setConfigObj($this);
		$form->execute();
		return $form->getObject();
	}

	/**
	 * @return string
	 */
	function getConfigPageTitle(){
		return "カスタムサーチフィールド";
	}
}
SOYShopPlugin::extension("soyshop.config", "custom_search_field", "CustomSearchFieldConfig");
?>