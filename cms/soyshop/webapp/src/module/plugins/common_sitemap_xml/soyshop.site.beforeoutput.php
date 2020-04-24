<?php
/*
 * soyshop.site.beforeoutput.php
 * Created: 2010/03/11
 */

class CommonSitemapXmlBeforeOutput extends SOYShopSiteBeforeOutputAction{

	private $csfConfigs;
	private $languages = array();

	function beforeOutput($page){
		$pageObj = $page->getPageObject();

		//カートページとマイページでは読み込まない
		if(!is_object($pageObj) || get_class($pageObj) != "SOYShop_Page") return;

		//sitemap.xmlでない場合は読み込まない
		if(!preg_match('/sitemap.xml/', $pageObj->getUri())) return;

		//フリーページ以外では読み込まない
		$pageType = $pageObj->getType();
		if($pageType != SOYShop_Page::TYPE_FREE) return;

		$pages = self::getPages();
		if(count($pages) == 0) return;

		//多言語プラグイン
		SOY2::import("util.SOYShopPluginUtil");
		SOY2::import("module.plugins.util_multi_language.util.UtilMultiLanguageUtil");
		if(SOYShopPluginUtil::checkIsActive("util_multi_language")){
			$this->languages = array_keys(UtilMultiLanguageUtil::allowLanguages());
		}

		//子商品の表示モードか？
		$isDisplayChildItem = SOYShop_ShopConfig::load()->getDisplayChildItem();

		$url = soyshop_get_site_url(true);

		header("Content-Type: text/xml");

		$html = array();
		$html[] = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		if(count($this->languages)){
			$html[] = "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">";
		}else{
			$html[] = "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:image=\"http://www.sitemaps.org/schemas/sitemap-image/1.1\" xmlns:video=\"http://www.sitemaps.org/schemas/sitemap-video/1.1\">";
		}


		foreach($pages as $obj){

			$uri = $obj->getUri();

			//多言語化プラグインで無視するurl
			if(count($this->languages)){
				$isStop = false;
				foreach($this->languages as $lang){
					if(strpos($uri, $lang . "/") === 0 || $lang == $uri) $isStop = true;
				}
				if($isStop) continue;
			}

			if($uri==SOYSHOP_TOP_PAGE_MARKER){
				$uri = "";

			//404の場合はスルー
			}else if($uri == SOYSHOP_404_PAGE_MARKER){
				continue;
			}

			switch($obj->getType()){
				case SOYShop_Page::TYPE_LIST:
					$value = array();
					$pageObject = $obj->getPageObject();
					switch($pageObject->getType()){
						case SOYShop_ListPage::TYPE_CATEGORY:
							//ディフォルトカテゴリがある場合
							if(!is_null($pageObject->getDefaultCategory())){
								$html[] = self::buildUrlTag($url, $uri . "/", "", 0.8, $obj->getUpdateDate());
							}

							$categoryIds = $pageObject->getCategories();
							foreach($categoryIds as $categoryId){
								$category = soyshop_get_category_object($categoryId);
								$html[] = self::buildUrlTag($url, $uri, $category->getAlias(), 0.5, $obj->getUpdateDate());
							}
							break;
						case SOYShop_ListPage::TYPE_FIELD:
							/**
							 * @ToDo 引数の設定の方は未着手
							 */
							$html[] = self::buildUrlTag($url, $uri . "/", "", 0.5, $obj->getUpdateDate());
							break;
						case SOYShop_ListPage::TYPE_CUSTOM:
							$moduleId = $pageObject->getModuleId();
							if(isset($moduleId)){
								//カスタムサーチフィールド
								if(strpos($moduleId, "custom_search_field") === 0){
									if(is_null($this->csfConfigs)){
											SOY2::import("module.plugins.custom_search_field.util.CustomSearchFieldUtil");
											$this->csfConfigs = CustomSearchFieldUtil::getConfig();
									}

									if(!count($this->csfConfigs)) break;
									foreach($this->csfConfigs as $fieldId => $config){
										if(!isset($config["sitemap"]) || !is_numeric($config["sitemap"]) || (int)$config["sitemap"] !== (int)$obj->getId()) continue;
										if(!isset($config["option"][UtilMultiLanguageUtil::LANGUAGE_JP]) || !strlen($config["option"][UtilMultiLanguageUtil::LANGUAGE_JP])) continue;

										foreach(explode("\n", $config["option"][UtilMultiLanguageUtil::LANGUAGE_JP]) as $index => $opt){
											$opt = trim($opt);
											if(strlen($opt)){
												$html[] = "	<url>";
												$html[] = "		<loc>" . $url . $uri . "/" . $fieldId . "/" . $opt . "</loc>";
												//多言語化
												if(count($this->languages)){
													foreach($this->languages as $lang){
														if(!self::isMultiLanguagePage($uri, $lang)) continue;
														if($lang == UtilMultiLanguageUtil::LANGUAGE_JP || !isset($config["option"][$lang]) || !strlen($config["option"][$lang])) continue;
														$multiOpts = explode("\n", $config["option"][$lang]);
														if(!isset($multiOpts[$index])) continue;
														$multiOpt = trim($multiOpts[$index]);
														if(!strlen($multiOpt)) continue;
														$html[] = self::buildMultiLangagePageUrl($url, $uri . "/" . $fieldId . "/" . $multiOpt, $lang);
													}
												}
		 										$html[] = "		<priority>0.5</priority>";
		 										$html[] = "		<lastmod>" . self::getDate($obj->getUpdateDate()) . "</lastmod>";
		 										$html[] = "	</url>";
											}
										}
									}
								}
							}
							break;
					}
					break;
				case SOYShop_Page::TYPE_DETAIL:
					$value = array();
					$items = self::getItems($obj->getId());
					if(count($items)){
						foreach($items as $item){
							if(!$item->isPublished()) continue;	//非公開の商品は除く
							if(!$isDisplayChildItem && is_numeric($item->getType())) continue;	//子商品を表示しないモードの場合は除く

							$html[] = self::buildUrlTag($url, $uri, $item->getAlias(), 0.8, $item->getUpdateDate());
						}
					}
					break;

				case SOYShop_Page::TYPE_COMPLEX:
				case SOYShop_Page::TYPE_FREE:
				case SOYShop_Page::TYPE_SEARCH:
				default:
					if(strpos($uri, ".xml") !== false) break;
					if(strpos($uri, ".html") == false && strlen($uri) > 1){
						$uri = $uri . "/";
					}
					$priority = (!strlen($uri)) ? "1.0" : "0.8";

					/** レビュープラグインからの自動出力 **/
					if(self::_isReviewPage() && self::_checkReviewPageId($obj->getId())){


						$reviews = self::_getReviewCountListEarchItems();
						if(count($reviews)){
							$div = self::_getDivideReviewCount();
							if($div > 0){
								foreach($reviews as $itemId => $cnt){
									$pageCnt = (int)max(1, ceil($cnt / $div));
									if($pageCnt > 0){
										for($pi = 1; $pi <= $pageCnt; $pi++){
											$html[] = self::buildUrlTag($url, $uri . "/" . $itemId . "/page-" . $pi . ".html", "", "0.5", $obj->getUpdateDate());
										}
									}
								}
							}
						}
					}else{
						$html[] = self::buildUrlTag($url, $uri, "", $priority, $obj->getUpdateDate());
					}

					break;
			}
		}

		//管理画面で手動で追加したURL分
		SOY2::import("module.plugins.common_sitemap_xml.util.SitemapXMLUtil");
		$configs = SitemapXMLUtil::getConfig();
		if(count($configs)){
			foreach($configs as $config){
				if(isset($config["url"]) && strlen($config["url"]) && strpos($config["url"], "http") === 0){
					$html[] = "	<url>";
					$html[] = "		<loc>" . htmlspecialchars($config["url"], ENT_QUOTES, "UTF-8") . "</loc>";

					//多言語
					if(count($this->languages)){
						foreach($this->languages as $lang){
							if(isset($config[$lang]) && strlen($config[$lang])){
								$langUrl = trim(htmlspecialchars($config[$lang], ENT_QUOTES, "UTF-8"));
								$html[] = '		<xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . $langUrl . '" />';
							}
						}
					}

					$html[] = "		<priority>0.5</priority>";
					$html[] = "		<lastmod>" . self::getDate($config["lastmod"]) . "</lastmod>";
					$html[] = "	</url>";
				}
			}
		}

		$html[] = "</urlset>";

		$page->addLabel("sitemap.xml", array(
			//"html" => "",
			"html" => implode("\n", $html),
			"soy2prefix" => SOYSHOP_SITE_PREFIX
		));
	}

	private function buildUrlTag($url, $uri, $alias, $priority = 0.8, $updateDate){
		if(strlen($alias)) $alias = "/" . $alias;
		$uriConcatedAlias = $uri . $alias;
		if(strpos($uriConcatedAlias, "//")) $uriConcatedAlias = str_replace("//", "/", $uriConcatedAlias);

		$html = array();
		$html[] = "	<url>";
		$html[] = "		<loc>" . $url . $uriConcatedAlias . "</loc>";
		//多言語化
		if(count($this->languages)){
			foreach($this->languages as $lang){
				if(self::isMultiLanguagePage($uri, $lang)){
					$html[] = self::buildMultiLangagePageUrl($url, $uriConcatedAlias, $lang);
				}
			}
		}
		$html[] = "		<priority>" . $priority . "</priority>";
		$html[] = "		<lastmod>" . self::getDate($updateDate) . "</lastmod>";
		$html[] = "	</url>";
		return implode("\n", $html);
	}

	private function isMultiLanguagePage($uri, $lang){
		if(!count($this->languages) || !array_search($lang, $this->languages)) return false;
		$filename = $lang . "_" . str_replace(array("/", "."), "_", $uri) . "_page.php";
		$filename = str_replace("__", "_", $filename);
		return file_exists(SOYSHOP_SITE_DIRECTORY . ".page/" . $filename);
	}

	private function buildMultiLangagePageUrl($url, $uri, $lang){
		return '		<xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . $url . $lang . "/" . $uri . '" />';
	}

	private function getDate($time){
		return date("Y", $time) . "-" . date("m", $time) . "-" . date("d", $time) . "T" . date("H", $time) . ":" . date("i", $time) . ":" . date("s", $time) . "+09:00";
	}

	private function getPages(){
		try{
			return SOY2DAOFactory::create("site.SOYShop_PageDAO")->get();
		}catch(Exception $e){
			return array();
		}
	}

	private function getItems($pageId){
		static $dao;
		if(is_null($dao)) $dao = SOY2DAOFactory::create("shop.SOYShop_ItemDAO");

		try{
			return $dao->getByDetailPageIdIsOpen($pageId);
		}catch(Exception $e){
			return array();
		}
	}

	/** レビュープラグイン **/
	private function _isReviewPage(){
		static $isActive;
		if(is_null($isActive)){
			$isActive = false;
			if(SOYShopPluginUtil::checkIsActive("item_review")){
				$config = self::_reviewConfig();
				$isActive = (isset($config["active_other_page"]) && (int)$config["active_other_page"] === 1);
			}
		}
		return $isActive;
	}

	private function _checkReviewPageId($pageId){
		$reviewPageId = self::_getReviewPageId();
		if($reviewPageId == $pageId) return true;

		//@ToDo スマホページを開いている場合
		SOY2::import("util.SOYShopPluginUtil");
		if(SOYShopPluginUtil::checkIsActive("util_mobile_check")){
			SOY2::import("module.plugins.util_mobile_check.util.UtilMobileCheckUtil");
			$mbCnf = UtilMobileCheckUtil::getConfig();
			if(isset($mbCnf["prefix_i"]) && strlen($mbCnf["prefix_i"])){
				return (self::_getMbReviewPageId($mbCnf["prefix_i"]) == $pageId);
			}
		}

		return false;
	}

	private function _getReviewPageId(){
		static $id, $pageDao;
		if(is_null($id)){
			$config = self::_reviewConfig();
			$id = (isset($config["review_page_id"]) && is_numeric($config["review_page_id"])) ? (int)$config["review_page_id"] : nul;
		}
		return $id;
	}

	private function _getMbReviewPageId($prefix){
		static $ids, $pageDao;
		if(isset($ids[$prefix])) return $ids[$prefix];
		if(is_null($pageDao)) $pageDao = SOY2DAOFactory::create("site.SOYShop_PageDAO");

		$mbPageUri = $prefix . "/" . soyshop_get_page_object(self::_getReviewPageId())->getUri();
		try{
			$ids[$prefix] = $pageDao->getByUri($mbPageUri)->getId();
		}catch(Exception $e){
			$ids[$prefix] = null;
		}
		return $ids[$prefix];
	}

	private function _getReviewCountListEarchItems(){
		SOY2::import("module.plugins.item_review.domain.SOYShop_ItemReviewDAO");
		return SOY2DAOFactory::create("SOYShop_ItemReviewDAO")->getReviewCountListEarchItems();
	}

	//1ページあたり何個レビューを表示するか？
	private function _getDivideReviewCount(){
		$config = self::_reviewConfig();
		return (isset($config["review_count"]) && is_numeric($config["review_count"]) && $config["review_count"] > 0) ? (int)$config["review_count"] : null;
	}

	private function _reviewConfig(){
		static $cnf;
		if(is_null($cnf)){
			SOY2::import("module.plugins.item_review.util.ItemReviewUtil");
			$cnf = ItemReviewUtil::getConfig();
		}
		return $cnf;
	}
}

SOYShopPlugin::extension("soyshop.site.beforeoutput", "common_sitemap_xml", "CommonSitemapXmlBeforeOutput");
