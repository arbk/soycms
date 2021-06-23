<?php

class SiteConfig
{
    const CHARSET_UTF_8 = 1;
    const CHARSET_SHIFT_JIS = 2;
    const CHARSET_EUC_JP = 3;

    private $name;
    private $siteConfig;
    private $charset;
    private $description;

    /**
     * 設定の入った配列がserializeされた文字列を返す
     */
    public function getSiteConfig()
    {
        return $this->siteConfig;
    }
    public function setSiteConfig($config)
    {
        //$this->siteConfigには常にserializeされた文字列が入る
        if (is_string($config)) {
            $this->siteConfig = $config;
        } else {
            $this->siteConfig = serialize($config);
        }
    }
    public function getCharset()
    {
        return $this->charset;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 設定の配列を返す
     */
    public function getSiteConfigArray()
    {
        if (strlen($this->siteConfig) && strpos($this->siteConfig, "a:") === 0 && ( $config = unserialize($this->siteConfig) ) !== false) {
            return $config;
        } else {
            return array();
        }
    }
    /**
     * 設定値を返す
     */
    public function getConfigValue($key)
    {
        $config = $this->getSiteConfigArray();
        if (is_array($config) && isset($config[$key])) {
            return $config[$key];
        } else {
            //値が見つからないとき
            return false;
        }
    }
    /**
     * 設定値を保持する
     */
    public function setConfigValue($key, $value)
    {
        $config = $this->getSiteConfigArray();
        $config[$key] = $value;
        $this->setSiteConfig($config);
    }

    /**
     * 最終更新時刻を設定
     */
    public function notifyUpdate()
    {
        $this->setConfigValue("udate", SOYCMS_NOW);
    }

    /**
     * 最終更新時刻を取得
     */
    public function getLastUpdateDate()
    {
        $udate = $this->getConfigValue("udate");
        if ($udate !== false) {
            return $udate;
        } else {
            return strtotime(date("Y-m-d 00:00:00"));
        }
    }

    // /**
    //  * 日付毎にディレクトリを作成するかどうか
    //  */
    // public function isCreateDefaultUploadDirectory()
    // {
    //     return (boolean)$this->getConfigValue("createUploadDirectoryByDate");
    // }

    // /**
    //  * 日付毎にディレクトリを作成するかどうかのフラグを保存
    //  */
    // public function setCreateUploadDirectoryByDate($value)
    // {
    //     $this->setConfigValue("createUploadDirectoryByDate", (int)$value);
    // }

    /**
     * 管理側にログインしている時のみ表示するかどうか
     */
    public function isShowOnlyAdministrator()
    {
        return (boolean)$this->getConfigValue("isShowOnlyAdministrator");
    }

    /**
     * 管理側にログインしている時のみ表示するかどうかのフラグを保存
     */
    public function setIsShowOnlyAdministrator($value)
    {
        $this->setConfigValue("isShowOnlyAdministrator", (int)$value);
    }

    public function getDefaultUploadDirectory()
    {
        return "/" . SOYCMS_USER_FILES_DIRNAME;

      /*
        $dir = $this->getConfigValue("upload_directory");
        if($dir === false){
            return "/files";
        }

        // ディレクトリの遡行は許されない
        $dir = str_replace("..","",$dir);

        // /始まりを強制
        if($dir[0] != '/'){
            $dir = '/'.$dir;
        }

        // 末尾の/は全て削除
        while(substr($dir,-1) == '/'){
            $dir = substr($dir,0,-1);
        }

        // /の連続は削除
        while(strpos($dir, "//") !== false){
            $dir = strtr($dir, array("//" => "/"));
        }

        return $dir;
        */
    }

    // /**
    //  * 記事投稿時のイメージの挿入の設定
    //  */
    // public function getDefaultUploadMode()
    // {
    //     $v = (int)$this->getConfigValue("uploadMode");
    //     if ($v === 0) {
    //         $v = 1;
    //     }
    //     return $v;
    // }
    // public function setDefaultUploadMode($mode)
    // {
    //     $this->setConfigValue("uploadMode", $mode);
    // }

    /**
     * アップロードディレクトリを作成して取得
     */
    public function getUploadDirectory()
    {
        return $this->getDefaultUploadDirectory();

//         $dir = $this->getDefaultUploadDirectory();

//         //日付別ディレクトリ
//         if ($this->isCreateDefaultUploadDirectory()) {
// //          SOY2::import("util.CMSFileManager");

//             $targetDir = UserInfoUtil::getSiteDirectory() . $dir . "/" . date("Ymd");
//             $targetUrl = $dir . "/" . date("Ymd");

//             //存在しなかったら作成する
//             if (!file_exists($targetDir)) {
//                 $res = @mkdir($targetDir);
//                 if (!$res) {
//                     return $dir;   //作成に失敗したら$dir
//                 }
//                 @chmod($targetDir, F_MODE_DIR);

//                 //ファイルDBに追加
// //              CMSFileManager::add($targetDir);
//             }

//             //ファイルDBになかったら追加する
// //          try {
// //              CMSFileManager::get($targetDir, $targetDir);
// //          } catch (Exception $e) {
// //              CMSFileManager::add($targetDir);
// //          }

//             if (file_exists($targetDir) && is_writable($targetDir)) {
//                 return $targetUrl;
//             }
//         }

//         return $dir;
    }

    public function setDefaultUploadDirectory($dir)
    {
        $this->setConfigValue("upload_directory", "/" . SOYCMS_USER_FILES_DIRNAME);
/*
        $this->setConfigValue("upload_directory", $dir);

        //正規化
        $this->setConfigValue("upload_directory", $this->getDefaultUploadDirectory());
*/
    }

    // public function getDefaultUploadResizeWidth()
    // {
    //     return $this->getConfigValue("resize_width");
    // }

    // public function setDefaultUploadResizeWidth($w)
    // {
    //     $this->setConfigValue("resize_width", $w);
    // }

    /**
     * 文字コード変換
     * (UTF-8→サイトの文字コード)
     */
    public function convertToSiteCharset($contents)
    {
        switch ($this->charset) {
            case SiteConfig::CHARSET_UTF_8:
                break;
            case SiteConfig::CHARSET_SHIFT_JIS:
                $contents = mb_convert_encoding($contents, 'SJIS-win', SOY2::CHARSET);
                break;
            case SiteConfig::CHARSET_EUC_JP:
                $contents = mb_convert_encoding($contents, 'eucJP-win', SOY2::CHARSET);
                break;
            default:
                break;
        }
        return $contents;
    }

    public function getCharsetText()
    {
        switch ($this->charset) {
            case SiteConfig::CHARSET_UTF_8:
                return SOY2::CHARSET;
                break;
            case SiteConfig::CHARSET_SHIFT_JIS:
                return "Shift_JIS";
                break;
            case SiteConfig::CHARSET_EUC_JP:
                return "EUC-JP";
                break;
            default:
                break;
        }
    }

    /**
     * 文字コード変換
     * (サイトの文字コード→UTF8)
     */
    public function convertFromSiteCharset($contents)
    {
        switch ($this->charset) {
            case SiteConfig::CHARSET_UTF_8:
                break;
            case SiteConfig::CHARSET_SHIFT_JIS:
                $contents = mb_convert_encoding($contents, SOY2::CHARSET, 'SJIS-win');
                break;
            case SiteConfig::CHARSET_EUC_JP:
                $contents = mb_convert_encoding($contents, SOY2::CHARSET, 'eucJP-win');
                break;
            default:
                break;
        }
        return $contents;
    }

    public static function getCharsetLists()
    {
        return array(
            SiteConfig::CHARSET_UTF_8     => SOY2::CHARSET,
            SiteConfig::CHARSET_SHIFT_JIS => "Shift_JIS",
            SiteConfig::CHARSET_EUC_JP    => "EUC-JP"
        );
    }

    /**
     * ラベルのカテゴリ分けを有効にするかどうか
     */
    public function useLabelCategory()
    {
        return (boolean)$this->getConfigValue("useLabelCategory");
    }
    /**
     * ラベルのカテゴリ分けを有効にするかどうかのフラグを保存
     */
    public function setUseLabelCategory($value)
    {
        $this->setConfigValue("useLabelCategory", (int)$value);
    }
}
