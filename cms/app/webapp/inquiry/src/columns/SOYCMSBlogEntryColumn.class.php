<?php

class SOYCMSBlogEntryColumn extends SOYInquiry_ColumnBase
{

    /**
     * ユーザに表示するようのフォーム
     */
    public function getForm($attr = array())
    {
        $title = (isset($_GET["entry_id"]) && is_numeric($_GET["entry_id"])) ? $this->_getEntryTitle() : null;
        if (strlen($title)) {
            $title = soy2_h($title);
        }

        $html = array();
        $html[] = $title;
        $html[] = "<input type=\"hidden\" name=\"data[" . $this->getColumnId() . "]\" value=\"" . $title . "\" />";

        return implode("\n", $html);
    }

    private function _getEntryTitle()
    {
        CMSApplication::switchAdminMode();

        $siteId = trim(substr(_SITE_ROOT_, strrpos(_SITE_ROOT_, "/")), "/");

        try {
            $site = SOY2DAOFactory::create("admin.SiteDAO")->getBySiteId($siteId);
        } catch (Exception $e) {
            $site = new Site();
        }

        $old["dsn"] = SOY2DAOConfig::dsn();
        $old["user"] = SOY2DAOConfig::user();
        $old["pass"] = SOY2DAOConfig::pass();

        SOY2DAOConfig::dsn($site->getDataSourceName());
        if (strpos($site->getDataSourceName(), "mysql") === 0) {
            require_once(_CMS_COMMON_DIR_ . "/config/db/mysql.php");
            SOY2DAOConfig::user(ADMIN_DB_USER);
            SOY2DAOConfig::pass(ADMIN_DB_PASS);
        }

        try {
            $title = SOY2DAOFactory::create("cms.EntryDAO")->getOpenEntryById($_GET["entry_id"], SOYCMS_NOW)->getTitle();
        } catch (Exception $e) {
            $title = null;
        }

        SOY2DAOConfig::dsn($old["dsn"]);
        SOY2DAOConfig::user($old["user"]);
        SOY2DAOConfig::pass($old["pass"]);

        CMSApplication::switchAppMode();

        return $title;
    }

    /**
     * 設定画面で表示する用のフォーム
     */
    public function getConfigForm()
    {
    }

    /**
     * 保存された設定値を渡す
     */
    public function setConfigure($config)
    {
        SOYInquiry_ColumnBase::setConfigure($config);
    }

    public function getConfigure()
    {
        return parent::getConfigure();
    }

    public function validate()
    {
    }
}
