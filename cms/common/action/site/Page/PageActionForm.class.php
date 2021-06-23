<?php
class PageActionForm extends SOY2ActionForm
{
    private $id;
    private $uri;
    private $title;
    private $template;
    private $pageType;
    private $openPeriodStart;
    private $openPeriodEnd;
    private $isPublished;
    private $parentPageId;
    private $pageTitleFormat;
    private $icon;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @validator number
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @validator string {"regex":"[^\\\/]$|^$"}
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getPageType()
    {
        return $this->pageType;
    }

    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    }

    public function getOpenPeriodStart()
    {
        return $this->openPeriodStart;
    }

    /**
     * 今日以降
     */
    public function setOpenPeriodStart($openPeriodStart)
    {
        $tmpDate = (strlen($openPeriodStart)) ? strtotime($openPeriodStart) : false;
        if ($tmpDate === false) {
            $this->openPeriodStart = null;
        } else {
            $this->openPeriodStart = $tmpDate;
        }
    }

    public function getOpenPeriodEnd()
    {
        return $this->openPeriodEnd;
    }

    /**
     * 今日以降
     */
    public function setOpenPeriodEnd($openPeriodEnd)
    {
        $tmpDate = (strlen($openPeriodEnd)) ? strtotime($openPeriodEnd) : false;
        if ($tmpDate === false) {
            $this->openPeriodEnd = null;
        } else {
            $this->openPeriodEnd = $tmpDate;
        }
    }
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * @validator number {"min":0,"max":1}
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }
    public function getParentPageId()
    {
        return $this->parentPageId;
    }
    public function setParentPageId($parentPageId)
    {
        $this->parentPageId = $parentPageId;
    }

    public function getPageTitleFormat()
    {
        return $this->pageTitleFormat;
    }
    public function setPageTitleFormat($pageTitleFormat)
    {
        $this->pageTitleFormat = $pageTitleFormat;
    }

    public function getIcon()
    {
        return $this->icon;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
