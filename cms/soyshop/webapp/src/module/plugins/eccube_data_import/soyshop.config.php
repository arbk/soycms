<?php
class ECCUBEDataImportConfig extends SOYShopConfigPageBase{

	/**
	 * @return string
	 */
	function getConfigPage(){
		
		SOY2::import("module.plugins.eccube_data_import.config.ECCUBEDataImportConfigFormPage");
		$form = SOY2HTMLFactory::createInstance("ECCUBEDataImportConfigFormPage");
		$form->setConfigObj($this);
		$form->execute();
		return $form->getObject();
	}

	/**
	 * @return string
	 * 拡張設定に表示されたモジュールのタイトルを表示する
	 */
	function getConfigPageTitle(){
		return "EC CUBE データインポートプラグイン";
	}

}
SOYShopPlugin::extension("soyshop.config","eccube_data_import","ECCubeDataImportConfig");
?>