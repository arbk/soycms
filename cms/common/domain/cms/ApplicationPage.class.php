<?php
SOY2::import("domain.cms.Page");

class ApplicationPage extends Page
{
    private $applicationId = "";

    /**
     * 保存用のstdObjectを返します
     */
    public function getConfigObj()
    {
        $obj = parent::getPageConfigObject();

        $obj->applicationId = $this->getApplicationId();
        return $obj;
    }

    public function getApplicationId()
    {
        return (string)$this->applicationId;
    }
    public function setApplicationId($applicationId)
    {
        $this->applicationId = (string)$applicationId;
    }
}
