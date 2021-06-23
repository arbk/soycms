<?php

class CategoryListAction extends SOY2Action
{
    private $pageId;

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        $blog = $dao->getById($this->pageId);
        $categoryLabels = $blog->getCategoryLabelList();

        $dao = SOY2DAOFactory::create("cms.LabelDAO");
        $ret_val = array();
        foreach ($categoryLabels as $labels) {
            try {
                $label = $dao->getById($labels);
                $ret_val[$label->getId()] = $label;
            } catch (Exception $e) {
              //do nothing
            }
        }
        $this->setAttribute("categoryLabels", $ret_val);
        return SOY2Action::SUCCESS;
    }
}
