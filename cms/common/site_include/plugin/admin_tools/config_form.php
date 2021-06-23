<?php

class AdminToolsPlugin_FormPage extends WebPage
{
    private $pluginObj; // AdminToolsPlugin Obj
    const CACHE_DIR = SOYCMS_CACHE_DIRNAME."/";
//  const TMB_DIR = ".tmb/";
    private $history_tbls = array("EntryHistory", "TemplateHistory");

    public function __construct()
    {
    }

    public function doPost()
    {
        if (!soy2_check_token()) {
            error_log("Token check error. : " . __METHOD__);
            return;
        }

        $msg = array();
        $site_dir = $this->getSiteDir();

        try {
            if ((null===$site_dir)) {
                error_log("Site dir is null. : " . __METHOD__);
                throw new Exception("失敗 : サイト・ディレクトリを取得できませんでした.");
            }

            if (isset($_POST["cache_clear"])) {
                $msg[] = $this->clearCache($site_dir);
            } elseif (isset($_POST["file_mdchg"])) {
                $msg[] = $this->changeFileMode($site_dir);
            } elseif (isset($_POST["db_hclear"])) {
                $msg[] = $this->clearHistoryData();
            } elseif (isset($_POST["db_optimize"])) {
                $msg[] = $this->optimizeDB();
            } elseif (isset($_POST["db_hclr_opt"])) {
                $msg[] = $this->clearHistoryData();
                $msg[] = $this->optimizeDB();
            }
        } catch (Exception $e) {
            $msg[] = $e->getMessage();
        }

        $this->pluginObj->msg = $msg;
        CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
        CMSPlugin::redirectConfigPage();
    }

    public function execute()
    {
        WebPage::__construct();

        $site_dir = $this->getSiteDir();

        $this->createAdd("admin_tools_form", "HTMLForm", array());

        $hasMsg = !empty($this->pluginObj->msg);
        $this->addLabel("proc_msg", array(
        "html"=>$hasMsg ? implode("<br>", $this->pluginObj->msg) : ""
        ));
        $this->addModel("proc_msg_v", array(
        "visible"=>$hasMsg
        ));
        if ($hasMsg) {
            $this->pluginObj->msg = array();
            CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
        }

        $this->addLabel("cache_pstg", array(
        "text"=>SOYCMS_USE_CACHE ? "利用する" : "利用しない"
        ));
        $this->addModel("cache_pstg_v", array(
        "visible"=>SOYCMS_USE_CACHE
        ));
        $this->addLabel("cache_pstg_lt", array(
        "text"=>(0 < SOYCMS_CACHE_LIFETIME) ? SOYCMS_CACHE_LIFETIME . " 秒" : "期限なし（データに変更がない限り永続的に有効）"
        ));
        $this->addLabel("cache_targ_d", array(
        "html"=>$site_dir . self::CACHE_DIR
//            . "<br>" . $site_dir.self::TMB_DIR,
        ));

        $this->addLabel("file_fmd_f", array(
        "text"=>decoct(F_MODE_FILE_USR)
        ));
        $this->addLabel("file_fmd_d", array(
        "text"=>decoct(F_MODE_DIR_USR)
        ));
        $userFilesDir = CMSUtil::getUserFilesDir($site_dir);
        $userFilesDirEnable = is_dir($userFilesDir);
        $this->addLabel("file_targ_d", array(
        "text"=>$userFilesDir
        ));
        $this->addModel("file_targ_d_alert_v", array(
        "visible"=>!$userFilesDirEnable
        ));
        $this->addModel("file_mdchg_v", array(
        "visible"=>$userFilesDirEnable
        ));

        $this->addLabel("db_type", array(
        "text"=>SOYCMS_DB_TYPE
        ));
        $this->addLabel("db_targ_t", array(
        "html"=>implode("<br>", $this->history_tbls)
        ));
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }

    public function getTemplateFilePath()
    {
        return __DIR__ . "/config_form.html";
    }

    private function getSiteDir()
    {
        if (defined("_SITE_ROOT_")) {
            return _SITE_ROOT_ . "/";
        } elseif (class_exists("UserInfoUtil")) {
            return UserInfoUtil::getSiteDirectory();
        } else {
            return null;
        }
    }

    private function clearCache($site_dir)
    {
        CMSUtil::unlinkAllIn($site_dir . self::CACHE_DIR, true);
//      CMSUtil::unlinkAllIn($site_dir . self::TMB_DIR);
        return "キャッシュファイル を削除しました.";
    }

    private function changeFileMode($site_dir)
    {
        CMSUtil::chmodAllInUserFilesDir($site_dir);
        return "ユーザーファイルのモードを設定しました.";
    }

    private function clearHistoryData()
    {
        try {
            $dao = new SOY2DAO();
            foreach ($this->history_tbls as $tbl) {
                $dao->executeUpdateQuery("delete from " . $tbl, array());
            }
        } catch (Exception $e) {
            error_log("History data deleting is failed. : " . __METHOD__ . "\n" . $e->getMessage());
            throw new Exception("失敗 : ヒストリデータ が削除できませんでした.");
        }
        return "DBに格納された ヒストリデータ を削除しました.";
    }

    private function optimizeDB()
    {
        try {
            $dao = new SOY2DAO();
            if (SOYCMS_DB_TYPE === "sqlite") {
                $dao->executeUpdateQuery("vacuum", array());
                $dao->executeUpdateQuery("reindex", array());
            } elseif (SOYCMS_DB_TYPE === "mysql") {
                $all_tbls = $dao->executeQuery("show tables", array());
                foreach ($all_tbls as $tbl) {
                    $dao->executeUpdateQuery('optimize table `' . $tbl[0] . '`', array());
                }
            } else {
                throw new Exception("DB type unknown. : [DB_TYPE:" . SOYCMS_DB_TYPE . "] : " . __METHOD__);
            }
        } catch (Exception $e) {
            error_log("DB optimization is failed. : " . __METHOD__ . "\n" . $e->getMessage());
            throw new Exception("失敗 : DBを最適化できませんでした.");
        }
        return "DBを最適化しました.";
    }
}
