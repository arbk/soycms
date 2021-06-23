<?php

class TrackbackListAction extends SOY2Action
{
    private $pageId;
    private $offset;
    private $limit;

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function execute()
    {
        $labels = $this->getLabelsByPageId($this->pageId);

        if (null===$labels) {
            return SOY2Action::FAILED;
        }

        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryTrackbackLogic");
        $this->setAttribute("list", $logic->getByLabelIds($labels, $this->limit, $this->offset));
        $this->setAttribute("count", $logic->getTotalCount());

        return SOY2Action::SUCCESS;
    }

    public function getLabelsByPageId($pageId)
    {
        try {
            $pageDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
            return array($pageDAO->getById($pageId)->getBlogLabelId());
        } catch (Exception $e) {
            return null;
        }
    }
}
