<?php

class CommonPointGrantCustomField extends SOYShopItemCustomFieldBase{

	const PLUGIN_ID = "common_point_base";

	private $itemAttributeDao;
	private $percentage;

	function doPost(SOYShop_Item $item){

		if(isset($_POST[self::PLUGIN_ID])){
			$percentage = soyshop_convert_number($_POST[self::PLUGIN_ID], 0);

			$dao = SOY2DAOFactory::create("shop.SOYShop_ItemAttributeDAO");
			try{
				$attr = $dao->get($item->getId(),self::PLUGIN_ID);
			}catch(Exception $e){
				$attr = new SOYShop_ItemAttribute();
			}

			if(!is_null($attr->getItemId())){
				$attr->setValue($percentage);
				$dao->update($attr);
			}else{
				$attr->setItemId($item->getId());
				$attr->setFieldId(self::PLUGIN_ID);
				$attr->setValue($percentage);

				$dao->insert($attr);
			}
		}
	}

	function getForm(SOYShop_Item $item){

		$html = array();
		$html[] = "<dt>ポイント</dt>";
		$html[] = "<dd>";
		$html[] = "<input type=\"text\" name=\"" . self::PLUGIN_ID . "\" value=\"" . self::getPercentage($item) . "\" style=\"width:40px;ime-mode:inactive;\">&nbsp;%";
		$html[] = "</dd>";

		return implode("\n", $html);
	}

	/**
	 * onOutput
	 */
	function onOutput($htmlObj, SOYShop_Item $item){

		$htmlObj->addLabel("item_point_grant_percentage", array(
			"soy2prefix" => SOYSHOP_SITE_PREFIX,
			"text" => SOY2Logic::createInstance("module.plugins.common_point_grant.logic.PointGrantLogic")->getPercentage($item)
		));

		//common_point_baseから持ってきた
		$htmlObj->addLabel("item_point_percentage", array(
			"soy2prefix" => SOYSHOP_SITE_PREFIX,
			"text" => self::getPercentage($item)
		));
	}

	function onDelete($id){
		$attributeDAO = SOY2DAOFactory::create("shop.SOYShop_ItemAttributeDAO");
		$attributeDAO->deleteByItemId($id);
	}

	function getPercentage(SOYShop_Item $item){
		self::prepare();

		try{
			return (int)$this->itemAttributeDao->get($item->getId(), self::PLUGIN_ID)->getValue();
		}catch(Exception $e){
			return $this->percentage;
		}
	}

	private function prepare(){
		if(!$this->itemAttributeDao) $this->itemAttributeDao = SOY2DAOFactory::create("shop.SOYShop_ItemAttributeDAO");
		if(!$this->percentage){
			SOY2::import("module.plugins.common_point_grant.util.PointGrantUtil");
			$config = PointGrantUtil::getConfig();
			$this->percentage = (int)$config["percentage"];
		}
	}
}

SOYShopPlugin::extension("soyshop.item.customfield", "common_point_grant", "CommonPointGrantCustomField");
