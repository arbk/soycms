<?php
////さくらの共有サーバのSSL対策
//if(isset($_SERVER["HTTP_X_SAKURA_FORWARDED_FOR"])){
//  $_SERVER["HTTPS"] = "on";
//  $_SERVER["SERVER_PORT"] = "443";
//}

/*
 * 共通の設定を記述
 */

// PHPの設定
require_once(__DIR__ . "/config/php.config.php");

// 現在時刻
define("SOYCMS_NOW", BASE_TIME);

// SOY2のinclude
require_once(__DIR__ . "/lib/soy2_build.php");
//require_once (__DIR__ . "/lib/magic_quote_gpc.php");
//require_once (__DIR__ . "/lib/json_lib.php");

// CGIモードの判断
define("SOYCMS_PHP_CGI_MODE", RUN_CGI);

////設定ファイルのinclude
//if(file_exists(__DIR__ . "/config/custom.config.php")){
//  //開発用orカスタマイズ用設定ファイル（config/custom.config.php）があればそっちを読み込む
//  include_once(__DIR__ . "/config/custom.config.php");
//}else{
  //標準設定ファイル
  require_once(__DIR__ . "/soycms.config.php");
//}

// 共通ソースコード
SOY2::RootDir(__DIR__ . "/");

// SOY2DAOの設定
SOY2ActionConfig::ActionDir(__DIR__ . "/action/");
SOY2DAOConfig::DaoDir(__DIR__ . "/domain/");
SOY2DAOConfig::EntityDir(__DIR__ . "/domain/");
SOY2DAOConfig::setOption("connection_failure", "throw");
if (defined("SOYCMS_VERSION")) {
    SOY2DAOConfig::setOption("cache_prefix", SOYCMS_VERSION . "_");
}

// SQLのディレクトリ
define("CMS_SQL_DIRECTORY", str_replace("\\", "/", __DIR__ . "/sql/"));

// SOY2HTMLの設定
if (defined("SOYCMS_VERSION")) {
    SOY2HTMLConfig::setOption("cache_prefix", SOYCMS_VERSION . "_");
}
SOY2HTMLConfig::setOption("output_html", true);
SOY2HTMLPlugin::addPlugin("page", "PagePlugin");
SOY2HTMLPlugin::addPlugin("link", "LinkPlugin");
SOY2HTMLPlugin::addPlugin("src", "SrcPlugin");
SOY2HTMLPlugin::addPlugin("display", "DisplayPlugin");
SOY2HTMLPlugin::addPlugin("panel", "PanelPlugin");
SOY2HTMLPlugin::addPlugin("message", "MessagePlugin");
SOY2HTMLPlugin::addPlugin("custom", "CustomPlugin");

// プラグインのディレクトリ
define("CMS_BLOCK_DIRECTORY", __DIR__ . "/site_include/block/");
//define("CMS_PAGE_DIRECTORY", __DIR__ . "/site_include/page/");
define("CMS_PAGE_PLUGIN", __DIR__ . "/site_include/plugin/");
define("CMS_PAGE_PLUGIN_ADMIN_MODE", true);

// サイト側includeのファイル
define("CMS_SITE_INCLUDE", str_replace("\\", "/", __DIR__ . "/site.inc.php"));

// ユーザの設定ファイル
//if(file_exists(__DIR__ . "/config/user.config.php")){
  require_once(__DIR__ . "/config/user.config.php");
//}
// HTMLの設定
require_once(__DIR__ . "/config/html.config.php");

// 設定ファイルの切り替え
// if( defined("SOYCMS_ASP_MODE") ){
//   switch(SOYCMS_ASP_MODE){
//     case "release":
//       require_once (SOY2::RootDir() . "config/asp/release.php");
//       break;
//     case "test":
//       require_once (SOY2::RootDir() . "config/asp/test.php");
//       break;
//     case "develop":
//     default:
//       require_once (SOY2::RootDir() . "config/asp/develop.php");
//       break;
//   }
//   SOY2::import("base.ASPSOY2DAO");
// }
// else{
//  include_once(SOY2::RootDir() . "config/normal.php");

if (file_exists(SOY2::RootDir() . "config/db/" . SOYCMS_DB_TYPE . ".php")) {
    require_once(SOY2::RootDir() . "config/db/" . SOYCMS_DB_TYPE . ".php");
} else {
    include(SOY2::RootDir() . "error/db.php");
    exit();
}
//}

// 言語 と メッセージファイル の設定
if (SOYCMS_LANGUAGE != SOYSYS_BASE_LANG) {
    SOY2HTMLConfig::Language(SOYCMS_LANGUAGE);
}
$msgDir = __DIR__ . "/message/language/";
$msgDir = $msgDir . ( is_dir($msgDir . SOYCMS_LANGUAGE) ? SOYCMS_LANGUAGE : SOYSYS_BASE_LANG );
// メッセージファイルディレクトリ
define("CMS_SOYBOY_MESSAGE_DIR", $msgDir . "/soyboy");       // 大豆君
define("CMS_HELP_MESSAGE_DIR", $msgDir . "/help");           // ヘルプ
define("CMS_CONTROLPANEL_MESSAGE_DIR", $msgDir . "/soycms"); // 管理画面

// 管理側URLの設定
empty(SOYCMS_ADMIN_ROOT) || define("SOY2_DOCUMENT_ROOT", SOYCMS_ADMIN_ROOT);

// SOY CMS, SOY Shop
define("SOYCMS_COMMON_DIR", SOY2::RootDir());
//define("SOYSHOP_COMMON_DIR", dirname(SOY2::RootDir()) . "/soyshop/webapp/src/");

// headerの送信
header("Content-Type: text/html; charset=" . SOY2::CHARSET);
//header("Content-Language: ".SOYCMS_LANGUAGE);
soycms_admin_header_output();

// fatal error
register_shutdown_function("soycms_shutdown");
function soycms_shutdown()
{
    if (function_exists("error_get_last")) { // PHP 5.2.0 or later
        $error = error_get_last();
        if (is_array($error) && isset($error["type"])) {
            if ($error["type"] == E_ERROR || $error["type"] == E_RECOVERABLE_ERROR) {
                $exception = new ErrorException($error["message"], 100, $error["type"], $error["file"], $error["line"]);
                include(__DIR__ . "/error/admin.php");
                exit();
            }
        }
    }
}
