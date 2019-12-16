<?php

/**
 * @entity SOYShop_UserStorage
 */
abstract class SOYShop_UserStorageDAO extends SOY2DAO{
	
	/**
	 * @return id
	 * @trigger onInsert
	 */
	abstract function insert(SOYShop_UserStorage $bean);
	
	/**
	 * @return object
	 */
	abstract function getById($id);
	
	/**
	 * @return list
	 */
	abstract function getByUserId($userId);
	
	/**
	 * @return object
	 */
	abstract function getByToken($token);
	
	/**
	 * @final
	 */
	function onInsert($query, $binds){
		$binds[":uploadDate"] = time();
		return array($query, $binds);
	}
}
?>