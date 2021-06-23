<?php

class ToggleApprovedAction extends SOY2Action
{
    public function execute($request, $form, $response)
    {
        $dao = SOY2DAOFactory::create("cms.EntryTrackbackDAO");
        if (!is_array($form->trackback_id)) {
            $form->trackback_id = array();
        }
        try {
            foreach ($form->trackback_id as $trackbackId) {
                $dao->setCertification($trackbackId, $form->state);
            }
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        $this->setAttribute("new_stat", $form->state);
        return SOY2Action::SUCCESS;
    }
}

class ToggleApprovedActionForm extends SOY2ActionForm
{
    public $trackback_id;
    public $state;

    /**
     * @validator number {"require":true}
     */
    public function setTrackback_id($trackback_id)
    {
        $this->trackback_id = $trackback_id;
    }

    /**
     * @validator number {"min":0,"max":1}
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
