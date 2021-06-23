<?php
/**
 * @class ItemList
 * @generated by SOY2HTML
 */
class ItemListComponent extends HTMLList{

	private $DAO;

	private $ignoreStock;

	protected function populateItem($entity, $key){

		$item = soyshop_get_item_object($entity->getItemId());
		$url = soyshop_get_page_url(soyshop_get_page_object($item->getDetailPageId())->getUri(), $item->getAlias()) . "?index=" . $key; //末尾にindexを付けておく

		$this->addLink("item_link", array(
			"link" => $url
		));

		$this->addLink("item_name", array(
			"text" => $item->getOpenItemName(),
			"link" => $url
		));
		$this->addLabel("item_name_plain", array(
			"text" => $item->getOpenItemName(),
		));

		$this->addLabel("item_price", array(
			"text" => soy2_number_format($entity->getItemPrice()),
		));

		//上のタグがなぜか使えないので予備でもう一つ
		$this->addLabel("item_price_yobi", array(
			"text" => soy2_number_format($entity->getItemPrice()),
		));

		$this->addLabel("item_price_on_cart", array(
			"text" => soy2_number_format($entity->getItemPrice()),
		));

		$this->addLabel("item_id", array(
			"text" => $item->getCode(),
		));

		$this->addLabel("item_code", array(
			"text" => $item->getCode(),
		));

		$this->addImage("item_small_image", array(
			"src" => (!is_null($item->getId())) ? self::_getParentAndChildImage($item->getId(), "image_small") : ""
		));

		$this->addImage("item_large_image", array(
			"src" => (!is_null($item->getId())) ? self::_getParentAndChildImage($item->getId(), "image_large") : ""
		));

		SOYShopPlugin::invoke("soyshop.item.customfield", array(
			"item" => $item,
			"htmlObj" => $this
		));

		$this->addLabel("item_option", array(
			"html" => self::getItemOptionHtml($key)
		));

		$this->addModel("order_number_tr", array(
			"attr:id" => "order_number_" . $key
		));

		$this->addInput("order_number", array(
			"name" => "ItemCount[" . $key . "]",
			"value" => $entity->getItemCount()
		));

		$this->createAdd("order_number_text", "NumberFormatLabel", array(
			"text" => $entity->getItemCount()
		));

		$this->createAdd("item_total", "NumberFormatLabel", array(
			"text" => $entity->getTotalPrice()
		));

		$this->addLink("item_delete", array(
			"link" => soyshop_get_cart_url(true) . "?a=remove&index=" . $key
		));

		$itemCount = $entity->getItemCount();
		$openStock = $item->getOpenStock();

		//子商品
		if(is_numeric($item->getType())){
			$parent = soyshop_get_item_object($item->getType());

			//子商品の在庫管理設定をオン(子商品購入時に親商品の在庫数で購入できるか判断する)
			if(self::getShopConfig()->getChildItemStock()){
				//親商品の残り在庫数を取得
				$openStock = $parent->getStock();

				//子商品の注文数の合算を取得
				$itemCount = self::getChildItemOrders($parent->getId());
			}
		}else{
			$parent = new SOYShop_Item();
		}


		$itemStockErrorMessage = "";
		if(!class_exists("SOYShopPluginUtil")) SOY2::import("util.SOYShopPluginUtil");
		if(!SOYShopPluginUtil::checkIsActive("reserve_calendar")){	//通常モード
			$isItemStockError = ($itemCount > $openStock && !$this->ignoreStock);
			$itemStockErrorMessage = ($openStock > 0) ? MessageManager::get("STOCK_NOTICE", array("stock" => $openStock)) : MessageManager::get("NO_STOCK");
		}else{	//予約カレンダーモード
			SOY2::import("module.plugins.reserve_calendar.util.ReserveCalendarUtil");
			$schedule = ReserveCalendarUtil::getScheduleByItemIndexAndItemId(CartLogic::getCart(), $key, $entity->getItemId());
			if(!is_null($schedule->getId())){
				//定員数0
				if(!ReserveCalendarUtil::checkIsUnsoldSeatByScheduleId($schedule->getId())){
					$itemStockErrorMessage = "予約受付を終了しました";	//@ToDo 多言語化
				}

				//定員数オーバー
				$unsoldSeat = ReserveCalendarUtil::getCountUnsoldSeat($schedule);
				if($unsoldSeat < $itemCount){
					$itemStockErrorMessage = MessageManager::get("STOCK_NOTICE", array("stock" => $unsoldSeat));	//@ToDo 多言語化
				}
			}

			$isItemStockError = (strlen($itemStockErrorMessage));
		}

		//背景色の変更用
		$this->addModel("item_stock_error_tr", array(
			"visible" => ($isItemStockError || !$item->checkAcceptOrder())
		));

		$this->addLabel("item_stock_error", array(
			"visible" => $isItemStockError,
			"text" => $itemStockErrorMessage
		));

		$this->addLabel("item_order_error", array(
			"visible" => (!$item->checkAcceptOrder()),
			"text" => ($item->getOrderPeriodStart() > time()) ? MessageManager::get("ORDER_NO_ACCEPT_START") : MessageManager::get("ORDER_NO_ACCEPT_END")
		));

		//item++
		$this->addLink("add_item_link", array(
			"link" => soyshop_get_cart_url(true) . "?a=add&item=" . $item->getId() ."&count=1",
			"visible" => ($itemCount >= $entity->getItemCount())
		));

		//item--
		$this->addLink("sub_item_link", array(
			"link" => soyshop_get_cart_url(true) . "?a=add&item=" . $item->getId() . "&count=-1",
			"visible" => ($itemCount > 1)
		));

		/** 親商品関連のタグ **/
		$this->addLink("parent_name", array(
			"text" => $parent->getOpenItemName(),
			"link" => $url
		));
		$this->addLabel("parent_name_plain", array(
			"text" => $parent->getOpenItemName(),
		));

		$this->addLabel("parent_code", array(
			"text" => $parent->getCode(),
		));

		$this->addImage("parent_small_image", array(
			"src" => (!is_null($parent->getId())) ? self::_getParentAndChildImage($parent->getId(), "image_small") : ""
		));

		$this->addImage("parent_large_image", array(
			"src" => (!is_null($parent->getId())) ? self::_getParentAndChildImage($parent->getId(), "image_large") : ""
		));
	}

	private function _getParentAndChildImage($itemId, $key){
		$item = soyshop_get_item_object($itemId);
		if(strlen($item->getAttribute($key))) return soyshop_convert_file_path($item->getAttribute($key), $item);

		if(!is_numeric($item->getId())) return null;

		//親商品を取得してみる
		$parent = soyshop_get_item_object($item->getType());
		return soyshop_convert_file_path($parent->getAttribute($key), $parent);
	}

	private function getItemOptionHtml($key){
		if(is_null($key)) return "";

		SOYShopPlugin::load("soyshop.item.option");
		$htmls = SOYShopPlugin::invoke("soyshop.item.option", array(
			"mode" => "item",
			"index" => $key,
			"htmlObj" => $this
		))->getHtmls();

		if(!is_array($htmls) || !count($htmls)) return "";

		$html = array();
		foreach($htmls as $modId => $h){
			if(!strlen($h)) continue;
			$html[] = $h;
		}

		return implode("<br>", $html);
	}

	private function getChildItemOrders($itemId){
		$cart = CartLogic::getCart();

		$itemCount = 0;

		$items = $cart->getItems();
		if(count($items) > 0){
			$dao = new SOY2DAO();
			$sql = "select id from soyshop_item where item_type = :id";
			$binds = array(":id" => $itemId);
			try{
				$result = $dao->executeQuery($sql,$binds);
			}catch(Exception $e){
				return 0;
			}
			$ids = array();
			foreach($result as $value){
				$ids[] = $value["id"];
			}

			foreach($items as $item){
				if(in_array($item->getItemId(),$ids)){
					$itemCount = $itemCount + $item->getItemCount();
				}
			}
		}

		return $itemCount;
	}


	private function getShopConfig(){
		static $config;
		if(is_null($config)) $config = SOYShop_ShopConfig::load();
		return $config;
	}

	function setIgnoreStock($ignoreStock){
		$this->ignoreStock = $ignoreStock;
	}
	function getIgnoreStock(){
		return $this->ignoreStock;
	}
}
