<?php
/*
 */
class AutoDeleteOrderInfo extends SOYShopInfoPageBase{

	function getPage($active = false){
		if($active){
			return '<a href="'.SOY2PageController::createLink("Config.Detail?plugin=auto_delete_order").'">キャンセル注文自動削除の設定</a>';
		}else{
			return "";
		}
	}
}
SOYShopPlugin::extension("soyshop.info", "auto_delete_order", "AutoDeleteOrderInfo");
