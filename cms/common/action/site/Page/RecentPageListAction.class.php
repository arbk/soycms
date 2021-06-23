<?php

class RecentPageListAction extends SOY2Action
{
    private $limit = SOYCMS_INI_NUMOF_PAGE_RECENT;

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.PageDAO");
        $dao->setLimit($this->limit);
        $this->setAttribute("list", $dao->getRecentPages());

        return SOY2Action::SUCCESS;
    }
}
