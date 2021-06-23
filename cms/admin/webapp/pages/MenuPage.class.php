<?php

class MenuPage extends CMSWebPageBase
{

    public $type = SOY2HTML::SOY_BODY;

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->addLink("administratorlink", array(
        "link" => SOY2PageController::createLink("Administrator.List")
        ));

        $this->addLink("sitelink", array(
        "link" => SOY2PageController::createLink("Site.List")
        ));

        $this->addLink("siterolelink", array(
        "link" => SOY2PageController::createLink("SiteRole.List")
        ));
    }
}
