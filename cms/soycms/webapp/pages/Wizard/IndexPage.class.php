<?php
require_once(__DIR__ . "/_stage/base/StageBase.class.php");

class IndexPage extends CMSWebPageBase
{
    public function doPost()
    {
        if (soy2_check_token()) {
            $contentPage = $this->getContentPage();

            if (isset($_GET["next"])) {
                if ($contentPage->checkNext()) {
                    SOY2ActionSession::getUserSession()->setAttribute("WizardCurrentStage", $contentPage->getNextObject());
                } else {
                  // エラーの時の処理をどうしよう
                }
            }

            if (isset($_GET["back"])) {
                if ($contentPage->checkBack()) {
                    SOY2ActionSession::getUserSession()->setAttribute("WizardCurrentStage", $contentPage->getBackObject());
                } else {
                  // エラーの時の処理をどうしよう
                }
            }

          // データを保存
            $this->saveWizardObject($contentPage->getWizardObj());
        }

        $this->jump("Wizard");
    }

    public function __construct()
    {
        parent::__construct();

        $contentPage = $this->getContentPage();

        HTMLHead::addLink("avav", array(
        "rel"=>"stylesheet",
        "type"=>"text/css",
        "href"=>SOY2PageController::createRelativeLink("./css/Wizard/create_page.css")
        ));

        $this->createAdd("next_link", "HTMLLink", array(
        "link"=>"#",
        "onclick"=>"$('#main_form').attr('action','" . SOY2PageController::createLink("Wizard") . "?next');$('#main_form').submit();",
        "text"=>$contentPage->getNextString(),
        "visible"=>strlen($contentPage->getNextString()) != 0
        ));

        $this->createAdd("prev_link", "HTMLLink", array(
        "link"=>"#",
        "onclick"=>"$('#main_form').attr('action','" . SOY2PageController::createLink("Wizard") . "?back');$('#main_form').submit();",
        "text"=>$contentPage->getBackString(),
        "visible"=>strlen($contentPage->getBackString()) != 0
        ));

        $this->add("content", $contentPage);

        $this->createAdd("main_form", "HTMLForm");
    }

    public function getContentPage()
    {
        $currentStage = $this->detectStages();
        $stageClassName = "Wizard._stage." . $currentStage;
        try {
            $page = $this->create("content", $stageClassName);
        } catch (Exception $e) {
            $page = $this->create("content", "Wizard._stage.EndStage");
        }

        $page->setWizardObj($this->getWizardObject());

        return $page;
    }

    public function detectStages()
    {
        $sessionStage = SOY2ActionSession::getUserSession()->getAttribute("WizardCurrentStage");

        if ((null===$sessionStage)) {
            return "StartStage";
        } else {
            return $sessionStage;
        }
    }

    public function getWizardObject()
    {
        $wizObj = SOY2ActionSession::getUserSession()->getAttribute("WizardObject");

        if ((null===$wizObj)) {
            $wizObj = new StdClass();
        } else {
            $wizObj = unserialize($wizObj);
        }

        return $wizObj;
    }

    public function saveWizardObject($wizObj)
    {
        SOY2ActionSession::getUserSession()->setAttribute("WizardObject", serialize($wizObj));
    }
}
