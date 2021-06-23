<?php

class CreateAction extends SOY2Action
{
    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if ($form->hasError()) {
            foreach ($form as $key => $value) {
                if ($form->isError($key)) {
                    $this->setErrorMessage($key, $form->getErrorString($key));
                }
            }
            return SOY2Action::FAILED;
        }

        if (null!==$form->separate) {
            $form->separate = false;
        } else {
            $form->separate = true;
        }

        //初期管理者のみ
        if (!UserInfoUtil::isDefaultUser()) {
            return SOY2Action::FAILED;
        }

        //SOYCMSのシステムディレクトリだった場合はスキップ
        if ($this->_isSoyCMSDir($form)) {
            return SOY2Action::FAILED;
        }

        $result = SOY2Logic::createInstance("logic.admin.Site.SiteLogic")->createSite($form->siteId, $form->siteName, $form->encoding, $form->separate, $form->copyFrom);
        if (!$result) {
            return SOY2Action::FAILED;
        }

        $site = SOY2DAOFactory::create("admin.SiteDAO")->getById($result);
        $this->setAttribute("Site", $site);


        //サイトURLを、サイト用DB SiteConfigに挿入
        try {
            $dsn = SOY2DAOConfig::Dsn();
            SOY2DAOConfig::Dsn($site->getDataSourceName());

            $siteConfigDao = SOY2DAOFactory::create("cms.SiteConfigDAO");
            $siteConfig = $siteConfigDao->get();
            $siteConfig->setConfigValue("url", $site->getUrl());
            $siteConfigDao->updateSiteConfig($siteConfig);

            SOY2DAOConfig::Dsn($dsn);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        return SOY2Action::SUCCESS;
    }

    public function _isSoyCMSDir($form)
    {
        $targetDir = str_replace("\\", "/", SOYCMS_TARGET_DIRECTORY .$form->siteId);
        $soyDir = str_replace("\\", "/", dirname(SOY2::RootDir()));

        $targetDir = str_replace("//", "/", $targetDir);

        /*
        作ろうとしている
        /usr/local/apache2/htdocs/hoge
        /usr/local/apache2/htdocs/soycms/fuga

        SYSDIR = /usr/local/apache2/htdocs/soycms
        /usr/local/apache2/htdocs/soycms/admin
        /usr/local/apache2/htdocs/soycms/common
        /usr/local/apache2/htdocs/soycms/soycms
        */

        if (strpos($soyDir, $targetDir, 0) === 0) {
            return true;
        }

        $sysflag_fname =  $targetDir."/SOYCMS_SYSTEM_DIRECTORY";

        if (file_exists($sysflag_fname)) {
            return true;
        } else {
            return false;
        }
    }
}

class CreateActionForm extends SOY2ActionForm
{
    public $siteId; //  サイトID  半角英数字
    public $siteName; //  サイト名  全角？文字
    public $encoding; //  文字コード  数字
    public $copyFrom;  //  コピー元サイト
    public $separate = null;

    /**
     * @validator string {"min" :1 ,"regex" : "^[a-zA-Z0-9\\-_\\.]+$","require" : true }
     */
    public function setSiteId($value)
    {
        $this->siteId = $value;
    }

    /**
     * @validator string {"max" : 100, "min" : 1, "require" : true }
     */
    public function setSiteName($value)
    {
        $this->siteName = $value;
    }

    /**
     * @validator number
     */
    public function setEncoding($value)
    {
        $this->encoding = $value;
    }
    public function setSeparate($separate)
    {
        $this->separate = $separate;
    }

    public function setCopyFrom($copyFrom)
    {
        $this->copyFrom = $copyFrom;
    }
}
