<?php
/**
 * @table Page
 */
class Page
{
    const PAGE_TYPE_NORMAL = 0;        // 標準ページ
//  const PAGE_TYPE_MOBILE = 100;      // 携帯用ページ（削除）
    const PAGE_TYPE_APPLICATION = 150; // アプリケーションページ
    const PAGE_TYPE_BLOG = 200;        // ブログページ
    const PAGE_TYPE_ERROR = 300;       // エラーページ

    const PAGE_ACTIVE = 1;
    const PAGE_ACTIVE_CLOSE_FUTURE = 2;
    const PAGE_ACTIVE_CLOSE_BEFORE = 3;

    const PAGE_OUTOFDATE = -1;
    const PAGE_NOTPUBLIC = -2;
    const PAGE_OUTOFDATE_BEFORE = -3;
    const PAGE_OUTOFDATE_PAST = -4;

    /**
     * @id
     */
    private $id;
    private $uri;
    private $title;
    private $template;
    private $isTrash;

    /**
     * @column page_type
     */
    private $pageType;

    /**
     * @column page_config
     */
    private $pageConfig;

    /**
     * @no_persistent
     */
    private $_pageConfig;
    private $openPeriodStart;
    private $openPeriodEnd;
    private $isPublished;
    private $udate;

    /**
     * @column parent_page_id
     */
    private $parentPageId;

    // 親子関係保持用
    /**
     * @no_persistent
     */
    private $childPages = array();

    /**
     * @no_persistent
     */
    private $parentPage;

    /**
     * @no_persistent
     */
    private $pageTitleFormat;
    private $icon;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUri()
    {
        return (string)$this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
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

    public function getPageConfig()
    {
        return $this->pageConfig;
    }

    public function setPageConfig($pageConfig)
    {
        if (is_object($pageConfig)) {
            $this->pageConfig = serialize($pageConfig);
            $this->_pageConfig = $pageConfig;
        } else {
            $this->pageConfig = $pageConfig;
            if (strlen($pageConfig) && strpos($pageConfig, 'O:8:"stdClass"') === 0) {
                $this->_pageConfig = unserialize($pageConfig);
            }
        }
    }

    public function getPageConfigObject()
    {
        if ((null===$this->_pageConfig)) {
            $this->_pageConfig = new stdClass();
            $this->pageConfig = serialize($this->_pageConfig);
        }
        return $this->_pageConfig;
    }

    public function getOpenPeriodStart()
    {
        return CMSUtil::decodeDate($this->openPeriodStart);
    }

    public function setOpenPeriodStart($openPeriodStart)
    {
        $this->openPeriodStart = $openPeriodStart;
    }

    public function getOpenPeriodEnd()
    {
        return CMSUtil::decodeDate($this->openPeriodEnd);
    }

    public function setOpenPeriodEnd($openPeriodEnd)
    {
        $this->openPeriodEnd = $openPeriodEnd;
    }

    public function getIsPublished()
    {
        if ((null===$this->isPublished)) {
            return 0;
        } else {
            return $this->isPublished;
        }
    }

    public function setIsPublished($isPublished)
    {
        if ((null===$isPublished)) {
            $this->isPublished = 0;
        } else {
            $this->isPublished = $isPublished;
        }
    }

    public function getParentPageId()
    {
        return $this->parentPageId;
    }

    public function setParentPageId($parentPageId)
    {
        if ((null===$parentPageId) || strlen($parentPageId) != 0) {
            $this->parentPageId = $parentPageId;
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 子ページを返す
     */
    public function getChildPages()
    {
        if (is_array($this->childPages)) {
            ksort($this->childPages);
        }
        return $this->childPages;
    }

    public function addChildPage($page)
    {
        $this->childPages[$page->getId()] = $page;
        $page->setParentPage($this);
    }

    /**
     * @param $childId
     */
    public function getNodePathCount($step = null)
    {
        if ($step === null) {
            $count = count($this->childPages);
        } else {
            $count = 0;
        }
        $counter = 0;

        foreach ($this->childPages as $key => $childPage) {
            if (null!==$step && $counter > $step) {
                break;
            }

            $tmp = $childPage->getNodePathCount();
            $count += max($tmp - 1, 0);
            $counter++;
        }
        return $count;
    }

    /**
     * 親ページを返す
     */
    public function getParentPage()
    {
        return $this->parentPage;
    }

    public function setParentPage($parentPage)
    {
        $this->parentPage = $parentPage;
    }

    public function getUdate()
    {
        return CMSUtil::decodeDate($this->udate);
    }

    public function setUdate($udate)
    {
        $this->udate = CMSUtil::encodeDate($udate, true);
    }

    public function getIsTrash()
    {
        if ((null===$this->isTrash)) {
            return 0;
        } else {
            return $this->isTrash;
        }
    }

    public function setIsTrash($isTrash)
    {
        if ((null===$isTrash)) {
            $this->isTrash = 0;
        } else {
            $this->isTrash = $isTrash;
        }
    }

    /**
     * 削除できるかどうか
     * @return boolean
     */
    public function isDeletable()
    {
        if ($this->pageType == Page::PAGE_TYPE_ERROR) {
            if ($this->id > 1 &&
             SOY2Logic::createInstance("logic.site.Page.PageLogic")->hasMultipleErrorPage()) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * 複製できるかどうか
     * @return boolean
     */
    public function isCopyable()
    {
        if ($this->pageType == Page::PAGE_TYPE_ERROR) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 現在このページの状態を返します
     * @return PAGE_ACTIVE 公開状態
     * @return PAGE_OUTOFDATE 期間外
     * @return PAGE_NOTPUBLIC 未公開状態
     *
     *         if(isActive() > 0){
     *         //公開状態のときの処理
     *         } else{
     *         //未公開状態の時の処理
     *         }
     */
    public function isActive($with_before_after = false)
    {
        if (!$this->isPublished) {
            return self::PAGE_NOTPUBLIC;
        }
        $now = SOYCMS_NOW;
        $start = CMSUtil::encodeDate($this->openPeriodStart, true);
        $end = CMSUtil::encodeDate($this->openPeriodEnd, false);

        if ($start < $now && $end > $now) {
            if ($with_before_after) {
                if ($end != CMSUtil::encodeDate(null, false)) {
                    return self::PAGE_ACTIVE_CLOSE_FUTURE;
                } elseif ($start != CMSUtil::encodeDate(null, true)) {
                    return self::PAGE_ACTIVE_CLOSE_BEFORE;
                } else {
                    return self::PAGE_ACTIVE;
                }
            } else {
                return self::PAGE_ACTIVE;
            }
        } else {
            if ($with_before_after) {
                if ($start >= $now) {
                    return self::PAGE_OUTOFDATE_BEFORE;
                } else {
                    return self::PAGE_OUTOFDATE_PAST;
                }
            } else {
                return self::PAGE_OUTOFDATE;
            }
        }
    }

    /**
     * ブログかどうかを返す
     */
    public function isBlog()
    {
        if ($this->pageType == Page::PAGE_TYPE_BLOG) {
            return true;
        } else {
            return false;
        }
    }

//  /**
//   * 携帯ページかどうか
//   */
//  function isMobile(){
//      if($this->pageType == Page::PAGE_TYPE_MOBILE){
//          return true;
//      }else{
//          return false;
//      }
//  }
    public function getStateMessage()
    {
        switch ($this->isActive()) {
            case self::PAGE_ACTIVE:
                return CMSMessageManager::get("SOYCMS_STAY_PUBLISHED");
            case self::PAGE_OUTOFDATE:
                return CMSMessageManager::get("SOYCMS_OUTOFDATE");
            case self::PAGE_NOTPUBLIC:
                return CMSMessageManager::get("SOYCMS_NOT_PUBLISHED");
            default:
                return CMSMessageManager::get("SOYCMS_OMG");
        }
    }

    public function getPageTitleFormat()
    {
        $config = $this->getPageConfigObject();
        if (!property_exists($config, "PageTitleFormat")) {
            $config->PageTitleFormat = "";
        }
        return $config->PageTitleFormat;
    }

    public function setPageTitleFormat($pageTitleFormat)
    {
        $pageConfig = $this->getPageConfigObject();
        $pageConfig->PageTitleFormat = $pageTitleFormat;
        $this->setPageConfig($pageConfig);
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIconUrl()
    {
        $icon = $this->getIcon();

        if ($this->getPageType() == Page::PAGE_TYPE_ERROR) {
            $icon = "notfound.gif";
        }
        if (!$icon && $this->getPageType() == Page::PAGE_TYPE_BLOG) {
            $icon = "blog_default.gif";
        }
        if (!$icon) {
            $icon = "page_default.gif";
        }
//      if($this->getIsTrash())$icon = "deleted.gif";

        return CMS_PAGE_ICON_DIRECTORY_URL . $icon;
    }
}
