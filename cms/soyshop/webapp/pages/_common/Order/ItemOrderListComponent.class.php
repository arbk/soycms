<?php

SOYShopPlugin::load("soyshop.item.option");
class ItemOrderListComponent extends HTMLList {

	protected function populateItem($itemOrder) {

		$item = soyshop_get_item_object($itemOrder->getItemId());

		$itemExists = ((int)$itemOrder->getItemId() > 0 && method_exists($item, "getCode") && strlen($item->getCode()) > 0);
		$this->addLink("item_id", array(
			"text" => $itemExists ? $item->getCode() : "Deleted Item (ID=" . $itemOrder->getItemId() . ")",
			"link" => $itemExists ? SOY2PageController::createLink("Item.Detail." . $itemOrder->getItemId()) : "",
		));

		$this->addInput("index_hidden", array(
			"name" => "Item[" . $itemOrder->getId() . "]",
			"value" => $itemOrder->getId()
		));

		//item_idが0の場合は名前を表示する
		$this->addLabel("item_name", array(
			"text" => ((int)$itemOrder->getItemId() === 0 || strpos($item->getCode(), "_delete_") === false) ? $itemOrder->getItemName() : "---"
		));

		$this->addLabel("item_option", array(
			"html" => ($itemOrder instanceof SOYShop_ItemOrder) ? soyshop_build_item_option_html_on_item_order($itemOrder) : ""
		));

		$this->addLabel("item_price", array(
			"text" => is_numeric($itemOrder->getItemPrice()) ? number_format($itemOrder->getItemPrice()) : $itemOrder->getItemPrice()
		));

		$this->addLabel("item_count", array(
			"text" => number_format($itemOrder->getItemCount())
		));

		$this->addLabel("item_total_price", array(
			"text" => number_format($itemOrder->getTotalPrice())
		));
	}
}
