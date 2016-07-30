<?php

class GoogleAnalyticsUtil{
	
	const INSERT_TAG_DISPLAY = 1;
	const INSERT_TAG_NOT_DISPLAY = 0;
	
	public static function getConfig(){
		return SOYShop_DataSets::get("google_analytics", array(
			"tracking_code" => "",
			"insert_to_head" => 0
		));
	}
	
	public static function saveConfig($values){
		SOYShop_DataSets::put("google_analytics", $values);
	}
	
	public static function getPageDisplayConfig(){
		$config = SOYShop_DataSets::get("google_analytics_page_config", null);
		
		if(is_null($config)){
			
			$pages = self::getPages();
			
			//
			$config = array();

			foreach($pages as $page){
				$config[$page->getId()] = self::INSERT_TAG_DISPLAY;
			}
		}
		
		return $config;
	}
	
	public static function savePageDisplayConfig($array){
		
		$pages = self::getPages();
		
		$config = array();
		foreach($pages as $page){
			$pageId = $page->getId();
			$config[$pageId] = (in_array($pageId, $array)) ? self::INSERT_TAG_DISPLAY : self::INSERT_TAG_NOT_DISPLAY;
		}
		SOYShop_DataSets::put("google_analytics_page_config", $config);
	}
	
	private static function getPages(){
		try{
			return SOY2DAOFactory::create("site.SOYShop_PageDAO")->get();
		}catch(Exception $e){
			return array();
		}
	}
}
?>