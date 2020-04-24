<?php

class RefundManagerUtil {

	const FIELD_ID = "refund_manager";
	const TYPE_CANCEL = "cancel";
	const TYPE_CHANGE = "change";

	const ACCOUNT_TYPE_NORMAL = 1;
	const ACCOUNT_TYPE_CURRENT = 2;

	public static function save($params, $isProcessed, $orderId){

		//値の確認 種別がある場合は保存
		if(isset($params["type"])){
			$dao = self::dao();
			$obj = new SOYShop_OrderAttribute();
			$obj->setOrderId($orderId);
			$obj->setFieldId(self::FIELD_ID);
			$obj->setValue1(soy2_serialize($params));
			$obj->setValue2($isProcessed);	//処理済みか？

			try{
				$dao->insert($obj);
			}catch(Exception $e){
				try{
					$dao->update($obj);
				}catch(Exception $e){
					var_dump($e);
				}
			}
		}
	}

	public static function get($orderId, $everytime=false){
		if($everytime){	//隠しモード
			return self::_get2($orderId);
		}else{
			return self::_get($orderId);
		}
	}

	public static function getTypeTextByOrderId($orderId, $everytime=false){
		if($everytime){		//隠しモード
			list($values, $isProcessed) = self::_get2($orderId);
		}else{
			list($values, $isProcessed) = self::_get($orderId);
		}

		if(!isset($values["type"])) return "";
		switch($values["type"]){
			case self::TYPE_CANCEL:
				return "キャンセル";
			case self::TYPE_CHANGE:
				return "変更";
		}
	}

	public static function getAccountTypeList(){
		return self::_getAccountTypeList();
	}

	public static function getAccountTypeText($type){
		$types = self::_getAccountTypeList();
		return (isset($types[$type])) ? $types[$type] : "普通";
	}

	private static function _getAccountTypeList(){
		return array(
			self::ACCOUNT_TYPE_NORMAL => "普通",
			self::ACCOUNT_TYPE_CURRENT => "当座"
		);
	}

	private static function _get($orderId){
		static $values, $isProcessed;
		if(is_null($values)){
			try{
				$obj = self::dao()->get($orderId, self::FIELD_ID);
				//設置値と処理済みかどうかを返す
				$values = soy2_unserialize($obj->getValue1());
				$isProcessed = (int)$obj->getValue2();
			}catch(Exception $e){
				$values = array();
				$isProcessed = 0;
			}
		}
		return array($values, $isProcessed);
	}

	//値のみ取得	隠し機能
	private static function _get2($orderId){
		try{
			$obj = self::dao()->get($orderId, self::FIELD_ID);
			//設置値と処理済みかどうかを返す
			$values = soy2_unserialize($obj->getValue1());
			$isProcessed = (int)$obj->getValue2();
		}catch(Exception $e){
			$values = array();
			$isProcessed = 0;
		}
		return array($values, $isProcessed);
	}



	private static function dao(){
		static $dao;
		if(is_null($dao)) $dao = SOY2DAOFactory::create("order.SOYShop_OrderAttributeDAO");
		return $dao;
	}
}
