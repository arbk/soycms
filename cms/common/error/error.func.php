<?php
function get_report($e)
{
    $cmsName = CMSUtil::getCMSName();

    $str = array();

    $str[] = 'DETECT DATE: '.date('c');
    $str[] = '';
    $str[] = get_exception_report($e);
    $str[] = '';
    $str[] = get_soycms_report();
    $str[] = '';
    $str[] = '';
    $str[] = 'STACK TRACE';
    $str[] = get_exception_trace($e);
    $str[] = '';
    $str[] = 'Server Environment';
    $str[] = get_environment_report();
//  if($cmsName == "SOY CMS"){
    $str[] = '';
    $str[] = $cmsName . ' Options';
    $str[] = get_soycms_options();
//  }

    return implode("\n", $str);
}

function get_soycms_report()
{
    $cmsName = CMSUtil::getCMSName();

    $str = array();

    $str[] = $cmsName . ' Version:           '.SOYCMS_VERSION.' (based on '.SOYCMS_BASE_VERSION.')';
    $str[] = $cmsName . ' Build Date:        '.SOYCMS_BUILD;
    $str[] = $cmsName . ' DB Type:           '.SOYCMS_DB_TYPE;
//  if($cmsName == "SOY CMS"){
    $str[] = 'SOY2RootDir:               '.SOY2::RootDir();
    $str[] = 'SOY2_DOCUMENT_ROOT:        '.( defined("SOY2_DOCUMENT_ROOT") ? SOY2_DOCUMENT_ROOT : "(undefined)" );
//  }

    return implode("\n", $str);
}

function get_soycms_options()
{
//  $cmsName = CMSUtil::getCMSName();

//  if($cmsName == "SOY CMS"){
    $str = array();

    $str[] = 'SOYSYS_BASE_LANG:          '.SOYSYS_BASE_LANG;
    $str[] = '';
    $str[] = 'SOYCMS_ALLOW_XMLRPC:       '.SOYCMS_ALLOW_XMLRPC;
    $str[] = 'SOYCMS_ALLOWED_EXTENSIONS: '.SOYCMS_ALLOWED_EXTENSIONS;
    $str[] = 'SOYCMS_ALLOW_PHP_SCRIPT:   '.SOYCMS_ALLOW_PHP_SCRIPT;
    $str[] = 'SOYCMS_ALLOW_PHP_MODULE:   '.SOYCMS_ALLOW_PHP_MODULE;
    $str[] = 'SOYCMS_BLOCK_LIST:         '.strtr(SOYCMS_BLOCK_LIST, array("," => "\n                           "));
    $str[] = 'SOYCMS_TARGET_DIRECTORY:   '.SOYCMS_TARGET_DIRECTORY;
    $str[] = 'SOYCMS_TARGET_URL:         '.SOYCMS_TARGET_URL;
    $str[] = 'SOYCMS_ADMIN_ROOT:         '.SOYCMS_ADMIN_ROOT;
    $str[] = 'SOYCMS_ADMIN_LANG:         '.SOYCMS_ADMIN_LANG;
    $str[] = 'SOYCMS_LOGIN_LIFETIME:     '.SOYCMS_LOGIN_LIFETIME;
    $str[] = '';
    $str[] = 'SOYCMS_INI_NUMOF_PAGE_RECENT:      '.SOYCMS_INI_NUMOF_PAGE_RECENT;
    $str[] = 'SOYCMS_INI_NUMOF_LABEL_RECENT:     '.SOYCMS_INI_NUMOF_LABEL_RECENT;
    $str[] = 'SOYCMS_INI_NUMOF_ENTRY:            '.SOYCMS_INI_NUMOF_ENTRY;
    $str[] = 'SOYCMS_INI_NUMOF_ENTRY_RECENT:     '.SOYCMS_INI_NUMOF_ENTRY_RECENT;
    $str[] = 'SOYCMS_INI_NUMOF_COMMENT:          '.SOYCMS_INI_NUMOF_COMMENT;
    $str[] = 'SOYCMS_INI_NUMOF_COMMENT_RECENT:   '.SOYCMS_INI_NUMOF_COMMENT_RECENT;
    $str[] = 'SOYCMS_INI_NUMOF_TRACKBACK:        '.SOYCMS_INI_NUMOF_TRACKBACK;
    $str[] = 'SOYCMS_INI_NUMOF_TRACKBACK_RECENT: '.SOYCMS_INI_NUMOF_TRACKBACK_RECENT;
    $str[] = '';
    $str[] = 'SOYCMS_CMS_NAME:           '.SOYCMS_CMS_NAME;
    $str[] = 'SOYCMS_DEVELOPER_NAME:     '.SOYCMS_DEVELOPER_NAME;
    $str[] = '';
    $str[] = 'SOYINQUIRY_USE_SITE_DB:    '.SOYINQUIRY_USE_SITE_DB;
    $str[] = 'SOYMAIL_USE_SITE_DB:       '.SOYMAIL_USE_SITE_DB;
    $str[] = '';
    $str[] = 'SOYCMS_LANGUAGE:           '.SOYCMS_LANGUAGE;
    $str[] = 'SOYCMS_USER_FILES_DIRNAME: '.SOYCMS_USER_FILES_DIRNAME;
    $str[] = 'SOYCMS_USE_CACHE:          '.SOYCMS_USE_CACHE;
    $str[] = 'SOYCMS_CACHE_LIFETIME:     '.SOYCMS_CACHE_LIFETIME;
    $str[] = 'SOYCMS_CACHE_DIRNAME:      '.SOYCMS_CACHE_DIRNAME;
    $str[] = 'SOYCMS_SITEMAP_GEN:        '.SOYCMS_SITEMAP_GEN;
    $str[] = '';
    $str[] = 'SOYCMS_ALLOW_BLOG_COMMENT:         '.SOYCMS_ALLOW_BLOG_COMMENT;
    $str[] = 'SOYCMS_BLOG_COMMENT_TITLE_MAXLEN:  '.SOYCMS_BLOG_COMMENT_TITLE_MAXLEN;
    $str[] = 'SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN: '.SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN;
    $str[] = 'SOYCMS_BLOG_COMMENT_BODY_MAXLEN:   '.SOYCMS_BLOG_COMMENT_BODY_MAXLEN;
    $str[] = 'SOYCMS_BLOG_COMMENT_NUMOF_RECENT:  '.SOYCMS_BLOG_COMMENT_NUMOF_RECENT;
    $str[] = '';
    $str[] = 'SOYCMS_ALLOW_BLOG_TRACKBACK:        '.SOYCMS_ALLOW_BLOG_TRACKBACK;
    $str[] = 'SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT: '.SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT;

    return implode("\n", $str);
//  }
//  return "";
}

function get_exception_report($e)
{
    $str = array();

    $document_root = $_SERVER["DOCUMENT_ROOT"];
    $file = str_replace("\\", "/", $e->getFile());
    $file = str_replace($document_root, "", $file);

    $str[] = 'MESSAGE: '.get_exception_message($e);
    $str[] = 'EXCEPTION TYPE: '.get_class($e);
    $str[] = 'LOCATION: '.$file." (".$e->getLine().")";

    return implode("\n", $str);
}

function get_exception_trace($e)
{
    $str = array();

    $trace = $e->getTrace();
    $traceCnt = min(5, count($trace));
    for ($i = 0; $i < $traceCnt; ++$i) {
        $str[] = get_trace_report($trace[$i], $i);
    }

    return implode("\n", $str);
}

function get_environment_report()
{
    $str = array();

    $str[] = 'PHP Version:          '.phpversion();
    $str[] = '';
    $str[] = 'PHP SAPI NAME:        '.php_sapi_name();
    $str[] = 'PHP SAFE MODE:        '.(ini_get("safe_mode")? "Yes" : "No");
//  $str[] = 'MAGIC_QUOTE_GPC:      '.( get_magic_quotes_gpc() ? "Yes" : "No" );
    $str[] = 'SHORT_OPEN_TAG:       '.( ini_get("short_open_tag") ? "Yes" : "No" );
    $str[] = '';
    $str[] = 'MEMORY_LIMIT:         '.ini_get("memory_limit")." Bytes";
    if (function_exists("memory_get_usage")) {
        $str[] = 'Memory Usage:         '.number_format(memory_get_usage())." Bytes";
        $str[] = '                      '.number_format(memory_get_usage(true))." Bytes (Real)";
    }
    if (function_exists("memory_get_peak_usage")) {
        $str[] = '                      '.number_format(memory_get_peak_usage())." Bytes (Peak)";
        $str[] = '                      '.number_format(memory_get_peak_usage(true))." Bytes (Peak, Real)";
    }
    $str[] = '';
    $str[] = 'MAX_EXECUTION_TIME:   '.ini_get("max_execution_time") ." sec.";
    $str[] = 'POST_MAX_SIZE:        '.ini_get("post_max_size")." Bytes";
    $str[] = 'UPLOAD_MAX_FILESIZE:  '.ini_get("upload_max_filesize")." Bytes";
    $str[] = '';
    $str[] = 'mb_string:            '.( extension_loaded("mbstring") ? "Yes" : "No" );
    $str[] = 'PDO:                  '.( extension_loaded("PDO") ? "Yes" : "No" );
    $str[] = 'PDO_SQLite:           '.( extension_loaded("PDO_SQLITE") ? "Yes" : "No" );
    $str[] = 'PDO_MySQL:            '.( extension_loaded("PDO_MySQL") ? "Yes" : "No" );
    $str[] = 'Standard PHP Library: '.( extension_loaded("SPL") ? "Yes" : "No" );
    $str[] = 'SimpleXML:            '.( extension_loaded("SimpleXML") ? "Yes" : "No" );
    $str[] = 'JSON:                 '.( extension_loaded("json") ? "Yes" : "No" );
    $str[] = 'Services_JSON:        '.( class_exists("Services_JSON") ? "Yes" : "No" );
    $str[] = 'ZIP:                  '.( extension_loaded("zip") ? "Yes" : "No" );
    $str[] = 'ZipArchive:           '.( class_exists("ZipArchive") ? "Yes" : "No" );
    $str[] = 'Archive_Zip:          '.( class_exists("Archive_Zip") ? "Yes" : "No" );
    $str[] = 'OpenSSL:              '.( extension_loaded("openssl") ? "Yes" : "No" );
    $str[] = 'HASH:                 '.( extension_loaded("hash") ? "Yes" : "No" );
    $str[] = 'GD:                   '.( extension_loaded("GD") ? "Yes" : "No" );
    $str[] = '';
    $str[] = 'Module/CGI            '.( (stripos(php_sapi_name(), "cgi")!==false) ? "CGI" : "Module");
    $str[] = 'Rewrite               '.( function_exists("apache_get_modules") ? ( in_array("mod_rewrite", apache_get_modules()) ? "OK" : "NG") : "Unknown");
    $str[] = '';
    $str[] = 'USER_AGENT:           '.@$_SERVER["HTTP_USER_AGENT"];
    $str[] = 'REQUEST_URI:          '.@$_SERVER["REQUEST_URI"];
    $str[] = 'SCRIPT_NAME:          '.@$_SERVER["SCRIPT_NAME"];
    $str[] = 'PATH_INFO:            '.@$_SERVER["PATH_INFO"];
    $str[] = 'QUERY_STRING:         '.@$_SERVER["QUERY_STRING"];
    $str[] = '';
    $str[] = 'DOCUMENT_ROOT:        '.@$_SERVER["DOCUMENT_ROOT"];
    $str[] = 'SCRIPT_FILENAME:      '.@$_SERVER["SCRIPT_FILENAME"];
    $str[] = '';
    $str[] = 'PHP_SAPI:             '. PHP_SAPI;
    $str[] = 'STDIN:                '. (defined("STDIN") ? STDIN : "(undefined)");

    return implode("\n", $str);
}

function get_exception_message($e)
{
    if (($e instanceof SOY2DAOException || $e instanceof PDOException) && method_exists("getPDOExceptionMessage", $e)) {
        return $e->getMessage()." (".$e->getPDOExceptionMessage().")";
    } else {
        return $e->getMessage();
    }
}

function get_trace_report($trace, $index)
{

    $document_root = $_SERVER["DOCUMENT_ROOT"];
    @$file = str_replace("\\", "/", $trace["file"]);
    $file = str_replace($document_root, "", $file);

    $str = array();
    $str[] = '-----------------------';
    @$str[] = $index. ":".$trace["class"].$trace["type"].$trace["function"];
    if (isset($trace["args"]) && is_array($trace["args"]) && count($trace["args"])) {
        $traceCnt = count($trace["args"]);
        for ($i = 0; $i<$traceCnt; ++$i) {
            $str[] = "\t".'argument['.$i.']: '.get_argument_string($trace["args"][$i]);
        }
    }

    $str[] = '';
    @$str[] = "\t".''.$file."(".$trace["line"].")";

    return implode("\n", $str);
}

function get_argument_string($arg)
{
    if (is_string($arg)) {
        return 'String("'.$arg.'")';
    } elseif (is_int($arg)) {
        return $arg;
    } elseif (is_bool($arg)) {
        return ($arg)? "true" : "false";
    } elseif (null===$arg) {
        return "null";
    } elseif (is_resource($arg)) {
        return "resource";
    } elseif (is_object($arg)) {
        if (method_exists($arg, "__toString")) {
            return get_class($arg)." [\"".(string)$arg."\"]";
        } else {
            return get_class($arg)." [".preg_replace("/\\n  /xms", "\n\t", var_export($arg, true))."]";
        }
    } elseif (is_array($arg)) {
        return preg_replace("/\\n  /xms", "\n\t", var_export($arg, true));
    } else {
        return "unknown type argument";
    }
}

/**
 * エラーの解決方法を出力する
 * @return text/html 必要に応じてエスケープされたHTML
 */
function get_resolve_message($e)
{
    if (method_exists($e, "getResolve")) {
        return soy2_h($e->getResolve());
    }

    if ($e instanceof SOY2HTMLException) {
        if (!is_writable(SOY2HTMLConfig::CacheDir())) {
            return
            'SOY2HTMLはキャッシュファイルの生成に失敗しました。<br>'.
            '現在のキャッシュディレクトリは<br><span style="margin-left:10px">'.soy2_h(str_replace("\\", "/", SOY2HTMLConfig::CacheDir())).'</span><br>となっています。<br>'.
            'キャッシュディレクトリが存在するか、また書き込み権限があるかなどを確認してください。';
        }
    } elseif ($e instanceof SOY2DAOException) {
        return
        'データベースへのアクセス中にエラーが発生しました。。<br>'.
        '<ul style="margin-left:50px;font-size:small;list-style-type:circle">'.
        '<li>SOY CMSのアップデートでデータベースの仕様が変更された可能性があります。公式ページ (http://www.soycms.net/) をご確認ください。</li>'.
        '<li>データベースへのアクセス権限が無い可能性があります。アクセス権限を確認してください。</li>'.
        '</ul>';
    } elseif ($e instanceof PDOException) {
        return
        'データベースへのアクセス中にエラーが発生しました。<br>'.
        'SOY CMSのアップデートを行った直後にこのエラーが発生した場合は、データベースの仕様変更があった可能性があります。'.
        '公式ページ (http://www.soycms.net/) にてご確認ください。';
    }

    return '開発元にご連絡ください。';
}
