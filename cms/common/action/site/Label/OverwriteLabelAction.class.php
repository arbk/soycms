<?php

class OverwriteLabelAction extends SOY2Action
{
    public function execute($request, $form, $response)
    {
        //記事管理者は操作禁止
        if (class_exists("UserInfoUtil") && !UserInfoUtil::hasSiteAdminRole()) {
            return SOY2Action::FAILED;
        }

        $dao = SOY2DAOFactory::create("cms.EntryLabelDAO");
        try {
            $dao->begin();
            foreach ($this->getLabelIds() as $label) {
                if (in_array($label, $form->label)) {
                    //ラベルを設定
                    foreach ($form->entry as $entry) {
                        try {
                              $dao->getByParam($label, $entry);
                              //すでに設定してある
                              //do nothing
                        } catch (Exception $e) {
                            //設定してない
                            CMSPlugin::callEventFunc('onEntryLabelApply', array("entryId"=>$entry,"labelId"=>$label));
                            $dao->setByParams($entry, $label);
                        }
                    }
                } else {
                    //ラベルを削除
                    foreach ($form->entry as $entry) {
                        $do = false;
                        try {
                            $dao->getByParam($label, $entry);
                            //すでに設定してある
                            $do = true;
                        } catch (Exception $e) {
                            //設定してない
                            //do nothing
                        }
                        if ($do) {
                            CMSPlugin::callEventFunc('onEntryLabelRemove', array("entryId"=>$entry,"labelId"=>$label));
                            $dao->deleteByParams($entry, $label);
                        }
                    }
                }
            }
            $dao->commit();
        } catch (Exception $e) {
            $dao->rollback();
        }
        return SOY2Action::SUCCESS;
    }

    public function getLabelIds()
    {
        $dao = SOY2DAOFactory::create("cms.LabelDAO");
        $labels = $dao->get();
        $labelIds = array_map(function ($v) {
            return $v->getId();
        }, $labels);
        return $labelIds;
    }
}

class OverwriteLabelActionForm extends SOY2ActionForm
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
