<?php
/**
 * @entity cms.BlogPage
 */
class BlogPageDAO
{
    public function get()
    {
        $dao = $this->getPageDAO();
        $pages = $dao->getByPageType(Page::PAGE_TYPE_BLOG);

        foreach ($pages as $key => $page) {
            $pages[$key] = $this->cast($page);
        }

        return $pages;
    }

    /**
     * IDを指定して取得
     */
    public function getById($id)
    {
        $obj = $this->getPageDAO()->getById($id);

        if ($obj->getPageType() != Page::PAGE_TYPE_BLOG) {
            throw new Exception("This Page is not Blog Page.");
        }

        return $this->cast($obj);
    }

    public function cast($page)
    {
        $blogPage = SOY2::cast("BlogPage", $page);

        $config = $blogPage->getPageConfigObject();

        if ($config) {
            $config = unserialize($blogPage->getPageConfig());
            SOY2::cast($blogPage, $config);
        }

        return $blogPage;
    }

    /**
     * BlogPageを初期化する
     */
    public function insert(Page $page)
    {
        $dao = $this->getPageDAO();
        $page = SOY2::cast("BlogPage", $page);

        // 初期データ
        $page->setGenerateTopFlag(true);
        $page->setGenerateEntryFlag(true);
        $page->setGenerateMonthFlag(true);
        $page->setGenerateCategoryFlag(true);
        $page->setGenerateRssFlag(true);

        $page->setTopDisplayCount(10);
        $page->setMonthDisplayCount(10);
        $page->setCategoryDisplayCount(10);
        $page->setRssDisplayCount(10);

        $page->setTopPageUri("");
        $page->setEntryPageUri("article");
        $page->setMonthPageUri("month");
        $page->setCategoryPageUri("category");
        $page->setRssPageUri("feed");

        $page->setTopTitleFormat("%BLOG%");
        $page->setEntryTitleFormat("%ENTRY% - %BLOG%");
        $page->setMonthTitleFormat("%YEAR%-%MONTH% - %BLOG%");
        $page->setCategoryTitleFormat("%CATEGORY% - %BLOG%");
        $page->setFeedTitleFormat("%BLOG%");

        $page->setTopEntrySortType(BlogPage::ORDER_TYPE_CDT);
        $page->setTopEntrySort(BlogPage::ENTRY_SORT_DESC);
        $page->setMonthEntrySortType(BlogPage::ORDER_TYPE_CDT);
        $page->setMonthEntrySort(BlogPage::ENTRY_SORT_DESC);
        $page->setCategoryEntrySortType(BlogPage::ORDER_TYPE_CDT);
        $page->setCategoryEntrySort(BlogPage::ENTRY_SORT_DESC);

        $page->setTopEntryOpdata(BlogPage::ENTRY_OPDATA_ALL);
        $page->setMonthEntryOpdata(BlogPage::ENTRY_OPDATA_ALL);
        $page->setCategoryEntryOpdata(BlogPage::ENTRY_OPDATA_ALL);

        $configObj = $page->getConfigObj();
        $page->setPageConfig($configObj);

        $id = $dao->insert($page);

        return $id;
    }

    /**
     * ページの設定を更新する
     */
    public function updatePageConfig(BlogPage $page)
    {

        $dao = $this->getPageDAO();
        $_page = $dao->getById($page->getId());

        // テンプレートは更新しない
        $page->setTemplate($_page->getTemplate());

        $configObj = $page->getConfigObj();
        $page->setPageConfig($configObj);
        $dao->update($page);
        $dao->updatePageConfig($page);
    }

    public function update(BlogPage $page)
    {
        $dao = $this->getPageDAO();
        $dao->update($page);
    }

    public function getPageDAO()
    {
        return SOY2DAOFactory::create("cms.PageDAO");
    }
}
