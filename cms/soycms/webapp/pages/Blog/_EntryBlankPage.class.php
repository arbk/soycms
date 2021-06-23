<?php

class _EntryBlankPage extends CMSWebPageBase
{
    private $pageId;

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->createAdd("entry_create_link", "HTMLLink", array(
            "link"=>SOY2PageController::createLink("Blog.Entry.".$this->pageId)
        ));
    }

    public function getPageId()
    {
        return $this->pageId;
    }
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
}
