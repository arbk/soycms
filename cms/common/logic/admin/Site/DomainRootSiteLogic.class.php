<?php

class DomainRootSiteLogic extends SOY2LogicBase
{
//  const FRONT_CONTROLLER_FILENAME = "index.php";
//  const HTACCESS_FILENAME = ".htaccess";

    private $pathOfController;
    private $pathOfHtaccess;

    public function __construct()
    {
        $this->pathOfController = SOYCMS_TARGET_DIRECTORY . F_FRCTRLER;
        $this->pathOfHtaccess   = SOYCMS_TARGET_DIRECTORY . F_HTACCESS;
    }

    public function create()
    {
        $this->createBackUp();
        $this->createController();
        $this->createHtaccess();
    }

    public function delete()
    {
        $this->createBackUp();
        @unlink($this->pathOfHtaccess);
        @unlink($this->pathOfController);
    }

    public function getPathOfController()
    {
        return $this->pathOfController;
    }

    public function getPathOfHtaccess()
    {
        return $this->pathOfHtaccess;
    }

    public function createHtaccess($htaccessFileContent = null)
    {
        if ((null===$htaccessFileContent)) {
            $htaccessFileContent = $this->getHtaccess();
        }
        CMSUtil::makeHtaccess(dirname($this->pathOfHtaccess), $htaccessFileContent);
    }

    public function getHtaccess($site = null)
    {
        if (!$site) {
            $site = $this->getDomainRootSite();
        }

        $tmp = array();
        $tmp[] = "# @generated by SOY CMS at " . date("Y-m-d H:i:s");
        $tmp[] = "RewriteEngine on";

        // 元のサイトの静的ファイルを参照してみる
        if ($site) {
            $tmp[] = "";
            $tmp[] = 'RewriteCond %{REQUEST_FILENAME} !-f';
            $tmp[] = 'RewriteCond %{DOCUMENT_ROOT}/' . $site->getSiteId() . '%{REQUEST_URI} -f [OR]';
            $tmp[] = 'RewriteCond %{DOCUMENT_ROOT}' . $site->getSiteId() . '%{REQUEST_URI} -f';
            $tmp[] = 'RewriteRule ^(.*)$ /' . $site->getSiteId() . '/$1 [L]';
            $tmp[] = "";
        }

        $tmp[] = "# 常にhttpsでアクセスさせる（httpでのアクセスをhttpsにリダイレクトする）";
        $tmp[] = "#RewriteCond %{HTTPS} =off";
        $tmp[] = "#RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L,QSA]";
        $tmp[] = "";
        $tmp[] = 'RewriteCond %{REQUEST_FILENAME} !-f';
        $tmp[] = 'RewriteCond %{REQUEST_FILENAME}/' . F_FRCTRLER . ' !-f';
        $tmp[] = 'RewriteCond %{REQUEST_FILENAME}/index.html !-f';
        $tmp[] = 'RewriteCond %{REQUEST_FILENAME}/index.htm !-f';
        $tmp[] = 'RewriteCond %{REQUEST_URI} !^/server-status';  // Apacheのmod_statusへの配慮
        $tmp[] = 'RewriteCond %{REQUEST_URI} !^/server-info';    // Apacheのmod_infoへの配慮
        $tmp[] = 'RewriteCond %{REQUEST_URI} !^/' . F_FRCTRLER . '/';

        if (SOYCMS_PHP_CGI_MODE) {
            $tmp[] = 'RewriteRule ^(.*)$ ' . F_FRCTRLER . '?pathinfo=$1&%{QUERY_STRING} [L]';
        } else {
            $tmp[] = 'RewriteRule ^(.*)$ ' . F_FRCTRLER . '/$1 [L]';
        }

        $tmp[] = "#---SOY CMS end of htaccess file --";
        $tmp[] = "";

        return implode("\n", $tmp);
    }

    public function createController($site = null)
    {
        file_put_contents($this->pathOfController, $this->getController($site));
        @chmod($this->pathOfController, F_MODE_FILE);
    }

    public function getController($site = null)
    {
        if (!$site) {
            $site = $this->getDomainRootSite();
        }

        $controller = array();
        if ($site) {
            $controller[] = "<?php ";
            $controller[] = '/* @generated by SOY CMS at ' . date("Y-m-d H:i:s") . '*/';
            $controller[] = 'require_once("' . $site->getSiteId() . '/' . F_FRCTRLER . '");';
            $controller[] = "";
        }
        return implode("\n", $controller);
    }

    /**
     * バックアップ作成
     */
    public function createBackUp()
    {
        CMSUtil::createBackup($this->pathOfController);
        CMSUtil::createBackup($this->pathOfHtaccess);
    }

    public function checkCreatedController($filepath)
    {
        $str = file_get_contents($filepath);

        if (preg_match('/@generated by SOY ?CMS/', $str)) {
            return true;
        }

        return false;
    }

    /**
     * ルートサイト取得
     */
    private function getDomainRootSite()
    {
        static $dao;
        if (!$dao) {
            $dao = SOY2DAOFactory::create("admin.SiteDAO");
        }
        try {
            return $dao->getDomainRootSite();
        } catch (Exception $e) {
            return null;
        }
    }
}
