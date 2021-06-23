<?php

class PageCountAction extends SOY2Action
{
    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.PageDAO");
        $result = $dao->getTotalPageCount();
        $this->setAttribute("PageCount", $result["count"]);
        return SOY2Action::SUCCESS;
    }
}
