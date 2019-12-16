<?php
/**
 * @table soyshop_stock_history
 */
class SOYShop_StockHistory {

    /**
     * @column item_id
     */
    private $itemId;

    /**
     * @column update_stock
     */
    private $updateStock = 0;

	private $memo;

    /**
     * @column create_date
     */
    private $createDate;

    function getItemId(){
    	return $this->itemId;
    }
    function setItemId($itemId){
    	$this->itemId = $itemId;
    }

    function getUpdateStock(){
    	return $this->updateStock;
    }
    function setUpdateStock($updateStock){
    	$this->updateStock = $updateStock;
    }

    function getMemo(){
    	return $this->memo;
    }
    function setMemo($memo){
    	$this->memo = $memo;
    }

    function getCreateDate(){
    	return $this->createDate;
    }
    function setCreateDate($createDate){
    	$this->createDate = $createDate;
    }
}
