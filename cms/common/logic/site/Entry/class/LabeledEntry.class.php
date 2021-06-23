<?php
SOY2::import("domain.cms.Entry");

/**
 * @table Entry inner join EntryLabel on(Entry.id = EntryLabel.entry_id)
 */
class LabeledEntry extends Entry
{
    const ENTRY_ACTIVE = 1;
    const ENTRY_OUTOFDATE = -1;
    const ENTRY_NOTPUBLIC = -2;

    const ORDER_MAX = 10000000;

    /**
     * @column label_id
     */
    private $labelId;

    /**
     * @column display_order
     */
    private $displayOrder;

    /**
     * @no_persistent
     */
    private $labels;

    /**
     * @no_persistent
     */
    private $everyLabels;

    /**
     * @no_persistent
     */
    private $trackbackCount;

    /**
     * @no_persistent
     */
    private $commentCount;

    /**
     * @no_persistent
     */
    private $everyCommentCount;

    public function getLabelId()
    {
        return $this->labelId;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }

    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
    public function setDisplayOrder($displayOrder)
    {
        if (((int)$displayOrder) >= LabeledEntry::ORDER_MAX) {
            return;
        }
        $this->displayOrder = $displayOrder;
    }

    public function setMaxDisplayOrder()
    {
        $this->displayOrder = LabeledEntry::ORDER_MAX;
    }

    public function getLabels()
    {
        return $this->labels;
    }
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    public function getEveryLabels()
    {
        return $this->everyLabels;
    }
    public function setEveryLabels($everyLabels)
    {
        $this->everyLabels = $everyLabels;
    }

    public function getTrackbackCount()
    {
        return $this->trackbackCount;
    }
    public function setTrackbackCount($trackbackCount)
    {
        $this->trackbackCount = $trackbackCount;
    }

    public function getCommentCount()
    {
        return $this->commentCount;
    }
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    }

    public function getEveryCommentCount()
    {
        return $this->everyCommentCount;
    }
    public function setEveryCommentCount($everyCommentCount)
    {
        $this->everyCommentCount = $everyCommentCount;
    }
}
