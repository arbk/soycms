<?php

class TemplateDeleteStage extends StageBase
{
    public function __construct()
    {
        $id = @$_GET["id"];

        $wizObj = SOY2ActionSession::getUserSession()->getAttribute("Template.Create.WizardObject");
        $wizObj = ((null===$wizObj)) ? new stdClass() : unserialize($wizObj);
        $templates = $wizObj->template->getTemplate();

        if (isset($templates[$id])) {
            unset($templates[$id]);
        }

        $wizObj->template->setTemplate($templates);

        SOY2ActionSession::getUserSession()->setAttribute("Template.Create.WizardObject", serialize($wizObj));
        SOY2ActionSession::getUserSession()->setAttribute("Template.Create.WizardCurrentStage", "TemplateSettingStage");

        $this->jump("Template.Create");

        exit();
    }
}
