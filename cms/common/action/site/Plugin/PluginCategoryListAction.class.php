<?php

class PluginCategoryListAction extends SOY2Action
{
    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.PluginDAO");
        $this->setAttribute("list", $dao->getCategoryArray());
        return SOY2Action::SUCCESS;
    }
}
