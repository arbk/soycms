<?php

class RecentCommentListAction extends SOY2Action
{
    public $limit = SOYCMS_INI_NUMOF_COMMENT_RECENT;
    public $labelId;

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }

    public function execute()
    {
        // 最新エントリーを取得
        $dao = SOY2DAOFactory::create("cms.EntryCommentDAO");
        $dao->setLimit($this->limit);
        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryCommentLogic");
        $array = $logic->getComments(array($this->labelId), $this->limit, 0);
        $this->setAttribute("comments", $array);

        return SOY2Action::SUCCESS;
    }
}
