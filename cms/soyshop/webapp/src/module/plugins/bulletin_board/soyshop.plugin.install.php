<?php
class BulletinBoardInstall extends SOYShopPluginInstallerBase{

	function onInstall(){
		//初期化時のみテーブルを作成する
		$dao = new SOY2DAO();

		$sqls = preg_split('/CREATE TABLE/', self::_sqls(), -1, PREG_SPLIT_NO_EMPTY) ;
		foreach($sqls as $sql){
			try{
				$dao->executeQuery("create table " . trim($sql));
			}catch(Exception $e){
				//
			}
		}

		//必要なプラグイン
		self::_initPlugins();

		//要らないデータベースのテーブルを削除
		self::_clearTable();

		//設定の変更
		SOY2::import("domain.config.SOYShop_ShopConfig");
		$cnf = SOYShop_ShopConfig::load();
		$cnf->setAppName("SOY Board");	//仮
		$cnf->setAppLogoPath(dirname(SOY2PageController::createLink("")) . "/app/css/images/main/logo.png");	//ロゴ画像の変更
		$cnf->setDisplayOrderAdminPage(0);	//注文ページを非表示
		$cnf->setDisplayItemAdminPage(0);	//商品ページを非表示
		$cnf->setAllowMailAddressLogin(0);	//メールアドレスでマイページにログインすることを禁止する
		$cnf->setAllowLoginIdLogin(1);		//ログインIDでマイページにログインすることを許可する
		$cnf->setDisplayUserOfficeItems(0);	//勤務先情報を非表示
		$cnf->setDisplayOrderButtonOnUserAdminPage(0);	//顧客詳細のページで注文ボタンを非表示
		//$cnf->setInsertDummyMailAddressOnAdminRegister(1);	//ダミーメールアドレスを許可する
		$cnf->setDisplayUserProfileItems(0);				//プロフィール関連を非表示
		$items = $cnf->getCustomerAdminConfig();

		//顧客の情報でtrueにするもの
		foreach($items as $key => $item){
			$items[$key] = false;
		}
		foreach(array("name", "mailAddress", "nickname", "accountId", "url") as $key => $v){
			$items[$v] = true;
		}
		$cnf->setCustomerAdminConfig($items);
		$cnf->setCustomerDisplayFormConfig($items);
		$cnf->setCustomerInformationConfig($items);

		SOYShop_ShopConfig::save($cnf);


		//Cartは封じる
		SOYShop_DataSets::put("config.cart.cart_id", "none");

		//MyPageに関する設定もしておきたい
		$mypageDir = SOY2::RootDir() . "mypage/bootstrap/";
		if(!file_exists($mypageDir)) mkdir($mypageDir);
		SOYShop_DataSets::put("config.mypage.id", "bootstrap");
		SOYShop_DataSets::put("config.mypage.title", "SOY Board");
		SOYShop_DataSets::put("config.mypage.title.no_logged_in", "ログイン - SOY Board");

		//テンプレート
		$html = file_get_contents(SOY2::RootDir() . "/logic/init/template/bryon/mypage/bootstrap.html");
		$html = str_replace("@@SOYSHOP_URI@@/", "/" . SOYSHOP_ID . "/", $html);
		file_put_contents(SOYSHOP_SITE_DIRECTORY . ".template/mypage/bootstrap.html", $html);

		copy(SOY2::RootDir() . "/logic/init/template/bryon/mypage/bootstrap.ini", SOYSHOP_SITE_DIRECTORY . ".template/mypage/bootstrap.ini");

		//ログイン後のURLの設定
		SOYShop_DataSets::put("config.mypage.url", "bulletin");
		SOYShop_DataSets::put("config.mypage.top", "board");

		//トップページ
		//マイページへのログインページへのリダイレクト
		copy(dirname(__FILE__) . "/template/complex/home.html", SOYSHOP_SITE_DIRECTORY . ".template/complex/home.html");
		copy(dirname(__FILE__) . "/template/complex/home.ini", SOYSHOP_SITE_DIRECTORY . ".template/complex/home.ini");

		//ページの作成
		$pageDao = SOY2DAOFactory::create("site.SOYShop_PageDAO");
		try{
			$page = $pageDao->getByUri(SOYShop_Page::URI_HOME);
		}catch(Exception $e){
			$page = new SOYShop_Page();
			$page->setName("リダイレクト");
			$page->setUri(SOYShop_Page::URI_HOME);
			$page->setType(SOYShop_Page::TYPE_COMPLEX);
			$page->setTemplate("home.html");
			SOY2Logic::createInstance("logic.site.page.PageCreateLogic")->create($page);
		}

		//サイトマップ
		copy(dirname(__FILE__) . "/template/free/sitemap.html", SOYSHOP_SITE_DIRECTORY . ".template/free/sitemap.html");
		copy(dirname(__FILE__) . "/template/free/sitemap.ini", SOYSHOP_SITE_DIRECTORY . ".template/free/sitemap.ini");

		try{
			$page = $pageDao->getByUri("sitemap.xml");
		}catch(Exception $e){
			$page = new SOYShop_Page();
			$page->setName("サイトマップ");
			$page->setUri("sitemap.xml");
			$page->setType(SOYShop_Page::TYPE_FREE);
			$page->setTemplate("sitemap.html");
			SOY2Logic::createInstance("logic.site.page.PageCreateLogic")->create($page);
		}

		//ユーザーカスタムサーチフィールドの初期化
		SOY2::import("module.plugins.user_custom_search_field.util.UserCustomSearchFieldUtil");
		$cnfs = UserCustomSearchFieldUtil::getConfig();
		if(!is_array($cnfs) || !count($cnfs)){
			try{
				$sql = file_get_contents(dirname(dirname(__FILE__)) . "/user_custom_search_field/sql/init_" . SOYSHOP_DB_TYPE . ".sql");
				$dao->executeQuery($sql);
			}catch(Exception $e){
				//
			}

			$cnfs = array();

			$logic = SOY2Logic::createInstance("module.plugins.user_custom_search_field.logic.UserDataBaseLogic");
			$fields = array(
				"management_side" => array("label" => "運営側アカウント", "type" => UserCustomSearchFieldUtil::TYPE_CHECKBOX, "option" => "運営側"),
				"profile" => array("label" => "紹介文", "type" => UserCustomSearchFieldUtil::TYPE_TEXTAREA),
			);

			foreach($fields as $fieldId => $field){
				$logic->addColumn($fieldId, $field["type"]);
				$cnfs[$fieldId] = array(
					"label" => $field["label"],
					"type" => $field["type"],
					"option" => (isset($field["option"])) ? $field["option"] : ""
				);
			}

			UserCustomSearchFieldUtil::saveConfig($cnfs);
		}

		//@ToDo SSLの設定
	}

	function onUnInstall(){
		//アンインストールしてもテーブルは残す
	}

	/**
	 * @return String sql for init
	 */
	private function _sqls(){
		return file_get_contents(dirname(__FILE__) . "/sql/init_" . SOYSHOP_DB_TYPE . ".sql");
	}

	private function _initPlugins(){
		$pluginIds = explode("\n", file_get_contents(dirname(__FILE__) . "/_init/plugins.txt"));
		if(is_numeric(strpos($_SERVER["HTTP_HOST"], "localhost"))){
			for($i = 0; $i < count($pluginIds); $i++){
				if(
					strpos($pluginIds[$i], "affiliate_") === 0 ||
					strpos($pluginIds[$i], "google_analytics") ||
					strpos($pluginIds[$i], "_gmo_") ||
					strpos($pluginIds[$i], "moba8") ||
					strpos($pluginIds[$i], "amazon_pay")
				){
					unset($pluginIds[$i]);
				}

			}
			$pluginIds = array_values($pluginIds);
		}

		$list = array();

		$dao = new SOY2DAO();
		try{
			$dao->executeUpdateQuery("UPDATE soyshop_plugins SET is_active = 0;");
			$dao->executeUpdateQuery("UPDATE soyshop_plugins SET is_active = 1 WHERE plugin_id IN ('" . implode("','", $pluginIds) . "');");
		}catch(Exception $e){
			var_dump($e);
			//
		}
	}

	private function _clearTable(){
		$dao = new SOY2DAO();

		$tables = array(
			"item",
			"item_attribute",
			"category",
			"category_attribute",
			"order",
			"order_state_history",
			"orders",
			"item_review",
			"breadcrumb",
			"favorite_item",
			"review_point"
		);

		foreach($tables as $t){
			try{
				$dao->executeUpdateQuery("DROP TABLE soyshop_" . $t);
			}catch(Exception $e){
				//
			}
		}
	}
}
SOYShopPlugin::extension("soyshop.plugin.install", "bulletin_board", "BulletinBoardInstall");
