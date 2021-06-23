<?php

class BlogPageLogic extends SOY2LogicBase
{
    public function getBlogPageList()
    {
        $dao = SOY2DAOFactory::create("cms.PageDAO");
        $pages = $dao->getByPageType(Page::PAGE_TYPE_BLOG);

        foreach ($pages as $key => $value) {
            $pages[$key] = $value->getTitle();
        }

        return $pages;
    }

    public function get()
    {
        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        return $dao->get();
    }
}
