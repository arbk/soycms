<?php
SOY2::import("domain.cms.Page");

/**
 * @table BlogPage
 */
class BlogPage extends Page
{
    const TEMPLATE_TOP = "top";
    const TEMPLATE_ARCHIVE = "archive";
    const TEMPLATE_ENTRY = "entry";
    const TEMPLATE_POPUP = "popup";

    const ORDER_TYPE_CDT = "otcdt";
    const ORDER_TYPE_TTL = "otttl";

    const ENTRY_SORT_ASC = "asc";
    const ENTRY_SORT_DESC = "desc";

    const ENTRY_OPDATA_ALL = "opall";
    const ENTRY_OPDATA_CNT = "opcnt";
    const ENTRY_OPDATA_TTL = "opttl";

    private $author;
    private $description;

    // 使用するラベル一覧
    private $blogLabelId;
    // カテゴリ分けに使うラベル一覧
    private $categoryLabelList = array();

    // トップページの生成フラグ
    private $generateTopFlag = true;
    // 単体ページの生成フラグ
    private $generateEntryFlag = true;
    // 月別ページの生成フラグ
    private $generateMonthFlag = true;
    // カテゴリ別ページの生成フラグ
    private $generateCategoryFlag = true;
    // RSSの生成フラグ
    private $generateRssFlag = true;

    // トップページの表示件数
    private $topDisplayCount = 10;
    // 月別ページの表示件数
    private $monthDisplayCount = 10;
    // カテゴリ別ページの表示件数
    private $categoryDisplayCount = 10;
    // RSSの表示件数
    private $rssDisplayCount = 10;

    // トップページのURL
    private $topPageUri = "";
    // 単体ページのURL
    private $entryPageUri = "article";
    // 月別ページのURL
    private $monthPageUri = "month";
    // カテゴリ別ページのURL
    private $categoryPageUri = "category";
    // RSSページのURL
    private $rssPageUri = "feed";

    // トップページのタイトルフォーマット
    private $topTitleFormat = "%BLOG%";
    // 単体ページのタイトルフォーマット
    private $entryTitleFormat = "%ENTRY% - %BLOG%";
    // 月別ページのタイトルフォーマット
    private $monthTitleFormat = "%YEAR%-%MONTH% - %BLOG%";
    // カテゴリー別ページのタイトルフォーマット
    private $categoryTitleFormat = "%CATEGORY% - %BLOG%";
    // フィードのタイトルフォーマット
    private $feedTitleFormat = "%BLOG%";

    // トップページの表示順
    private $topEntrySortType = self::ORDER_TYPE_CDT;
    private $topEntrySort = self::ENTRY_SORT_DESC;
    // 月別ページの表示順
    private $monthEntrySortType = self::ORDER_TYPE_CDT;
    private $monthEntrySort = self::ENTRY_SORT_DESC;
    // カテゴリ別ページの表示順
    private $categoryEntrySortType = self::ORDER_TYPE_CDT;
    private $categoryEntrySort = self::ENTRY_SORT_DESC;

    // トップページの出力項目
    private $topEntryOpdata = self::ENTRY_OPDATA_ALL;
    // 月別ページの出力項目
    private $monthEntryOpdata = self::ENTRY_OPDATA_ALL;
    // カテゴリ別ページの出力項目
    private $categoryEntryOpdata = self::ENTRY_OPDATA_ALL;

    // コメントのデフォルト承認
    private $defaultAcceptComment;
    // Trackbackのデフォルト承認
    private $defaultAcceptTrackback;

    // b_blockの設定
    private $bBlockConfig;

    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
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
    public function setCategoryLabelList($list)
    {
        if (!is_array($list)) {
            return;
        }
        $this->categoryLabelList = $list;
    }

    public function getGenerateTopFlag()
    {
//      if(defined('CMS_PREVIEW_MODE') && CMS_PREVIEW_MODE){
//        return true;
//      }else{
            return $this->generateTopFlag;
//      }
    }
    public function getRawGenerateTopFlag()
    {
        return $this->generateTopFlag;
    }
    public function setGenerateTopFlag($generateTopFlag)
    {
        $this->generateTopFlag = $generateTopFlag;
    }
    public function getGenerateEntryFlag()
    {
//      if(defined('CMS_PREVIEW_MODE') && CMS_PREVIEW_MODE){
//        return true;
//      }else{
            return $this->generateEntryFlag;
//      }
    }
    public function setGenerateEntryFlag($generateEntryFlag)
    {
        $this->generateEntryFlag = $generateEntryFlag;
    }
    public function getGenerateMonthFlag()
    {
//      if(defined('CMS_PREVIEW_MODE') && CMS_PREVIEW_MODE){
//        return true;
//      }else{
            return $this->generateMonthFlag;
//      }
    }
    public function setGenerateMonthFlag($generateMonthFlag)
    {
        $this->generateMonthFlag = $generateMonthFlag;
    }
    public function getGenerateCategoryFlag()
    {
//      if(defined('CMS_PREVIEW_MODE') && CMS_PREVIEW_MODE){
//        return true;
//      }else{
            return $this->generateCategoryFlag;
//      }
    }
    public function setGenerateCategoryFlag($generateCategoryFlag)
    {
        $this->generateCategoryFlag = $generateCategoryFlag;
    }
    public function getGenerateRssFlag()
    {
//      if(defined('CMS_PREVIEW_MODE') && CMS_PREVIEW_MODE){
//        return true;
//      }else{
            return $this->generateRssFlag;
//      }
    }
    public function setGenerateRssFlag($generateRssFlag)
    {
        $this->generateRssFlag = $generateRssFlag;
    }

    public function getTopDisplayCount()
    {
        return $this->topDisplayCount;
    }
    public function setTopDisplayCount($topDisplayCount)
    {
        $this->topDisplayCount = (int)$topDisplayCount;
    }
    public function getMonthDisplayCount()
    {
        return $this->monthDisplayCount;
    }
    public function setMonthDisplayCount($monthDisplayCount)
    {
        $this->monthDisplayCount = (int)$monthDisplayCount;
    }
    public function getCategoryDisplayCount()
    {
        return $this->categoryDisplayCount;
    }
    public function setCategoryDisplayCount($categoryDisplayCount)
    {
        $this->categoryDisplayCount = (int)$categoryDisplayCount;
    }
    public function getRssDisplayCount()
    {
        return $this->rssDisplayCount;
    }
    public function setRssDisplayCount($rssDisplayCount)
    {
        $this->rssDisplayCount = (int)$rssDisplayCount;
    }

    public function getTopPageUri()
    {
        return $this->topPageUri;
    }
    public function setTopPageUri($topPageUri)
    {
        $this->topPageUri = $topPageUri;
    }
    /**
     * @param startWithSlash /で始まるかどうか
     */
    public function getEntryPageUri($startWithSlash = false)
    {
        if ($startWithSlash && strlen($this->entryPageUri) > 0) {
            return "/" . $this->entryPageUri;
        } else {
            return $this->entryPageUri;
        }
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
    public function setEntryTitleFormat($EntryTitleFormat)
    {
        $this->entryTitleFormat = $EntryTitleFormat;
    }
    public function getMonthTitleFormat()
    {
        return $this->monthTitleFormat;
    }
    public function setMonthTitleFormat($MonthTitleFormat)
    {
        $this->monthTitleFormat = $MonthTitleFormat;
    }
    public function getCategoryTitleFormat()
    {
        return $this->categoryTitleFormat;
    }
    public function setCategoryTitleFormat($CategoryTitleFormat)
    {
        $this->categoryTitleFormat = $CategoryTitleFormat;
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

    public function getDefaultAcceptComment()
    {
        return $this->defaultAcceptComment;
    }
    public function setDefaultAcceptComment($defaultAcceptComment)
    {
        $this->defaultAcceptComment = $defaultAcceptComment;
    }

    public function getDefaultAcceptTrackback()
    {
        return $this->defaultAcceptTrackback;
    }
    public function setDefaultAcceptTrackback($defaultAcceptTrackback)
    {
        $this->defaultAcceptTrackback = $defaultAcceptTrackback;
    }

    public function getBBlockConfig()
    {
        if ((null===$this->bBlockConfig) || (is_array($this->bBlockConfig) && !count($this->bBlockConfig))) {
            foreach ($this->getBBlockList() as $tag) {
                $this->bBlockConfig[$tag] = 1;
            }
        }
        return $this->bBlockConfig;
    }
    public function setBBlockConfig($bBlockConfig)
    {
        $this->bBlockConfig = $bBlockConfig;
    }

    /**
     * トップページのURL
     */
    public function getTopPageURL($withPageUri = true)
    {
        if ($withPageUri && strlen($this->getUri()) > 0) {
            if (strlen($this->getTopPageUri()) > 0) {
                return $this->getUri() . "/" . $this->getTopPageUri();
            } else {
                return $this->getUri();
            }
        } else {
            return $this->getTopPageUri();
        }
    }
    /**
     * エントリーページのURLを取得（末尾はスラッシュ付き）
     *
     * @param withPageUri ページのUriを追加するかどうか
     */
    public function getEntryPageURL($withPageUri = true)
    {
        $url = "";
        if ($withPageUri && strlen($this->getUri()) > 0) {
            $url .= $this->getUri() . "/";
        }
        if (strlen($this->getEntryPageUri()) > 0) {
            $url .= $this->getEntryPageUri() . "/";
        }
        return $url;
    }
    /**
     * カテゴリーアーカイブのURL（末尾はスラッシュ付き）
     */
    public function getCategoryPageURL($withPageUri = true)
    {
        $url = "";
        if ($withPageUri && strlen($this->getUri()) > 0) {
            $url .= $this->getUri() . "/";
        }
        if (strlen($this->getCategoryPageUri()) > 0) {
            $url .= $this->getCategoryPageUri() . "/";
        }
        return $url;
    }
    /**
     * 月別アーカイブのURL（末尾はスラッシュ付き）
     */
    public function getMonthPageURL($withPageUri = true)
    {
        $url = "";
        if ($withPageUri && strlen($this->getUri()) > 0) {
            $url .= $this->getUri() . "/";
        }
        if (strlen($this->getMonthPageUri()) > 0) {
            $url .= $this->getMonthPageUri() . "/";
        }
        return $url;
    }
    /**
     * RSSページのURL
     */
    public function getRssPageURL($withPageUri = true)
    {
        if ($withPageUri && strlen($this->getUri()) > 0) {
            return $this->getUri() . "/" . $this->getRssPageUri();
        } else {
            return $this->getRssPageUri();
        }
    }

    /**
     * 保存用のstdObjectを返します。
     */
    public function getConfigObj()
    {
        $obj = new stdClass();

        $obj->topPageUri = $this->topPageUri;
        $obj->entryPageUri = $this->entryPageUri;
        $obj->monthPageUri = $this->monthPageUri;
        $obj->categoryPageUri = $this->categoryPageUri;
        $obj->rssPageUri = $this->rssPageUri;

        $obj->blogLabelId = $this->blogLabelId;
        $obj->categoryLabelList = $this->categoryLabelList;

        $obj->topDisplayCount = $this->topDisplayCount;
        $obj->monthDisplayCount = $this->monthDisplayCount;
        $obj->categoryDisplayCount = $this->categoryDisplayCount;
        $obj->rssDisplayCount = $this->rssDisplayCount;

        $obj->topEntrySortType = $this->topEntrySortType;
        $obj->topEntrySort = $this->topEntrySort;
        $obj->monthEntrySortType = $this->monthEntrySortType;
        $obj->monthEntrySort = $this->monthEntrySort;
        $obj->categoryEntrySortType = $this->categoryEntrySortType;
        $obj->categoryEntrySort = $this->categoryEntrySort;

        $obj->topEntryOpdata = $this->topEntryOpdata;
        $obj->monthEntryOpdata = $this->monthEntryOpdata;
        $obj->categoryEntryOpdata = $this->categoryEntryOpdata;

        $obj->generateTopFlag = $this->generateTopFlag;
        $obj->generateMonthFlag = $this->generateMonthFlag;
        $obj->generateCategoryFlag = $this->generateCategoryFlag;
        $obj->generateRssFlag = $this->generateRssFlag;
        $obj->generateEntryFlag = $this->generateEntryFlag;

        $obj->topTitleFormat = @$this->topTitleFormat;
        $obj->monthTitleFormat = @$this->monthTitleFormat;
        $obj->categoryTitleFormat = @$this->categoryTitleFormat;
        $obj->entryTitleFormat = @$this->entryTitleFormat;
        $obj->feedTitleFormat = @$this->feedTitleFormat;

        $obj->description = @$this->description;
        $obj->author = @$this->author;

        $obj->defaultAcceptComment = @$this->defaultAcceptComment;
        $obj->defaultAcceptTrackback = @$this->defaultAcceptTrackback;

        $obj->bBlockConfig = $this->bBlockConfig;

        return $obj;
    }

    private function _getTemplate()
    {
        $array = @unserialize($this->getTemplate());

        if (!is_array($array)) {
            $array = array(
            BlogPage::TEMPLATE_ARCHIVE=>"",
            BlogPage::TEMPLATE_TOP=>"",
            BlogPage::TEMPLATE_ENTRY=>"",
            BlogPage::TEMPLATE_POPUP=>"",
            );
        }

        return $array;
    }

    /**
     * ブログトップページ
     */
    public function getTopTemplate()
    {
        $template = $this->_getTemplate();
        return $template[BlogPage::TEMPLATE_TOP];
    }

    /**
     * エントリーテンプレート
     */
    public function getEntryTemplate()
    {
        $template = $this->_getTemplate();
        return $template[BlogPage::TEMPLATE_ENTRY];
    }

    /**
     * アーカイブテンプレート
     */
    public function getArchiveTemplate()
    {
        $template = $this->_getTemplate();
        return $template[BlogPage::TEMPLATE_ARCHIVE];
    }

    /**
     * ポップアップコメントテンプレート
     */
    public function getPopUpTemplate()
    {
        $template = $this->_getTemplate();
        return $template[BlogPage::TEMPLATE_POPUP];
    }

    /** 便利なメソッド **/

    const B_BLOCK_CATEGORY = "category";
    const B_BLOCK_ARCHIVE = "archive";
    const B_BLOCK_ARCHIVE_BY_YEAR = "archive_by_year";
    const B_BLOCK_ARCHIVE_EVERY_YEAR = "archive_every_year";
//  const B_BLOCK_RECENT_ENTRY_LIST = "recent_entry_list";
    const B_BLOCK_RECENT_COMMENT_LIST = "recent_comment_list";
    const B_BLOCK_RECENT_TRACKBACK_LIST = "recent_trackback_list";
    const B_BLOCK_PAGER = "pager";
    const B_BLOCK_CURRENT_CATEGORY = "current_category";
    const B_BLOCK_CURRENT_ARCHIVE = "current_archive";
    const B_BLOCK_CURRENT_CATEGORY_OR_ARCHIVE = "current_category_or_archive";
    const B_BLOCK_COMMENT_FORM = "comment_form";
    const B_BLOCK_COMMENT_LIST = "comment_list";
    const B_BLOCK_TRACKBACK_LINK = "trackback_link";
    const B_BLOCK_TRACKBACK_LIST = "trackback_list";
    const B_BLOCK_TOP_LINK = "top_link";
    const B_BLOCK_META_FEED_LINK = "meta_feed_link";
    const B_BLOCK_RSS_LINK = "rss_link";

    public function getBBlockList()
    {
        return array(
            self::B_BLOCK_CATEGORY,
            self::B_BLOCK_ARCHIVE,
            self::B_BLOCK_ARCHIVE_BY_YEAR,
            self::B_BLOCK_ARCHIVE_EVERY_YEAR,
//          self::B_BLOCK_RECENT_ENTRY_LIST,
            self::B_BLOCK_RECENT_COMMENT_LIST,
            self::B_BLOCK_RECENT_TRACKBACK_LIST,
            self::B_BLOCK_PAGER,
            self::B_BLOCK_CURRENT_CATEGORY,
            self::B_BLOCK_CURRENT_ARCHIVE,
            self::B_BLOCK_CURRENT_CATEGORY_OR_ARCHIVE,
            self::B_BLOCK_COMMENT_FORM,
            self::B_BLOCK_COMMENT_LIST,
            self::B_BLOCK_TRACKBACK_LINK,
            self::B_BLOCK_TRACKBACK_LIST,
            self::B_BLOCK_TOP_LINK,
            self::B_BLOCK_META_FEED_LINK,
            self::B_BLOCK_RSS_LINK
        );
    }
}
