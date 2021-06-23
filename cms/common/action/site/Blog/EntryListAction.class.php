<?php

class EntryListAction extends SOY2Action
{
    private $pageId;

    private $offset;
    private $limit;

    /**
     * 取得しない列項目
     */
    private $ignoreColumns;

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function setIgnoreColumns($ignoreColumns)
    {
        $this->ignoreColumns = $ignoreColumns;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        $blog = $dao->getById($this->pageId);
        $categoryLabels = array($blog->getBlogLabelId());

        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic", array("offset" => $this->offset, "limit" => $this->limit));
        $logic->setIgnoreColumns($this->ignoreColumns);
        $entries = $logic->getOpenEntryByLabelIds($categoryLabels);
        $this->setAttribute("entries", $entries);
        return SOY2Action::SUCCESS;
    }
}
