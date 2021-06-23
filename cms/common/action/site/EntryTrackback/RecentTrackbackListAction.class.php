<?php

class RecentTrackbackListAction extends SOY2Action
{
    private $labelId;
    private $limit = SOYCMS_INI_NUMOF_TRACKBACK_RECENT;

    public function setlabelId($labelId)
    {
        $this->labelId = $labelId;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function execute()
    {
        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryTrackbackLogic");
        $this->setAttribute("trackbacks", $logic->getByLabelIds(array($this->labelId), $this->limit, 0));

        return SOY2Action::SUCCESS;
    }
}
