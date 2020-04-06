<?php

PublisherPlugin::register();

class PublisherPlugin{

	const PLUGIN_ID = "publisher";


	function getId(){
		return self::PLUGIN_ID;
	}

	function init(){
		CMSPlugin::addPluginMenu(self::PLUGIN_ID,array(
			"name"=>"静的化プラグイン",
			"description"=>"",
			"author"=>"齋藤毅",
			"url"=>"http://saitodev.co",
			"mail"=>"tsuyoshi@saitodev.co",
			"version"=>"0.9"
		));
//		CMSPlugin::addPluginConfigPage(self::PLUGIN_ID,array(
//			$this,"config_page"
//		));

		if(CMSPlugin::activeCheck(self::PLUGIN_ID)){
			//管理画面側
			if(!defined("_SITE_ROOT_")){
				CMSPlugin::setEvent('onPageUpdate', self::PLUGIN_ID, array($this, "onPageUpdate"));
				CMSPlugin::setEvent('onPageRemove', self::PLUGIN_ID, array($this, "onPageUpdate"));

				CMSPlugin::setEvent('onEntryUpdate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
				CMSPlugin::setEvent('onEntryCreate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
				CMSPlugin::setEvent('onEntryRemove', self::PLUGIN_ID, array($this, "onEntryUpdate"));
				CMSPlugin::setEvent('onEntryCopy', self::PLUGIN_ID, array($this, "onEntryUpdate"));
			//公開側
			}else{
				CMSPlugin::setEvent('onOutput',self::PLUGIN_ID, array($this,"onOutput"), array("filter"=>"all"));
			}
		}
	}

	function onOutput($arg){
		$html = &$arg["html"];
		$page = &$arg["page"];

		//アプリケーションページと404ページの場合は静的化しない
		if($page->getPageType() == Page::PAGE_TYPE_APPLICATION || $page->getPageType() == Page::PAGE_TYPE_ERROR) return $html;

		//GETがある場合は検索ページと見なして対象外とする
		if(isset($_GET["q"])) return $html;

		//@ToDo そのうち禁止するURLの設定を行いたい	cms:module="common.entry_calendar"を使用している場合は静的化を禁止
		if(strpos($html, "cms:blog=")) return $html;

		//GETの値がある場合は対象外
		if(isset($_SERVER["REDIRECT_QUERY_STRING"]) && strpos($_SERVER["REDIRECT_QUERY_STRING"], "pathinfo") != 0) return $html;

		//URIにsearchとresultがある場所は検索結果ページと見なして、静的化の対象外とする
		if(strpos($page->getUri(), "search") !== false || strpos($page->getUri(), "result") !== false) return $html;

		//ブログページの場合はトップページのみ静的化の対象とする
		if($page->getPageType() == Page::PAGE_TYPE_BLOG){

			//ブログの記事詳細の場合は少し趣向を変える /サイトID/.cache/ページID/記事ID.html
			$webPage = &$arg["webPage"];
			switch($webPage->mode){
				case CMSBlogPage::MODE_ENTRY:
				case CMSBlogPage::MODE_MONTH_ARCHIVE:
				case CMSBlogPage::MODE_CATEGORY_ARCHIVE:
					self::generateStaticHTMLFileOnEntry($html);
					break;
				case CMSBlogPage::MODE_RSS:
				case CMSBlogPage::MODE_POPUP:
					return $html;
			}

			//PATH_INFOがある場合はトップではないとみなす
			/**
			 * @ToDo もっときれいな書き方を検討する
			 */
			if(isset($_SERVER["PATH_INFO"])){

				//ページャから出力されたページは除外 この処理はブログトップのみ
				if(self::checkIsBlogTopPage($page) && preg_match('/page-[0-9]*/', $_SERVER["PATH_INFO"])){
					//何もしない → そのまま返す に変更
					return $html;
				}else{
					return $html;
				}
			}
		}


		//トップページである
		if(!strlen($page->getUri())){
			//ルート直下
			if(self::checkIsDomainRoot(trim($arg["webPage"]->siteRoot, "/")) && file_exists($_SERVER["DOCUMENT_ROOT"] . "/index.php") && !file_exists($_SERVER["DOCUMENT_ROOT"] . "/index.html")){
				file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/index.html", $html);
			}else{
				if(!file_exists(_SITE_ROOT_ . "/index.html")){
					file_put_contents(_SITE_ROOT_ . "/index.html", $html);
				}
			}

			//ブログトップページのページャから出力されたページ
			//self::generateStaticHTMLFile($html);
		//それ以外のページ
		}else{
			self::generateStaticHTMLFile($html);
		}

		return $html;
	}

	private function generateStaticHTMLFile($html){
		$currentDir = $_SERVER["DOCUMENT_ROOT"];
		$dirs = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
		foreach($dirs as $dir){

			//繰り返し中に嫌だが、jsonとxmlの場合は処理を止める
			if(strpos($dir, ".json") || strpos($dir, ".xml")) return $html;

			if(strpos($dir, ".html") || strpos($dir, ".php")) break;

			//この２つのディレクトリは確実に関係ないたた調べるのを省く
			if($dir == "fonts" || $dir == "images") break;;
			$currentDir .= "/" . $dir;
			if(!file_exists($currentDir) && strlen($currentDir) <= 100){
				mkdir($currentDir);
			}
		}

		//配列の最後の値がhtmlかどうかを確認する
		$lastDir = end($dirs);

		if(file_exists($currentDir)){
			if(strpos($lastDir, ".html")){
				file_put_contents($currentDir . "/" . $lastDir, $html);
			}else{
				file_put_contents($currentDir . "/index.html", $html);
			}
		}
	}

	// /サイトID/.cache/ページID/記事ID.html
	private function generateStaticHTMLFileOnEntry($html){
		if(!isset($_SERVER["PATH_INFO"])) return;
		$pathInfo = $_SERVER["PATH_INFO"];
		if(!strlen($pathInfo)) return;

		$alias = trim(substr($pathInfo, strrpos($pathInfo, "/")), "/");

		$dir = _SITE_ROOT_ . "/.cache/static_cache/";
		if(!file_exists($dir)) mkdir($dir);

		if(is_numeric($alias)){
			$dir .= "n/";
			if(!file_exists($dir)) mkdir($dir);
		}else{
			$dir .= "s/";
			if(!file_exists($dir)) mkdir($dir);
		}

		$hash = md5($pathInfo);
		for($i = 0; $i < 10; $i++){
			$dir .= substr($hash, 0, 1) . "/";
			if(!file_exists($dir)) mkdir($dir);
			$hash = substr($hash, 1);
		}

		file_put_contents($dir . $hash . ".html", $html);
	}

	private function checkIsBlogTopPage(Page $page){
		$obj = $page->getPageConfigObject();
		$uri = $_SERVER["REQUEST_URI"];
		if(
			strpos($uri, $obj->entryPageUri) ||
			strpos($uri, $obj->monthPageUri) ||
			strpos($uri, $obj->categoryPageUri)
		){
			return false;
		}else{
			return true;
		}
	}

	private function checkIsDomainRoot($siteId){
		//サイトIDがない場合はルート設定
		if(!strlen($siteId)) return true;

		$old = CMSUtil::switchDsn();
		$dao = new SOY2DAO();
		try{
			$res = $dao->executeQuery("SELECT isDomainRoot FROM Site WHERE site_id = :siteId", array(":siteId" => $siteId));
		}catch(Exception $e){
			return false;
		}
		CMSUtil::resetDsn($old);

		return (isset($res[0]["isDomainRoot"]) && (int)$res[0]["isDomainRoot"] === 1);
	}

	function onPageUpdate($arg){
		if(!isset($arg["new_page"])) return;

		$page = $arg["new_page"];

		$uri = $page->getUri();

		//ルート設定していない場合はURIの頭にサイトIDを付与する
		if(!UserInfoUtil::getSiteIsDomainRoot()){
			$uri = UserInfoUtil::getSite()->getSiteId() . "/" . $uri;
		}

		if(!strpos($uri, ".html")) {
			$uri = rtrim($uri, "/");
			$uri .= "/index.html";
		}

		$path = $_SERVER["DOCUMENT_ROOT"] . "/" . $uri;
		if(file_exists($path)) unlink($path);
	}

	function onEntryUpdate($arg){

		//記事を更新した時にルート直下のindex.htmlを削除する
		if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/index.html")){
			unlink($_SERVER["DOCUMENT_ROOT"] . "/index.html");
		}else{
			if(file_exists(UserInfoUtil::getSiteDirectory(true) . "index.html")){
				unlink(UserInfoUtil::getSiteDirectory(true) . "index.html");
			}
		}

		//ブログのトップページ周りのファイルを削除
		$rootDir = $_SERVER["DOCUMENT_ROOT"];
		$dirs = scandir($rootDir);
		foreach($dirs as $dir){
			if(strpos($dir, ".") === 0) continue;
			if(preg_match('/page-[0-9]*/', $dir)){
				$file = $rootDir . "/" . $dir . "/index.html";
				if(file_exists($file)){
					unlink($file);
				}
			}
		}

		//サイトディレクトリ以下のブログページを調べる
		$siteDir = UserInfoUtil::getSiteDirectory(true);
		$dirs = scandir($siteDir);
		foreach($dirs as $dir){
			if(strpos($dir, ".") === 0) continue;
			if(preg_match('/page-[0-9]*/', $dir)){
				$file = $siteDir . $dir . "/index.html";
				if(file_exists($file)){
					unlink($file);
				}
			}
		}
	}

//	function config_page(){
//
//		include_once(dirname(__FILE__) . "/config/SitemapConfigFormPage.class.php");
//		$form = SOY2HTMLFactory::createInstance("SitemapConfigFormPage");
//		$form->setPluginObj($this);
//		$form->execute();
//		return $form->getObject();
//	}

	public static function register(){

		$obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
		if(!$obj){
			$obj = new PublisherPlugin();
		}

		CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
	}
}
