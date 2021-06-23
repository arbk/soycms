<?php

class CMSUtil
{
    const DATE_MIN = 0;
    const DATE_MAX = 2147483647;

    const MODE_ADMIN = 0;  //adminの管理画面を見ているか？
    const MODE_SOYCMS = 1; //サイト毎の管理画面を見ているか？

  /**
   * Convert Unix time stamp to CMS time format
   * @param boolean true:startDate false:endDate
   */
    public static function encodeDate($date, $startFlag = true)
    {
        if ((null===$date)) {
            if ($startFlag) {
                //min date
                return self::DATE_MIN;
            } else {
              //Max date
                return self::DATE_MAX;
            }
        } else {
            return $date;
        }
    }

  /**
   * Convert CMS time format to UNIX time stamp
   */
    public static function decodeDate($date)
    {
        if ($date == self::DATE_MIN || $date == self::DATE_MAX) {
            return null;
        } else {
            return $date;
        }
    }

  /**
   * サイトURLをサイト側のデータベースから取得する。取得できなければUserInfoUtilの公開URLを調べる
   */
    public static function getSiteUrl()
    {
        static $siteUrlBySiteUrl;
        if ((null===$siteUrlBySiteUrl)) {
            $siteConfigDao = SOY2DAOFactory::create("cms.SiteConfigDAO");
            try {
                $siteConfig = $siteConfigDao->get();
            } catch (Exception $e) {
                $siteConfig = new SiteConfig();
            }

            //SiteConfigに入っているURLを取得する
            $url = $siteConfig->getConfigValue("url");
            if (is_bool($url) && !$url) {
                $url = null;  //falseで返ってくることがあった。
            }
            if (isset($url) && is_string($url) && strlen($url) > 0) {
                $siteUrlBySiteUrl = $url;
              //SiteConfigにURLが入っていなかった場合はUserInfoUtilから公開URLを取得する
            } else {
                $siteUrlBySiteUrl = UserInfoUtil::getSitePublishURL();
            }
        }

        return $siteUrlBySiteUrl;
    }

    public static function getEntryHiddenInputHTML($entryId, $title)
    {
        $str = CMSMessageManager::get("SOYCMS_PREVIEW_EDIT_BUTTON");
        $str = str_replace("%TITLE%", "[".$title."]", $str);
        return "<button type=\"button\" class=\"cms_hidden_entry_id\" entryid=\"$entryId\" style=\"display:none;\">".$str."</button>";
    }

    public static function getEntryAddHiddenInputHTML($labelId)
    {
        $str = CMSMessageManager::get("SOYCMS_PREVIEW_ADD_BUTTON");
        return "<button type=\"button\" class=\"cms_hidden_entry_id\" labelid=\"$labelId\" style=\"display:none;\">".$str."</button>";
    }

  /**
   * notifyUpdate
   */
    public static function notifyUpdate()
    {
        static $dao;
        if (!$dao) {
            $dao = SOY2DAOFactory::create("cms.SiteConfigDAO");
        }
        return $dao->notifyUpdate();
    }

  /**
   * ディレクトリ内のファイルリストを取得
   * $dirを正規化（更新）するので $dirは参照渡しの必要がある.
   * @param string $dir
   */
    public static function getFileListInDir(&$dir)
    {
        if (1 > strlen($dir)) {
            return null;
        }
        if ($dir[strlen($dir) - 1] != "/") {
            $dir .= "/";
        }
        if (!is_dir($dir)) {
            return null;
        }
        return array_diff(scandir($dir), array('..', '.'));
    }

  /**
   * ディレクトリ内のファイルを全削除（キャッシュ削除用）
   * - ファイル名がドットからはじまるもの（.xxx）は削除しない.
   */
    public static function unlinkAllIn($dir, $recursive = false, $rmdir = false)
    {
        $file_list = self::getFileListInDir($dir);
        if ((null===$file_list)) {
            return;
        }

        foreach ($file_list as $filename) {
            if (1 > strlen($filename)) {
                continue;
            }
            $filePath = $dir . $filename;
            if (is_file($filePath)) {
                if ($filename[0] === ".") {
                    continue;
                }
                unlink($filePath);
            } elseif (is_dir($filePath) && $recursive) {
                self::unlinkAllIn($filePath, $recursive, $rmdir);

                if ($filename[0] === ".") {
                    continue;
                }
                if ($rmdir) {
                    rmdir($filePath);
                }
            } else {
                continue;
            }
        }
    }

  /**
   * ディレクトリ内の全ファイルのモードを変更
   */
    public static function chmodAllIn($dir, $filemode, $dirmode, $recursive = false, $filterFile = null, $filterDir = null)
    {
        $file_list = self::getFileListInDir($dir);
        if ((null===$file_list)) {
            return;
        }

        foreach ($file_list as $filename) {
            $filePath = $dir . $filename;
            if (is_file($filePath)) {
                if (null!==$filterFile && 1 !== preg_match($filterFile, $filename)) {
                    continue;
                }
                (null!==$filemode) && chmod($filePath, $filemode);
            } elseif (is_dir($filePath)) {
                if ($recursive) {
                    self::chmodAllIn($filePath, $filemode, $dirmode, $recursive, $filterFile, $filterDir);
                }

                if (null!==$filterDir && 1 !== preg_match($filterDir, $filename)) {
                    continue;
                }
                (null!==$dirmode) && chmod($filePath, $dirmode);
            } else {
                continue;
            }
        }
    }

  /**
   * ユーザーファイルディレクトリのパスを取得
   * @param string $site_dir
   * @return string
   */
    public static function getUserFilesDir($site_dir)
    {
        return $site_dir.SOYCMS_USER_FILES_DIRNAME."/";
    }

  /**
   * ユーザーファイルディレクトリ内の全ファイルのモードを変更
   * @param string $site_dir
   * @param boolean $all
   */
    public static function chmodAllInUserFilesDir($site_dir, $all = false)
    {
        $userFilesDir = self::getUserFilesDir($site_dir);
        if (!is_dir($userFilesDir)) {
            return;
        }

      // ユーザーファイルディレクトリ
        chmod($userFilesDir, F_MODE_DIR_USR);

      // ユーザーファイルディレクトリ配下
      //  ファイル    : .（ドット）で始まらず, .php で終わらないもの
      //  ディレクトリ: .（ドット）で始まらないもの
        CMSUtil::chmodAllIn($userFilesDir, F_MODE_FILE_USR, F_MODE_DIR_USR, true, "/^(?!\.)(?!.*\.php$).*$/i", "/^(?!\.).*$/i");
        if ($all) {
        //  ファイル    : .（ドット）で始まるもの
        //  ディレクトリ: .（ドット）で始まるもの
            CMSUtil::chmodAllIn($userFilesDir, F_MODE_FILE, F_MODE_DIR_HDN, true, "/^\..+$/i", "/^\..+$/i");
        //  ファイル    : .php で終わるもの
            CMSUtil::chmodAllIn($userFilesDir, F_MODE_FILE_DISABLE, null, true, "/^.+\.php$/i");
        }
    }

  /**
   * 指定した文字数で文字列を丸める
   * @param string $str 丸めたい文字列
   * @param int $length 丸める文字数
   */
    public static function strimlength($str, $length)
    {
        return (mb_strlen($str) <= $length) ? $str : rtrim(mb_substr($str, 0, $length, SOY2::CHARSET)) . "...";
    }

  /**
   * 指定した幅で文字列を丸める
   * @param string $str 丸めたい文字列
   * @param int $width 丸める幅
   */
    public static function strimwidth($str, $width)
    {
        return mb_strimwidth($str, 0, $width + 3, "...", SOY2::CHARSET);
    }

  /**
   * エントリーやラベルのエイリアスでURLに使われると困る文字列を除去する
   * ?#/%\&+@;:$,=
   * 2009-04-24 半角スペース, ", ' を追加
   * 2009-06-11 半角スペースは_に変換する（SEOや読みやすさのために）
   * 2010-02-19 RFC2396のreservedのうち +  @ ; : $ , = を追加し、すべて _ に変換することにした。
   *            残りのreservedのうち / ? & は既存。なお @ ; : $ , = はアクセス可能。
   * 2011-07-07 リンクを張る際に不都合が出やすいので<>も追加
   * 2016-01-30 半角英数以外の文字間の（最終的には _ に変換される）半角スペースを削除
   * 2016-08-22 アクセス制限ではじかれる可能性のある拡張子・末尾記号を削除
   * 2016-08-26 アルファベトを小文字に統一（大文字→小文字変換処理追加）
   */
    public static function sanitizeAlias($alias)
    {
        $alias = str_replace(array("?","#","/","%","\\", "&", "'", '"', "+", "@", ";", ":", '$', ",", "=", "<", ">"), " ", $alias);
        $alias = trim($alias);
        $alias = preg_replace("/([^0-9a-zA-Z])( +)([^0-9a-zA-Z])( *)/", "$1$3", $alias);
        $alias = preg_replace("/ +/", "_", $alias);
        $alias = mb_strtolower($alias);
        $alias = preg_replace("/(^#.*#|\.(bak|conf|dist|dll|fla|in[ci]|log|psd|sh|sql|sw[op])|~)$/", "", $alias);
        return $alias;
    }

  /**
   * メールアドレスの形式チェックを行う
   * @param string $email
   * @return boolean 正しければtrue
   */
    public static function validEmail($email)
    {
        return SOY2Mail_MailAddress::validation($email);
    }

  /**
   * URLの形式チェックを行う
   * @param string $url
   * @return boolean 正しければtrue
   */
    public static function validUrl($url)
    {
        return ( !empty($url) && false !== filter_var($url, FILTER_VALIDATE_URL) );
    }

  /**
   * 日付文字列をUnixタイムスタンプに変換する
   * @param string $time
   * @return number
   */
    public static function strtotime($time)
    {
        return strtotime(trim(str_replace(
            array("年","月","日","時","分","秒", "  "),
            array( "-", "-", " ", ":", ":",  "", " "),
            $time
        )));
    }

  /**
   * mkdir して chmod する
   * mkdir(dir, mode) がうまく動かないサーバーがある
   * @return boolean
   */
    public static function makeDir($dir, $mode = F_MODE_DIR)
    {
        $ret = false;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        if (is_dir($dir)) {
            $ret = chmod($dir, $mode);
        }
        return $ret;
    }

  /**
   * .htaccess ファイルを作成する
   * @param string $dir ファイルを作成するディレクトリへのパス
   * @param string $cnt ファイルの内容
   * @return boolean
   */
    public static function makeHtaccess($dir, $cnt = "order deny,allow\ndeny from all\n")
    {
        $ret = false;
        if (is_dir($dir) && is_writable($dir)) {
            $htf = rtrim($dir, "/") . "/" . F_HTACCESS;
            $ret = file_put_contents($htf, $cnt);
            if (false !== $ret) {
                $ret = chmod($htf, F_MODE_FILE);
            }
        }
        return $ret;
    }

  /**
   * ファイルのバックアップを作成
   * @return boolean
   */
    public static function createBackup($file)
    {
        $file = realpath($file);
        $dir = dirname($file);

        if (file_exists($file) && is_writable($dir)) {
            $backup = $backup_filename_base = "{$file}.old";

            $i = 1;
            while (file_exists($backup) && $i<100) {
                $backup = sprintf("{$backup_filename_base}.%02d", $i);
                $i++;
            }

            return copy($file, $backup);
        }

        return false;
    }

  /**
   * バックアップファイルのリスト
   */
    public static function getBackupList($original)
    {
        $list = array();

        $backup_filename_base = "{$original}.old";

        if (file_exists($backup_filename_base)) {
            $list[] = $backup_filename_base;
        }

        for ($i=1; $i<100; ++$i) {
            $backup = sprintf("{$backup_filename_base}.%02d", $i);
            if (file_exists($backup)) {
                $list[] = $backup;
            }
        }

        return $list;
    }

  /**
   * 12時間以内なら 時:分 を、半年以内なら 月/日 を、他は 年-月-日 を返す
   */
    public static function getRecentDateTimeText($unixtime)
    {
        if (!is_numeric($unixtime)) {
            $unixtime = 0;
        }
        $diff = abs(SOYCMS_NOW - $unixtime);
        switch (true) {
            case $diff < 12*60*60:
                return date("H:i", $unixtime);
            case $diff < 180*24*60*60:
                return date("n/j", $unixtime);
            default:
                return date("Y-n-j", $unixtime);
        }
    }

  /**
   * 公開期間設定の文字列を返す（多言語対応）
   * TODO とりあえずここに書くけどもっとふさわしい場所があるはず。CMSMessageManagerとか。
   */
    public static function getOpenPeriodMessage($start, $end)
    {
        if ((null!==$start) and (null!==$end)) {
            $text = CMSMessageManager::get("SOYCMS_PUBLISH_FROM_TO", array(
            "FROM" => date("Y-m-d H:i:s", $start),
            "TO"   => date("Y-m-d H:i:s", $end)
            ));
        } elseif ((null!==$start)) {
            $text = CMSMessageManager::get("SOYCMS_PUBLISH_FROM", array(
            "FROM" => date("Y-m-d H:i:s", $start)
            ));
        } elseif ((null!==$end)) {
            $text = CMSMessageManager::get("SOYCMS_PUBLISH_TO", array(
            "TO" => date("Y-m-d H:i:s", $end)
            ));
        } else {
            $text = CMSMessageManager::get("SOYCMS_NO_SETTING");
        }
        return $text;
    }

//   /** ロゴ画像 **/
//     public static function getLogoFile($mode = self::MODE_ADMIN)
//     {
//         switch ($mode) {
//             case self::MODE_SOYCMS:
//                 $logoDir = dirname(SOY2::RootDir()) . "/soycms/image/logo/";
//                 break;
//             case self::MODE_ADMIN:
//             default:
//                 $logoDir = dirname(SOY2::RootDir()) . "/admin/image/logo/";
//                 break;
//         }
//
//         if (file_exists($logoDir) && is_dir($logoDir)) {
//             foreach (glob($logoDir . "*") as $f) {
//                 if (is_file($f) && !strpos($f, ".txt")) {
//                     $fileName = trim(substr($f, strrpos($f, "/") + 1), "/");
//                     if (preg_match('/\.(jpg|jpeg|gif|png|bmp)/', $fileName, $tmp)) {
//                         return SOY2PageController::createRelativeLink("image/logo/" . $fileName);
//                     }
//                 }
//             }
//         }
//         return SOY2PageController::createRelativeLink("css/img/logo_big.gif");
//     }

    public static function getCMSName()
    {
        return SOYCMS_CMS_NAME;
    }

    public static function getDeveloperName()
    {
        return SOYCMS_DEVELOPER_NAME;
    }

  /**
   * エントリーリストの出力項目を判定します.
   * 出力項目リストが空（未指定）の場合, 全ての項目を出力対象と判定します.
   * @param string $items 出力項目リスト（,区切り文字列）
   * @param string $item 項目名（cms:id）
   * @return boolean
   */
    public static function isEntryListItems($items, $item)
    {
        if (empty($items)) {
            return true;
        }
        $itemArray=explode(",", $items);
        return in_array($item, $itemArray, true);
    }

  /**
   * 簡易的なユーザー識別トークンを取得します.
   * Sessionを利用せずに 簡易的に ユーザーを識別するために利用します.
   *
   * @param string $identifier トークン発行対象（Webページ等）を識別するためのデータ. [未指定:無し（識別しない）]
   * @param string $expire 有効期限. [未指定:無期限, "y":当年, "m":当月, "d":当日]
   * @param string $user ユーザーを識別するためのデータ. [未指定:IPアドレス]
   * @return トークン文字列
   */
    public static function getEasyUserToken($identifier = "", $expire = "", $user = null)
    {
        switch ($expire) {
            case "y":
                $expire = date("Y");
                break;
            case "m":
                $expire = date("Ym");
                break;
            case "d":
                $expire = date("Ymd");
                break;
            default:
                $expire = "";
                break;
        }
        if ((null===$user)) {
            $user = $_SERVER["REMOTE_ADDR"];
        }

        $data = SOYCMS_VERSION . $user .
            SOYCMS_BUILD . $expire .
            SOYCMS_BUILD_TIME . $identifier .
            SOYCMS_REVISION . $_SERVER["DOCUMENT_ROOT"];
            // $user, $expire, $identifier 以外の値は salt的に利用.
            // (設置環境に応じて変わる値ならば何でもよい.)

        return soy2_hash($data);
    }

  /**
   * 簡易的な識別トークンを取得します.
   * Sessionを利用せずに 簡易的に トークン発行対象を識別するために利用します.
   *
   * @param string $identifier トークン発行対象（Webページ等）を識別するためのデータ.
   */
    public static function getEasyToken($identifier)
    {
        return self::getEasyUserToken($identifier, null, "");
    }

  /**
   * 翻訳ファイルを設定する
   */
    public static function Text($lang = null)
    {
        static $_lang;

        if ($lang) {
            $_lang = $lang;
        }

        return $_lang;
    }

  /**
   * 翻訳を行う
   * ソースコードはjaで書かれていることを基本にする
   */
    public static function getText($text)
    {
        $soycms_language = self::Text();

        if (isset($soycms_language[$text])) {
            return $soycms_language[$text];
        }

        return $text;
    }

  /**
   * バイト数をGBやMBなどの文字列に変換する
   */
    public static function GetHumanReadableSize($byte)
    {
        $byte *= 10;
        if ($byte >= 1073741824) {//1024*1024*1024 = 1073741824
            $valueX10 = floor($byte / 1073741824);
            $unit = "GB";
        } elseif ($byte >= 1048576) {//1024*1024 = 1048576
            $valueX10 = floor($byte / 1048576);
            $unit = "MB";
        } elseif ($byte >= 1024) {
            $valueX10 = floor($byte / 1024);
            $unit = "KB";
        } else {
            $valueX10 = floor($byte);
            $unit = "B";
        }
        return ($valueX10/10).$unit;
    }

  /**
   * ◯GBや◯MBなどの文字列をバイト数に変換する
   */
    public function GetNumricByte($val)
    {
        $val = trim($val);
      //末尾のBを削除
        if (strlen($val) && strtoupper($val[strlen($val)-1]) == "B") {
            $val = substr($val, 0, strlen($val)-1);
        }
        $last = strtoupper($val[strlen($val)-1]);
        switch ($last) {
            case 'G':
                $val *= 1024;
                // no break
            case 'M':
                $val *= 1024;
                // no break
            case 'K':
                $val *= 1024;
        }
        return $val;
    }

//   /**
//    * mod_rewriteが使えるかどうか
//    * 2010-02-19 ServerInfoUtil::isEnableModRewriteに移動（メソッドは元からあった）
//    * @return boolean
//    */
//     public static function checkEnableModRewrite()
//     {
//         SOY2::import("util.ServerInfoUtil");
//         return ServerInfoUtil::isEnableModRewrite();
//     }

  /**
   * Zipが利用可能かどうか判断
   * 2010-02-19 ServerInfoUtilに移動
   * @return クラス名
   */
    public static function checkZipEnable($expandOnly = false)
    {
        SOY2::import("util.ServerInfoUtil");
        return ServerInfoUtil::checkZipEnable($expandOnly);
    }

  /**
   * DSNをadminに切り替える
   */
    public static function switchDsn()
    {
        $old["dsn"] = SOY2DAOConfig::Dsn();
        $old["user"] = SOY2DAOConfig::User();
        $old["pass"] = SOY2DAOConfig::Pass();

        SOY2DAOConfig::Dsn(ADMIN_DB_DSN);
        SOY2DAOConfig::User(ADMIN_DB_USER);
        SOY2DAOConfig::Pass(ADMIN_DB_PASS);

        return $old;
    }

    public static function resetDsn($old)
    {
        SOY2DAOConfig::Dsn($old["dsn"]);
        SOY2DAOConfig::User($old["user"]);
        SOY2DAOConfig::Pass($old["pass"]);
    }

//   /**
//    * 記事のエイリアスをURLとして出力するためにエンコードする
//    */
//     public static function urlencodeForEntryAlias($alias)
//     {
//         return rawurlencode($alias);
//     }

  /**
   * 記事雛形が利用可能かどうか
   * @return boolean
   */
    public static function isEntryTemplateEnabled()
    {
        return self::_isSimpleXmlEnabled();
    }

  /**
   * ページ雛形が利用可能かどうか
   * @return boolean
   */
    public static function isPageTemplateEnabled()
    {
        return self::checkZipEnable() && self::_isSimpleXmlEnabled();
    }

  /**
   * simplexml_load_fileが利用可能かどうか
   * @return boolean
   */
    private static function _isSimpleXmlEnabled()
    {
        return function_exists("simplexml_load_file");
    }
}
