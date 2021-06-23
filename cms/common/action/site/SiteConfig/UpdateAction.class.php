<?php

class UpdateAction extends SOY2Action
{
    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        SOY2::import("domain.cms.SiteConfig");
        // if (!is_numeric($form->defaultUploadResizeWidth)) {
        //     $form->defaultUploadResizeWidth = null;
        // }
        $siteConfig = SOY2::cast("SiteConfig", $form);
        $siteConfig->setConfigValue("url", $_POST["url"]);
        $logic = SOY2Logic::createInstance("logic.site.SiteConfig.SiteConfigLogic");
        try {
            $logic->update($siteConfig);

            $site = UserInfoUtil::getSite();
            $site->setSiteName($siteConfig->getName());
            UserInfoUtil::updateSite($site);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
        return SOY2Action::SUCCESS;
    }
}

class UpdateActionForm extends SOY2ActionForm
{
    public $name;
    public $description;
    public $charset;
    public $siteConfig;
//  public $defaultUploadMode = 0;
    public $defaultUploadDirectory;
//  public $defaultUploadResizeWidth;
//  public $createUploadDirectoryByDate;
    public $isShowOnlyAdministrator;
    public $useLabelCategory;

    public function setName($name)
    {
        $this->name = $name;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function setSiteConfig($siteConfig)
    {
        $this->siteConfig = $siteConfig;
    }

    // public function setDefaultUploadMode($mode)
    // {
    //     $this->defaultUploadMode = $mode;
    // }

    public function setDefaultUploadDirectory($defaultUploadDirectory)
    {
        $this->defaultUploadDirectory = $defaultUploadDirectory;
    }

    // public function getCreateUploadDirectoryByDate()
    // {
    //     return $this->createUploadDirectoryByDate;
    // }
    // public function setCreateUploadDirectoryByDate($createUploadDirectoryByDate)
    // {
    //     $this->createUploadDirectoryByDate = $createUploadDirectoryByDate;
    // }

    // public function setDefaultUploadResizeWidth($defaultUploadResizeWidth)
    // {
    //     $this->defaultUploadResizeWidth = $defaultUploadResizeWidth;
    // }

    public function getIsShowOnlyAdministrator()
    {
        return $this->isShowOnlyAdministrator;
    }
    public function setIsShowOnlyAdministrator($isShowOnlyAdministrator)
    {
        $this->isShowOnlyAdministrator = $isShowOnlyAdministrator;
    }

    public function getUseLabelCategory()
    {
        return $this->useLabelCategory;
    }
    public function setUseLabelCategory($useLabelCategory)
    {
        $this->useLabelCategory = $useLabelCategory;
    }
}
