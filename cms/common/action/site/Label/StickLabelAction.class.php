<?php

class StickLabelAction extends SOY2Action
{
    public function execute($request, $form, $response)
    {
        $dao = SOY2DAOFactory::create("cms.EntryLabelDAO");
        try {
            $dao->begin();
            foreach ($form->label as $label) {
                foreach ($form->entry as $entry) {
                    try {
                        $dao->getByParam($label, $entry);
                    } catch (Exception $e) {
                        CMSPlugin::callEventFunc('onEntryLabelApply', array("entryId"=>$entry,"labelId"=>$label));
                        $dao->setByParams($entry, $label);
                    }
                }
            }
            $dao->commit();
        } catch (Exception $e) {
            $dao->rollback();
        }

        return SOY2Action::SUCCESS;
    }
}

class StickLabelActionForm extends SOY2ActionForm
{
    public $label = array();
    public $entry = array();

    public function setLabel($label)
    {
        $this->label = $label;
        if (null===$this->label) {
            $this->label = array();
        }
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
        if (null===$this->entry) {
            $this->label = array();
        }
    }
}
