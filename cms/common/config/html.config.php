<?php
// HTML 設定ファイル
//  設定ファイルの読込順: php.config.php -> (soy2_build.php) -> user.config.php -> html.config.php


// 管理側 HTML 設定

//  管理側 HTML言語設定
//define("SOYCMS_ADMIN_HTML_LANG", SOYCMS_ADMIN_LANG);
define("SOYCMS_ADMIN_HTML_LANG", SOYCMS_ADMIN_LANG);

//  管理側 共通 html head 出力関数
function soycms_admin_html_head_output()
{
    echo '
<meta charset="' . SOY2::CHARSET . '">
<meta name="robots" content="noindex,nofollow,noarchive">
<meta name="referrer" content="no-referrer, same-origin">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
';
}
