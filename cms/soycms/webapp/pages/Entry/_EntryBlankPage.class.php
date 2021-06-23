<?php

class _EntryBlankPage extends CMSWebPageBase
{
    private $labelIds = array();

    public function setLabelIds($labelIds)
    {
        $this->labelIds = $labelIds;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->createAdd("create_link", "HTMLLink", array(
            "link" => SOY2PageController::createLink("Entry.Create") . "/" .implode("/", $this->labelIds)
        ));
    }
}
