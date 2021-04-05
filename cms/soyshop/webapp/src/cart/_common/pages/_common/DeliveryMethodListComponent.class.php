<?php
/**
 * @class Delivery_methodList
 * @generated by SOY2HTML
 */
class DeliveryMethodListComponent extends HTMLList{
	private $selected;
	private $cart;

	protected function populateItem($entity, $key, $counter, $length){
		$this->addCheckBox("delivery_method", array(
			"name" => "delivery_module",
			"value" => $key,
			"selected" => ( ($this->selected == $key) || ($length == 1) ),
			"label" => MessageManager::get("LABEL_SELECT")
		));

		$this->addLabel("delivery_name", array(
			"text" => $entity["name"]
		));

		$this->addLabel("delivery_description", array(
			"html" => $entity["description"]
		));

		$this->addLabel("delivery_charge", array(
			"text" => (isset($entity["price"]) && is_numeric($entity["price"])) ? MessageManager::get("LABEL_PRICE", array("price" => soy2_number_format($entity["price"]))) : "",
		));

		//falseを返すことができる拡張ポイント
		return SOYShopPlugin::invoke("soyshop.delivery", array("mode" => "method", "moduleId" => $key, "cart" => $this->getCart()))->getMethod();
	}

	function getSelected() {
		return $this->selected;
	}
	function setSelected($selected) {
		if(strlen($selected)){
			$this->selected = $selected;
		}
	}

	function getCart(){
		return $this->cart;
	}
	function setCart($cart){
		$this->cart = $cart;
	}
}
