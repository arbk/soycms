<?php
class EndStage extends StageBase
{
    public function __construct()
    {
    }

    public function execute()
    {
        $redirect = @$this->wizardObj->end_redirect_address;
        if ((null===$redirect)) {
            $redirect = "";
        }

        $sessionStage = SOY2ActionSession::getUserSession()->setAttribute("WizardCurrentStage", null);
        $wizObj = SOY2ActionSession::getUserSession()->setAttribute("WizardObject", null);

        $this->jump($redirect);
    }
}
