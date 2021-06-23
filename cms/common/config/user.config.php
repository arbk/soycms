<?php
// SOY CMS ユーザ設定ファイル
//  設定ファイルの読込順: php.config.php -> (soy2_build.php) -> user.config.php -> html.config.php


// システム全体設定

// システム基本言語
//define("SOYSYS_BASE_LANG", "ja");
define("SOYSYS_BASE_LANG", "ja");


// CMS 設定 (主に管理側の設定)

// XML-RPCを許可する: true or false
//define("SOYCMS_ALLOW_XMLRPC", false);
define("SOYCMS_ALLOW_XMLRPC", false);

// ファイルマネージャーでアップロード可能な拡張子: ","(コンマ)区切りの文字列で指定
//  拡張子例:
//   css,htm,html,js,
//   csv,ini,log,txt,xml,
//   bmp,gif,ico,jpg,jpeg,png,tif,tiff,svg,svgz,
//   eot,otf,ttf,woff,woff2,
//   gz,lzh,tar,zip,
//   doc,docx,odt,ods,odp,pdf,ppt,pptx,xls,xlsx,
//   avi,mov,mp3,mpg,mpeg,ogg,swf,wav,wmv,
//   bin,cgi,exe,php,pl,sh,
//define("SOYCMS_ALLOWED_EXTENSIONS", "css,htm,html,js,csv,ini,txt,xml,gif,ico,jpg,jpeg,png,svg,svgz,eot,otf,ttf,woff,woff2,zip,pdf");
define("SOYCMS_ALLOWED_EXTENSIONS", "css,htm,html,js,csv,ini,txt,xml,gif,ico,jpg,jpeg,png,svg,svgz,eot,otf,ttf,woff,woff2,zip,pdf");

// テンプレートでPHPを許可する: true or false
//define("SOYCMS_ALLOW_PHP_SCRIPT", false);
define("SOYCMS_ALLOW_PHP_SCRIPT", false);

// PHPモジュールの使用を許可する: true or false
//define("SOYCMS_ALLOW_PHP_MODULE", false);
define("SOYCMS_ALLOW_PHP_MODULE", false);

// 使用するブロック：「,」区切りで設定. 管理画面で表示されるブロックの順番を入れ替えたり, 不要なブロックを削除できる.
//  ブロックのリスト:
//   EntryBlockComponent
//   LabeledBlockComponent
//   MultiLabelBlockComponent
//   SiteLabeledBlockComponent
//   PluginBlockComponent
//define("SOYCMS_BLOCK_LIST", "EntryBlockComponent,LabeledBlockComponent,MultiLabelBlockComponent,SiteLabeledBlockComponent,PluginBlockComponent");
define("SOYCMS_BLOCK_LIST", "EntryBlockComponent,LabeledBlockComponent,MultiLabelBlockComponent,SiteLabeledBlockComponent,PluginBlockComponent");

// サイトを作成するディレクトリ: 末尾は「/」. (例: /var/www/html/ , /home/users/example/web/)
//  通常はDOCUMENT_ROOTを設定する.
//define("SOYCMS_TARGET_DIRECTORY", $_SERVER["DOCUMENT_ROOT"]);
define("SOYCMS_TARGET_DIRECTORY", $_SERVER["DOCUMENT_ROOT"]);

// 公開側のURL: SOYCMS_TARGET_DIRECTORYを参照するURLをスキーム部分(http, https)から指定. 末尾は「/」. (例: http://example.jp/)
//  ""(空) を指定した場合は アクセスしているURLをもとに自動設定する.
//  通常は""(空) のままで良い. 公開側と管理側でホスト名が異なる場合などに設定する.
//define("SOYCMS_TARGET_URL", "");
define("SOYCMS_TARGET_URL", "");

// 管理側のドキュメントルート
//  管理側のドキュメントルートを動かす場合に指定する. 通常は""(空) のままで良い.
//define("SOYCMS_ADMIN_ROOT", "");
define("SOYCMS_ADMIN_ROOT", "");

// SITE管理画面の場所(ディレクトリ名)を指定する. 変更する場合は ディレクトリを作成し, index.php を配置すること.
//define("SOYCMS_ADMIN_URI", "soycms");
define("SOYCMS_ADMIN_URI", "soycms");

// 管理側 言語設定
//define("SOYCMS_ADMIN_LANG", SOYSYS_BASE_LANG);
define("SOYCMS_ADMIN_LANG", SOYSYS_BASE_LANG);

// 自動ログアウト: 秒数を指定
//  無操作時間が続いた場合にログアウトする秒数を指定する. 0を指定した場合はログアウトしない.
//  session.cookie_lifetime より大きい値を設定しても効果はない.
//define("SOYCMS_LOGIN_LIFETIME", 0);
define("SOYCMS_LOGIN_LIFETIME", 0);

// 最新ページ件数の初期値
//define("SOYCMS_INI_NUMOF_PAGE_RECENT", 3);
define("SOYCMS_INI_NUMOF_PAGE_RECENT", 3);

// 最近使ったラベル件数の初期値
//define("SOYCMS_INI_NUMOF_LABEL_RECENT", 4);
define("SOYCMS_INI_NUMOF_LABEL_RECENT", 4);

// 記事件数の初期値
//define("SOYCMS_INI_NUMOF_ENTRY", 20);
define("SOYCMS_INI_NUMOF_ENTRY", 20);
// 最新記事件数の初期値
//define("SOYCMS_INI_NUMOF_ENTRY_RECENT", 3);
define("SOYCMS_INI_NUMOF_ENTRY_RECENT", 3);

// コメント件数の初期値
//define("SOYCMS_INI_NUMOF_COMMENT", 20);
define("SOYCMS_INI_NUMOF_COMMENT", 20);
// 最新コメント件数の初期値
//define("SOYCMS_INI_NUMOF_COMMENT_RECENT", 3);
define("SOYCMS_INI_NUMOF_COMMENT_RECENT", 3);

// トラックバック件数の初期値
//define("SOYCMS_INI_NUMOF_TRACKBACK", 20);
define("SOYCMS_INI_NUMOF_TRACKBACK", 20);
// 最新トラックバック件数の初期値
//define("SOYCMS_INI_NUMOF_TRACKBACK_RECENT", 3);
define("SOYCMS_INI_NUMOF_TRACKBACK_RECENT", 3);

// CMS名称
//define("SOYCMS_CMS_NAME", "SoyCMS Trivial");
define("SOYCMS_CMS_NAME", "SoyCMS Trivial");
// CMS開発元名称
//define("SOYCMS_DEVELOPER_NAME", "arbk-works");
define("SOYCMS_DEVELOPER_NAME", "arbk-works");


// APP 設定

// SOY Appのデータを各サイトごとに持つかどうか: true or false. 通常は/common/db/配下のDBを使用する (false).
//  SOY Inquiry
//define("SOYINQUIRY_USE_SITE_DB", false);
define("SOYINQUIRY_USE_SITE_DB", false);
//  SOY Mail
//define("SOYMAIL_USE_SITE_DB", false);
define("SOYMAIL_USE_SITE_DB", false);


// SITE 設定
//  各サイト別に設定可能 (各サイトのindex.phpで定義する.)

// 言語設定
//  デフォルト値は SOYCMS_LANGUAGE = SOYCMS_ADMIN_LANG で固定. (サイト別の変更は可能.)
//defined("SOYCMS_LANGUAGE") || define("SOYCMS_LANGUAGE", SOYCMS_ADMIN_LANG);
defined("SOYCMS_LANGUAGE") || define("SOYCMS_LANGUAGE", SOYCMS_ADMIN_LANG);

// サイトのユーザーファイルディレクトリ: ユーザーファイルを設置するディレクトリ.
//  各サイト別に設定する場合, サイト作成時には指定できない ので サイト作成後に指定する. (ディレクトリもリネームすること.)
//defined("SOYCMS_USER_FILES_DIRNAME") || define("SOYCMS_USER_FILES_DIRNAME", "files");
defined("SOYCMS_USER_FILES_DIRNAME") || define("SOYCMS_USER_FILES_DIRNAME", "files");

//  キャッシュ機能を使うかどうか: true or false
//defined("SOYCMS_USE_CACHE") || define("SOYCMS_USE_CACHE", false);
defined("SOYCMS_USE_CACHE") || define("SOYCMS_USE_CACHE", false);
//  キャッシュの有効期間: 秒数を指定. 0を指定した場合はデータに変更がない限り永続的に有効となる.
//defined("SOYCMS_CACHE_LIFETIME") || define("SOYCMS_CACHE_LIFETIME", 86399); // 24時間 - 1秒
defined("SOYCMS_CACHE_LIFETIME") || define("SOYCMS_CACHE_LIFETIME", 86399);
//  キャッシュディレクトリ
//defined("SOYCMS_CACHE_DIRNAME") || define("SOYCMS_CACHE_DIRNAME", ".cache");
defined("SOYCMS_CACHE_DIRNAME") || define("SOYCMS_CACHE_DIRNAME", ".cache");

// ブログ テンプレートで使用する ラベル別表示制御 の個数. (1以上の値. 1未満を指定しても最低1つは作成される.)
//  visible_by_label, visible_by_lab1, ... visible_by_labN  (N = SOYCMS_INI_NUMOF_BLOG_DCM_LBL - 1)
//defined("SOYCMS_INI_NUMOF_BLOG_DCM_LBL") || define("SOYCMS_INI_NUMOF_BLOG_DCM_LBL", 1);
defined("SOYCMS_INI_NUMOF_BLOG_DCM_LBL") || define("SOYCMS_INI_NUMOF_BLOG_DCM_LBL", 1);

// ブログで sitemap feed を生成する: true or false
//defined("SOYCMS_SITEMAP_GEN") || define("SOYCMS_SITEMAP_GEN", false);
defined("SOYCMS_SITEMAP_GEN") || define("SOYCMS_SITEMAP_GEN", false);

// ブログ コメント
//  コメントを許可する: true or false
//defined("SOYCMS_ALLOW_BLOG_COMMENT") || define("SOYCMS_ALLOW_BLOG_COMMENT", false);
defined("SOYCMS_ALLOW_BLOG_COMMENT") || define("SOYCMS_ALLOW_BLOG_COMMENT", false);
//  コメントタイトル 最大長
//defined("SOYCMS_BLOG_COMMENT_TITLE_MAXLEN") || define("SOYCMS_BLOG_COMMENT_TITLE_MAXLEN", 50);
defined("SOYCMS_BLOG_COMMENT_TITLE_MAXLEN") || define("SOYCMS_BLOG_COMMENT_TITLE_MAXLEN", 50);
//  オーサー 最大長
//defined("SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN") || define("SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN", 20);
defined("SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN") || define("SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN", 20);
//  コメント本文 最大長
//defined("SOYCMS_BLOG_COMMENT_BODY_MAXLEN") || define("SOYCMS_BLOG_COMMENT_BODY_MAXLEN", 500);
defined("SOYCMS_BLOG_COMMENT_BODY_MAXLEN") || define("SOYCMS_BLOG_COMMENT_BODY_MAXLEN", 500);
//  最新コメント表示件数
//defined("SOYCMS_BLOG_COMMENT_NUMOF_RECENT") || define("SOYCMS_BLOG_COMMENT_NUMOF_RECENT", 3);
defined("SOYCMS_BLOG_COMMENT_NUMOF_RECENT") || define("SOYCMS_BLOG_COMMENT_NUMOF_RECENT", 3);

// ブログ トラックバック
//  トラックバックを許可する: true or false
//defined("SOYCMS_ALLOW_BLOG_TRACKBACK") || define("SOYCMS_ALLOW_BLOG_TRACKBACK", false);
defined("SOYCMS_ALLOW_BLOG_TRACKBACK") || define("SOYCMS_ALLOW_BLOG_TRACKBACK", false);
//  最新トラックバック表示件数
//defined("SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT") || define("SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT", 3);
defined("SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT") || define("SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT", 3);
