<?php
/**
 * @entity order.SOYShop_OrderStateHistory
 */
abstract class SOYShop_OrderStateHistoryDAO extends SOY2DAO{

    /**
	 * @return id
	 * @trigger onInsert
	 */
    abstract function insert(SOYShop_OrderStateHistory $bean);

    /**
     * @order id desc
     */
    abstract function getByOrderId($orderId);

    /**
     * @return list
     * @query order_id = :orderId AND order_date > :startDate AND order_date <= :endDate
     * @order id asc
     */
    abstract function getByOrderIdBetweenDate($orderId, $startDate, $endDate = 2147483647);

	/**
     * @final
     */
    function onInsert($query, $binds){
    	if(!isset($binds[":date"])) $binds[":date"] = time();
    	if(!isset($binds[":author"]) || !strlen($binds[":author"])){
			/*
    		 * 管理画面では管理者情報をを登録する
    		 * SOY ShopでUserInfoUtilが使えることはないのでは？
    		 */
    		if(class_exists("UserInfoUtil")){
    			$author = UserInfoUtil::getUserName();
				if(!strlen($author)) $author = UserInfoUtil::getUserId();
    		}else{
    			$author = SOY2ActionSession::getUserSession()->getAttribute("username");
				if(!strlen($author)) $author = SOY2ActionSession::getUserSession()->getAttribute("userid");
    		}

			//カートからの注文の場合は設定画面で決めたauthorにする
			if(!strlen($author)){
				SOY2::import("domain.config.SOYShop_ShopConfig");
				$author = SOYShop_ShopConfig::load()->getAutoOperateAuthorId();
			}

			$binds[":author"] = $author;
    	}
    	return array($query, $binds);
    }
}
