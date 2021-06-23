<?php
//クライアント側からの設定ファイル

/* ここからcommon.inc.phpのコピー+α */

// PHPの設定
require_once(__DIR__."/config/php.config.php");

// 現在時刻
define("SOYCMS_NOW", BASE_TIME);

// SOY2のinclude
require_once(__DIR__."/lib/soy2_build.php");
//require_once(__DIR__."/lib/magic_quote_gpc.php");
//require_once(__DIR__."/lib/json_lib.php");

// CGIモードの判断
define("SOYCMS_PHP_CGI_MODE", RUN_CGI);

////設定ファイルのinclude
//if(file_exists(__DIR__."/config/custom.config.php")){
//  //開発用orカスタマイズ用設定ファイル（config/custom.config.php）があればそっちを読み込む
//  include_once(__DIR__."/config/custom.config.php");
//}else{
  //標準設定ファイル
  require_once(__DIR__."/soycms.config.php");
//}

// 共通ソースコード
SOY2::RootDir(__DIR__."/");

// SOY2DAOの設定
SOY2ActionConfig::ActionDir(__DIR__."/action/");
SOY2DAOConfig::DaoDir(__DIR__."/domain/");
SOY2DAOConfig::EntityDir(__DIR__."/domain/");
SOY2DAOConfig::setOption("connection_failure", "throw");
//SOY2DAOConfig::DaoCacheDir(dirname(__DIR__)."/cache_dao/");

// SOY2HTMLの設定
SOY2HTMLConfig::setOption("output_html", true);
SOY2HTMLPlugin::addPlugin("page", "PagePlugin");
SOY2HTMLPlugin::addPlugin("link", "LinkPlugin");
SOY2HTMLPlugin::addPlugin("src", "SrcPlugin");
SOY2HTMLPlugin::addPlugin("display", "DisplayPlugin");
SOY2HTMLPlugin::addPlugin("panel", "PanelPlugin");
SOY2HTMLPlugin::addPlugin("message", "MessagePlugin");
SOY2HTMLPlugin::addPlugin("custom", "CustomPlugin");

// プラグインのディレクトリ
define("CMS_BLOCK_DIRECTORY", __DIR__."/site_include/block/");
//define("CMS_PAGE_DIRECTORY",  __DIR__."/site_include/page/");
define("CMS_PAGE_PLUGIN", __DIR__."/site_include/plugin/");

// ユーザの設定ファイル
//if(file_exists(__DIR__."/config/user.config.php")){
  require_once(__DIR__."/config/user.config.php");
//}

// 設定ファイルの切り替え
//include_once(SOY2::RootDir()."config/normal.php");

require_once(SOY2::RootDir()."config/db/".SOYCMS_DB_TYPE.".php");

/* ここまでcommon.inc.phpのコピー+α */


/* サイトIDを定義する */
define("_SITE_ID_", substr(_SITE_ROOT_, strrpos(_SITE_ROOT_, DIRECTORY_SEPARATOR)+1));

//Utilty
SOY2::import('util.CMSUtil');
SOY2::import('util.CMSPlugin');
SOY2::import("util.UserInfoUtil");

//site_include
//SOY2::import('site_include.CMSPage');
//SOY2::import('site_include.CMSBlogPage');
////SOY2::import('site_include.CMSMobilePage');
//SOY2::import('site_include.CMSApplicationPage');
SOY2::import('site_include.CMSPageLinkPlugin');
SOY2::import('site_include.CMSPagePluginBase');
SOY2::import('site_include.CMSLabel');
SOY2::import('site_include.CMSPageController');
SOY2::import('site_include.DateLabel');
SOY2::import('site_include.DisplayCtrlModel');

//if(defined("SOYCMS_ALLOW_PHP_SCRIPT")){
  define("SOY2HTML_ALLOW_PHP_SCRIPT", SOYCMS_ALLOW_PHP_SCRIPT);
//}else{
//  define("SOY2HTML_ALLOW_PHP_SCRIPT",false);
//}

//if(defined("SOYCMS_ASP_MODE")){
//  $_SERVER["SCRIPT_NAME"] = "";
//  $_SERVER["DOCUMENT_ROOT"] = _SITE_ROOT_;
//}

SOY2HTMLConfig::CacheDir(_SITE_ROOT_."/".SOYCMS_CACHE_DIRNAME."/");
SOY2DAOConfig::DaoCacheDir(_SITE_ROOT_."/".SOYCMS_CACHE_DIRNAME."/");
