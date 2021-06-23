<?php
/**
 * コメントのリストを取得します
 */
class CommentListAction extends SOY2Action
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

        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryCommentLogic");
        try {
            $comments = $logic->getComments($labels, $this->limit, $this->offset);
        } catch (Exception $e) {
            $this->setErrorMessage("failed", "コメントの一覧の取得に失敗しました。");
            return SOY2Action::FAILED;
        }

        $this->setAttribute("list", $comments);
        $this->setAttribute("count", $logic->getTotalCount());

        return SOY2Action::SUCCESS;
    }

    /**
     * ページIDに関連付けられているラベル一覧の取得
     * @return array
     */
    public function getLabelsByPageId($pageId)
    {
        try {
            $pageDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
            return array($pageDAO->getById($pageId)->getBlogLabelId())  ;
        } catch (Exception $e) {
            return null;
        }
    }
}
