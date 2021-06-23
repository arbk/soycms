<?php

class UpdateBlogConfigAction extends SOY2Action
{
    public $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        // 生成フラグの変換
        if ($form->entryPageUri == $form->categoryPageUri
        || $form->entryPageUri == $form->monthPageUri
        || $form->entryPageUri == $form->rssPageUri
        || $form->categoryPageUri == $form->monthPageUri
        || $form->categoryPageUri == $form->rssPageUri
        || $form->monthPageUri == $form->rssPageUri
        ) {
            return SOY2Action::FAILED;
        }

        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        $page = $dao->getById($this->id);
        $page = SOY2::cast($page, $form);

        //カテゴリ未選択の場合は、pageオブジェクトも未選択にする
        if (null===$form->getCategoryLabelList()) {
            $page->setCategoryLabelList(null);
        }

        $page->setId($this->id);

        try {
            $dao->updatePageConfig($page);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        return SOY2Action::SUCCESS;
    }
}

class UpdateBlogConfigActionForm extends SOY2ActionForm
{
    public $title;
    public $icon;
    public $uri;
    public $parentPageId;
    public $description;

    public $blogLabelId;
    public $categoryLabelList = array();

    public $generateTopFlag;
    public $generateEntryFlag;
    public $generateMonthFlag;
    public $generateCategoryFlag;
    public $generateRssFlag;

    public $topDisplayCount;
    public $monthDisplayCount;
    public $categoryDisplayCount;
    public $rssDisplayCount;

    public $topPageUri;
    public $entryPageUri;
    public $monthPageUri;
    public $categoryPageUri;
    public $rssPageUri;

    public $topTitleFormat;
    public $entryTitleFormat;
    public $monthTitleFormat;
    public $categoryTitleFormat;
    public $feedTitleFormat;

    public $topEntrySortType;
    public $topEntrySort;
    public $monthEntrySortType;
    public $monthEntrySort;
    public $categoryEntrySortType;
    public $categoryEntrySort;

    public $topEntryOpdata;
    public $monthEntryOpdata;
    public $categoryEntryOpdata;

    public $openPeriodStart;
    public $openPeriodEnd;

    public $isPublished;

    public $bBlockConfig;

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getIcon()
    {
        return $this->icon;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function setUri($uri)
    {
        $this->uri = $uri;
    }
    public function getParentPageId()
    {
        return $this->parentPageId;
    }
    public function setParentPageId($parentPageId)
    {
        $this->parentPageId = $parentPageId;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getBlogLabelId()
    {
        return $this->blogLabelId;
    }
    public function setBlogLabelId($blogLabelId)
    {
        $this->blogLabelId = $blogLabelId;
    }
    public function getCategoryLabelList()
    {
        return $this->categoryLabelList;
    }
    public function setCategoryLabelList($categoryLabelList)
    {
        $this->categoryLabelList = $categoryLabelList;
    }

    public function getGenerateTopFlag()
    {
        return $this->generateTopFlag;
    }
    public function setGenerateTopFlag($generateTopFlag)
    {
        $this->generateTopFlag = (boolean)$generateTopFlag;
    }
    public function getGenerateEntryFlag()
    {
        return $this->generateEntryFlag;
    }
    public function setGenerateEntryFlag($generateEntryFlag)
    {
        $this->generateEntryFlag = (boolean)$generateEntryFlag;
    }
    public function getGenerateMonthFlag()
    {
        return $this->generateMonthFlag;
    }
    public function setGenerateMonthFlag($generateMonthFlag)
    {
        $this->generateMonthFlag = (boolean)$generateMonthFlag;
    }
    public function getGenerateCategoryFlag()
    {
        return $this->generateCategoryFlag;
    }
    public function setGenerateCategoryFlag($generateCategoryFlag)
    {
        $this->generateCategoryFlag = (boolean)$generateCategoryFlag;
    }
    public function getGenerateRssFlag()
    {
        return $this->generateRssFlag;
    }
    public function setGenerateRssFlag($generateRssFlag)
    {
        $this->generateRssFlag = (boolean)$generateRssFlag;
    }

    public function getTopDisplayCount()
    {
        return $this->topDisplayCount;
    }
    public function setTopDisplayCount($topDisplayCount)
    {
        $this->topDisplayCount = $topDisplayCount;
    }
    public function getMonthDisplayCount()
    {
        return $this->monthDisplayCount;
    }
    public function setMonthDisplayCount($monthDisplayCount)
    {
        $this->monthDisplayCount = $monthDisplayCount;
    }
    public function getCategoryDisplayCount()
    {
        return $this->categoryDisplayCount;
    }
    public function setCategoryDisplayCount($categoryDisplayCount)
    {
        $this->categoryDisplayCount = $categoryDisplayCount;
    }
    public function getRssDisplayCount()
    {
        return $this->rssDisplayCount;
    }
    public function setRssDisplayCount($rssDisplayCount)
    {
        $this->rssDisplayCount = $rssDisplayCount;
    }

    public function getTopPageUri()
    {
        return $this->topPageUri;
    }
    public function setTopPageUri($topPageUri)
    {
        $this->topPageUri = $topPageUri;
    }
    public function getEntryPageUri()
    {
        return $this->entryPageUri;
    }
    public function setEntryPageUri($entryPageUri)
    {
        $this->entryPageUri = $entryPageUri;
    }
    public function getMonthPageUri()
    {
        return $this->monthPageUri;
    }
    public function setMonthPageUri($monthPageUri)
    {
        $this->monthPageUri = $monthPageUri;
    }
    public function getCategoryPageUri()
    {
        return $this->categoryPageUri;
    }
    public function setCategoryPageUri($categoryPageUri)
    {
        $this->categoryPageUri = $categoryPageUri;
    }
    public function getRssPageUri()
    {
        return $this->rssPageUri;
    }
    public function setRssPageUri($rssPageUri)
    {
        $this->rssPageUri = $rssPageUri;
    }

    public function getTopTitleFormat()
    {
        return $this->topTitleFormat;
    }
    public function setTopTitleFormat($topTitleFormat)
    {
        $this->topTitleFormat = $topTitleFormat;
    }
    public function getEntryTitleFormat()
    {
        return $this->entryTitleFormat;
    }
    public function setEntryTitleFormat($entryTitleFormat)
    {
        $this->entryTitleFormat = $entryTitleFormat;
    }
    public function getMonthTitleFormat()
    {
        return $this->monthTitleFormat;
    }
    public function setMonthTitleFormat($monthTitleFormat)
    {
        $this->monthTitleFormat = $monthTitleFormat;
    }
    public function getCategoryTitleFormat()
    {
        return $this->categoryTitleFormat;
    }
    public function setCategoryTitleFormat($categoryTitleFormat)
    {
        $this->categoryTitleFormat = $categoryTitleFormat;
    }
    public function getFeedTitleFormat()
    {
        return $this->feedTitleFormat;
    }
    public function setFeedTitleFormat($feedTitleFormat)
    {
        $this->feedTitleFormat = $feedTitleFormat;
    }

    public function getTopEntrySortType()
    {
        return $this->topEntrySortType;
    }
    public function setTopEntrySortType($topEntrySortType)
    {
        $this->topEntrySortType = $topEntrySortType;
    }

    public function getTopEntrySort()
    {
        return $this->topEntrySort;
    }
    public function setTopEntrySort($topEntrySort)
    {
        $this->topEntrySort = $topEntrySort;
    }

    public function getMonthEntrySortType()
    {
        return $this->monthEntrySortType;
    }
    public function setMonthEntrySortType($monthEntrySortType)
    {
        $this->monthEntrySortType = $monthEntrySortType;
    }

    public function getMonthEntrySort()
    {
        return $this->monthEntrySort;
    }
    public function setMonthEntrySort($monthEntrySort)
    {
        $this->monthEntrySort = $monthEntrySort;
    }

    public function getCategoryEntrySortType()
    {
        return $this->categoryEntrySortType;
    }
    public function setCategoryEntrySortType($categoryEntrySortType)
    {
        $this->categoryEntrySortType = $categoryEntrySortType;
    }

    public function getCategoryEntrySort()
    {
        return $this->categoryEntrySort;
    }
    public function setCategoryEntrySort($categoryEntrySort)
    {
        $this->categoryEntrySort = $categoryEntrySort;
    }

    public function getTopEntryOpdata()
    {
        return $this->topEntryOpdata;
    }
    public function setTopEntryOpdata($topEntryOpdata)
    {
        $this->topEntryOpdata = $topEntryOpdata;
    }
    public function getMonthEntryOpdata()
    {
        return $this->monthEntryOpdata;
    }
    public function setMonthEntryOpdata($monthEntryOpdata)
    {
        $this->monthEntryOpdata = $monthEntryOpdata;
    }
    public function getCategoryEntryOpdata()
    {
        return $this->categoryEntryOpdata;
    }
    public function setCategoryEntryOpdata($categoryEntryOpdata)
    {
        $this->categoryEntryOpdata = $categoryEntryOpdata;
    }

    public function getOpenPeriodStart()
    {
        return $this->openPeriodStart;
    }
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
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    public function getBBlockConfig()
    {
        return $this->bBlockConfig;
    }
    public function setBBlockConfig($bBlockConfig)
    {
        $this->bBlockConfig = $bBlockConfig;
    }
}
