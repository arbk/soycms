<?php
// PHP 設定ファイル
//  設定ファイルの読込順: php.config.php -> (soy2_build.php) -> user.config.php -> html.config.php


// base point in time
define("BASE_TIME", time());


// environment information normalization
function soycms_envinfo_normalize()
{
  // DOCUMENT_ROOT
  //  DOCUMENT_ROOTとして意図した値が取得できない場合は 次のコードを有効にする.
//  $doc_root = substr($_SERVER["SCRIPT_FILENAME"], 0, -strlen($_SERVER["SCRIPT_NAME"]));
//  if( is_dir($doc_root) ){ $_SERVER["DOCUMENT_ROOT"] = $doc_root; }
  // 正規化した絶対パスを設定. パス区切り「/」に統一, 末尾に「/」付与.
    $_SERVER["DOCUMENT_ROOT"] = str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"])) . "/";

  // さくらのレンタルサーバーの共有SSL対策
  //  さくらのレンタルサーバーでHTTPSを認識できない場合は 次のコードを有効にする.
//  if( isset($_SERVER["HTTP_X_SAKURA_FORWARDED_FOR"]) ){
//    $_SERVER["HTTPS"] = "on";
//    $_SERVER["SERVER_PORT"] = "443";
//  }
}
soycms_envinfo_normalize();


// environment information
//  runtime
define("RUN_CLI", "cli" === PHP_SAPI || defined("STDIN"));
define("RUN_CGI", stripos(PHP_SAPI, "cgi") !== false);
//  secure communication
define("COM_SECURE", isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");


// special file settings
//  front controller file
define("F_FRCTRLER", "index.php");
//  distributed configuration file
define("F_HTACCESS", ".htaccess");

// mode settings: ファイルモードの指定 (8進数)
//  standard
define("F_MODE_DIR", 0705);        // dir             ex: 0755, 0705
define("F_MODE_FILE", 0604);        // file            ex: 0644, 0604
//  user files
define("F_MODE_DIR_USR", F_MODE_DIR);  // user files dir  ex: 0777, 0707 or F_MODE_DIR
define("F_MODE_FILE_USR", F_MODE_FILE); // user file       ex: 0666, 0606 or F_MODE_FILE
//  special
define("F_MODE_DIR_HDN", 0700);        // hidden dir      ex: 0700
define("F_MODE_FILE_PXE", F_MODE_FILE); // php exec file   ex: 0755, 0705 or F_MODE_FILE
define("F_MODE_FILE_DISABLE", 0000);        // disable file        0000

// error settings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set("display_errors", "Off");
//  error log
define("ERR_LOG_DIR", dirname(__DIR__) . "/log");
define("ERR_LOG_FILE", ERR_LOG_DIR . "/error-" . date("Ym") . ".log");
if (is_dir(ERR_LOG_DIR) && is_writable(ERR_LOG_DIR)) {
    ini_set("log_errors", 1);
    ini_set("error_log", ERR_LOG_FILE);
//error_log を指定しない場合 Apache のログに出力される.
}

// execution settings
if (RUN_CLI) {
    define("EXEC_TIME_NORMAL", 0);
    define("EXEC_TIME_LONG", 0);
    define("EXEC_TIME_NO_LIMIT", 0);
} else {
    define("EXEC_TIME_NORMAL", 60);
    define("EXEC_TIME_LONG", 180);
    define("EXEC_TIME_NO_LIMIT", 0);
}
ini_set("max_execution_time", EXEC_TIME_NORMAL);

// mb settings
mb_language("Japanese");
//if( PHP_VERSION_ID < 50600 ){ // PHP 5.6.0 未満の場合
//  mb_internal_encoding("UTF-8");
//  mb_regex_encoding(mb_internal_encoding());
//}

// sessions settings
//  セッションファイルの保存場所
define("SESSION_SAVE_DIR", $_SERVER["DOCUMENT_ROOT"] . ".yrd/_session");
//  共通 session 設定関数
function soycms_common_session_settings($session_name_base = null, $cookie_path = null)
{
    // Site|Appのパス(==識別子)
    $app_path = "/";
    if (false !== $pos = strpos($_SERVER["SCRIPT_NAME"], "/", 1)) {
        $app_path = substr($_SERVER["SCRIPT_NAME"], 0, $pos) . "/";
    }
    // session.save_path
    $ss_save_path = SESSION_SAVE_DIR . $app_path;
    if (!file_exists($ss_save_path)) {
        mkdir($ss_save_path, F_MODE_DIR_HDN);
        $htf = $ss_save_path . F_HTACCESS;
        file_put_contents($htf, "order deny,allow\ndeny from all\n");
        chmod($htf, F_MODE_FILE);
    }
    if (is_writable($ss_save_path)) {
        session_save_path($ss_save_path);
    }
    // session.name
    if (null === $session_name_base) {
        // 指定がなければ Site|App単位でセッション名をわける
        $session_name_base = $app_path;
    }
    session_name(substr(md5("s" . $session_name_base . "i"), 0, 8) . "d"); // セッション名は無意味な文字列
    if (null === $cookie_path) {
        // 指定がなければ Site|App単位のPathを設定
        $cookie_path = $app_path;
    }
    // session.cookie : lifetime, path, domain, secure, httponly
    session_set_cookie_params(0, $cookie_path, "", COM_SECURE, true);
}
if (!RUN_CLI) {
    soycms_common_session_settings();
}

// header settings
//  header 送信直前処理の登録
header_register_callback(function () {
    // 不要なヘッダーの削除
    header_remove("X-Powered-By");
});
//  共通 header 送信関数
function soycms_common_header_output($admin = false)
{
    $isHtml = true; // Content-Typeの設定がない場合にはtext/htmlとみなす.
    $hs = headers_list();
    foreach ($hs as $h) {
        if (stripos($h, "Content-Type:") === 0) {
            if (stripos($h, "text/html;") === false) {
                $isHtml = false;
            }
            break;
        }
    }
    // text/htmlの場合のみ送信
    if ($isHtml) {
        header("Content-Language: ".SOYCMS_LANGUAGE); // SOYCMS_LANGUAGE= 管理側:SOYCMS_ADMIN_LANG, 公開側:SOYCMS_ADMIN_LANG|独自設定
        if ($admin) {
            // 管理側
            $target_uri = empty(SOYCMS_TARGET_URL) ? "" : " ".SOYCMS_TARGET_URL;
            header("Content-Security-Policy: default-src 'self'". $target_uri ."; img-src 'self' data:; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; form-action 'self'; frame-ancestors 'self'; base-uri 'self'" . $target_uri);
            // - img-src data: for elfinder
            // - 'unsafe-inline' 'unsafe-eval' を最終的には外す.

            header("Referrer-Policy: no-referrer, same-origin");
        } else {
            // 公開側
            header("Content-Security-Policy: frame-ancestors 'self';");
            header("Referrer-Policy: no-referrer, " . (COM_SECURE ? "strict-origin-when-cross-origin" : "origin-when-cross-origin"));
        }

        header("X-Frame-Options: SAMEORIGIN");  // CSP対応ブラウザでは frame-ancestors が優先.
        header("X-XSS-Protection: 1; mode=block");
    }
    // 全コンテンツに送信
    if (COM_SECURE) {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");  // for full https.
    }
//  header("X-Content-Type-Options: nosniff"); // Webサーバーで設定する. (設定できない場合は このコードを有効にする.)
}
// 公開側 header 送信関数
function soycms_site_header_output()
{
    soycms_common_header_output();
}
// 管理側 header 送信関数
function soycms_admin_header_output()
{
    soycms_common_header_output(true);
}
