<?php
include(dirname(__FILE__) . "/common.php");

class DeliveryPage extends WebPage{

	private $cart;

	function doPost(){
		//あえてsoy2_check_tokenなし

		$cart = $this->cart;

		//まずはエラーチェックのみ
		if(!isset($_POST["delivery_module"]) || strlen($_POST["delivery_module"]) < 1){
			$cart->addErrorMessage("delivery","配送方法が選択されていません。");
			$res = true;
		}else{
			$cart->removeErrorMessage("delivery");
		}

		/* 古いのをクリア */
		$cart->removeModule($cart->getAttribute("delivery_module"));
		$cart->clearAttribute("delivery_module");

		//配送
		if(!$cart->hasError("delivery")){
			$moduleId = @$_POST["delivery_module"];
			$cart->setAttribute("delivery_module", $moduleId);

			$moduleDAO = SOY2DAOFactory::create("plugin.SOYShop_PluginConfigDAO");
			$deliveryModule = $moduleDAO->getByPluginId($moduleId);
			SOYShopPlugin::load("soyshop.delivery", $deliveryModule);

			SOYShopPlugin::invoke("soyshop.delivery", array(
				"mode" => "select",
				"cart" => $cart
			));
		}


		$cart->save();
		if($cart->hasError()){
			SOY2PageController::jump("Order.Register.Delivery");
		}else{
			SOY2PageController::jump("Order.Register");
		}
	}

    function __construct($args) {
		SOYShopPlugin::active("soyshop.delivery");
		$this->cart = AdminCartLogic::getCart();

		parent::__construct();

		$this->addForm("form");
		$this->createAdd("delivery_method_list", "Delivery_methodList", array(
			"list" => $this->cart->getDeliveryMethodList(),
			"selected" => $this->cart->getAttribute("delivery_module")
		));

		//エラー文言
		$error = $this->cart->getErrorMessage("delivery");
		$this->addLabel("error", array(
			"html" => nl2br(htmlspecialchars($error, ENT_QUOTES, "UTF-8")),
			"visible" => isset($error) && strlen($error)
		));
   }

	function getCSS(){
		return array(
			"./css/admin/order_register.css"
		);
	}
}

/**
 * @class Delivery_methodList
 * @generated by SOY2HTML
 */
class Delivery_methodList extends HTMLList{
	private $selected = "";

	protected function populateItem($entity, $key, $counter, $length){
		$this->addCheckBox("delivery_method", array(
			"name" => "delivery_module",
			"value" => $key,
			"selected" => ( ($this->selected == $key) || ($length == 1) ),
			"label" => (isset($entity["name"])) ? $entity["name"] : ""
		));

		$this->addLabel("delivery_name", array(
			"text" => (isset($entity["name"])) ? $entity["name"] : ""
		));

		$this->addLabel("delivery_description", array(
			"html" => (isset($entity["description"])) ? $entity["description"] : ""
		));
		
		$this->addLabel("delivery_charge", array(
			"text" => (isset($entity["price"]) && strlen($entity["price"])) ? number_format($entity["price"]) . " 円" : ""
		));		
	}

	function getSelected() {
		return $this->selected;
	}
	function setSelected($selected) {
		if(strlen($selected)){
			$this->selected = $selected;
		}
	}
}