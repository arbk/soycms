<?php

class DisplayCtrlModel extends HTMLModel
{
    const CTRL_TYPE_LABEL = "label";
    const CTRL_TYPE_LABEL_ATTR_LBL = "cms:label";
    const CTRL_TYPE_LABEL_ATTR_CAT = "cms:category";

    private $ctrlType = null;

    public function getCtrlType()
    {
        return $this->ctrlType;
    }
    public function setCtrlType($ctrlType)
    {
        $this->ctrlType = $ctrlType;
    }

    private $entry = null;

    public function getEntry()
    {
        return $this->entry;
    }
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    private function getEntryLabelIds($entry, $attrType)
    {
        $lbls = (self::CTRL_TYPE_LABEL_ATTR_CAT === $attrType) ? $entry->getlabels() : $entry->getEveryLabels();
        $lblIds = array();
        foreach ($lbls as $lbl) {
            $lblIds[] = $lbl->getId();
        }
        return $lblIds;
    }

    private function ctrlByLabelProc($ctrled_visible, $attrType)
    {
        $attr = $this->getAttribute($attrType);
        if (0 < strlen($attr)) {
            $eLblIds = $this->getEntryLabelIds($this->entry, $attrType);
            $eLblIds_empty = (1 > count($eLblIds));
            if ("+" === $attr) {
                $ctrled_visible = !$eLblIds_empty;
            } elseif ("-" === $attr) {
                $ctrled_visible = $eLblIds_empty;
            } else {
                $ctrled_visible = ("-" === $attr[0]);
                $lbls = explode(",", $attr);
                foreach ($lbls as $lbl) { // labelの指定は OR で 後優先
                    $vd = true;
                    if ("-" === $lbl[0]) {
                        $vd = false;
                        $lbl = substr($lbl, 1);
                    }
                    if (in_array($lbl, $eLblIds, true)) {
                        $ctrled_visible = $vd;
                    }
                }
            }
        }
        return $ctrled_visible;
    }
    private function ctrlByLabel($ctrled_visible)
    {
        $ctrled_visible = $this->ctrlByLabelProc($ctrled_visible, self::CTRL_TYPE_LABEL_ATTR_LBL);
        $ctrled_visible = $this->ctrlByLabelProc($ctrled_visible, self::CTRL_TYPE_LABEL_ATTR_CAT);
        return $ctrled_visible;
    }

    public function execute()
    {
        $ctrled_visible = $this->getVisible();
        if (null!==$this->entry) {
            switch ($this->ctrlType) {
                // label | category
                case self::CTRL_TYPE_LABEL:
                    $ctrled_visible = $this->ctrlByLabel($ctrled_visible);
                    break;

                // none
                default:
                    break;
            }
        }
        $this->setVisible($ctrled_visible);
        parent::execute();
    }
}
