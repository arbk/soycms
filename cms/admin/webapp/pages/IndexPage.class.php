<?php
SOY2::import("domain.admin.Site");
class IndexPage extends CMSWebPageBase
{
    public function doPost()
    {
        if (soy2_check_token()) {
            if (isset($_POST["cache_clear"])) {
                set_time_limit(EXEC_TIME_NO_LIMIT);
                SOY2Logic::createInstance("logic.cache.CacheLogic")->clearCache(false);
                set_time_limit(EXEC_TIME_NORMAL);
                $this->addMessage("ADMIN_DELETE_CACHE");
//              $this->jump("?cache_cleared");
//              exit();
            } elseif (isset($_POST["file_mdchg"])) {
                set_time_limit(EXEC_TIME_NO_LIMIT);

                // CMS
                $root = dirname(SOY2::RootDir());
                CMSUtil::chmodAllIn($root . "/admin/cache/", F_MODE_FILE, F_MODE_DIR);
                CMSUtil::chmodAllIn($root . "/soycms/cache/", F_MODE_FILE, F_MODE_DIR);
//              CMSUtil::chmodAllIn($root . "/soyshop/cache/", F_MODE_FILE, F_MODE_DIR);
                CMSUtil::chmodAllIn($root . "/app/cache/", F_MODE_FILE, F_MODE_DIR, true);
                CMSUtil::chmodAllIn($root . "/common/log/", F_MODE_FILE, F_MODE_DIR);

                // CMS - ファイルマネージャー（elfinder）のサムネイル画像
                CMSUtil::chmodAllIn($root . "/soycms/tmb/", F_MODE_FILE, F_MODE_DIR, true);
//              CMSUtil::chmodAllIn($root . "/soyshop/tmb/", F_MODE_FILE, F_MODE_DIR, true);

                // Site
                $sites = SOY2Logic::createInstance("logic.admin.Site.SiteLogic")->getSiteList();
                foreach ($sites as $site) {
                    $sitePath = $site->getPath();
                    $file_list = CMSUtil::getFileListInDir($sitePath);
                    if (null===$file_list) {
                        continue;
                    }

                    // サイト直下の ファイル と 隠しディレクトリ
                    foreach ($file_list as $filename) {
                        $filePath = $site->getPath() . $filename;
                        if (is_file($filePath)) {
                            if (0 === strcasecmp(".php", substr($filename, -4))) {
                            // サイト直下のPHPファイル
                                @chmod($filePath, F_MODE_FILE_PXE);
                            } else {
                            // サイト直下のファイル (PHPファイル以外)
                                @chmod($filePath, F_MODE_FILE);
                            }
                        } elseif (is_dir($filePath)) {
                            if (0 <= strlen($filename) && $filename[0] === ".") {
                                // サイト直下の隠しディレクトリ
                                @chmod($filePath, F_MODE_DIR_HDN);
                                // サイト直下の隠しディレクトリ配下
                                CMSUtil::chmodAllIn($filePath, F_MODE_FILE, F_MODE_DIR, true);
                            }
                        } else {
                            continue;
                        }
                    }

                    // ユーザーファイルディレクトリ
                    CMSUtil::chmodAllInUserFilesDir($site->getPath(), true);
                }

                set_time_limit(EXEC_TIME_NORMAL);
                $this->addMessage("ADMIN_CHANGE_MODE");
//              $this->jump("?file_mdchged");
//              exit();
            }

            $this->jump("");
        }
    }

    public function __construct($arg)
    {
      //バージョンアップ時のキャッシュの自動削除
        $cacheLogic = SOY2Logic::createInstance("logic.cache.CacheLogic");
        if ($cacheLogic->checkCacheVersion()) {
            $cacheLogic->clearCache();
        }

        parent::__construct();

      /*
       * データベースのバージョンチェック
       * ここまででDataSetsを呼び出していないこと ← そのうち破綻する気がする
       * @TODO 初期管理者以外ではバージョンアップを促す文言を出すとか
       */
        $this->run("Database.CheckVersionAction");
        $this->run("Administrator.CheckAdminVersionAction");

      // ユーザに割り当てられたサイト/Appが１つのときは、そのサイトにログイン(redirect)するようにする。
        $this->run("SiteRole.DefaultLoginAction");

      // ファイルDB更新、キャッシュの削除
  //  $this->addForm("file_form");
        $this->addForm("cache_form");
        $this->addForm("file_mdchg_form");

  //  $this->addModel("cache_clear_massage", array(
  //      "visible"=>(isset($_GET["cache_cleared"]))
  //  ));
  //  $this->addModel("file_mdchg_massage", array(
  //      "visible"=>(isset($_GET["file_mdchged"]))
  //  ));

      //バージョン番号
        $this->addLabel("version", array(
        "text" => "version: ".SOYCMS_VERSION,
        ));

        $this->addLabel("cms_name", array(
        "text" => CMSUtil::getCMSName()
        ));

      // 現在のユーザーがログイン可能なサイトのみを表示する
        $loginableSiteList = SOY2Logic::createInstance("logic.admin.Site.SiteLogic")->getLoginableSiteListByUserId(UserInfoUtil::getUserId());
        $this->createAdd("list", "_common.Site.SiteListComponent", array(
        "list"=>$loginableSiteList
        ));

        $this->addModel("no_site", array(
        "visible"=>(count($loginableSiteList) < 1)
        ));

        $this->addLink("create_link", array(
        "link"=>SOY2PageController::createLink("Site.Create")
        ));

        $this->addLink("addAdministrator", array(
        "link"=>SOY2PageController::createLink("Administrator.Create")
        ));

      //アプリケーション ログイン可能なアプリケーションを読み込む
        $applications = SOY2Logic::createInstance("logic.admin.Application.ApplicationLogic")->getLoginiableApplicationLists();
        $this->createAdd("application_list", "_common.Application.ApplicationListComponent", array(
        "list"=>$applications
        ));

        $this->addModel("application_list_wrapper", array(
        "visible"=>(count($applications) > 0)
        ));

        $this->addModel("allow_php", array(
        "visible"=>SOYCMS_ALLOW_PHP_SCRIPT
        ));
        $this->addModel("allow_php_module", array(
        "visible"=>SOYCMS_ALLOW_PHP_MODULE
        ));
    }
}
