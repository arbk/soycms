<?php

/**
 * @table EntryLabel
 */
class EntryLabel
{
    const ORDER_MAX = 10000000;

    /**
     * @column entry_id
     */
    private $entryId;

    /**
     * @column label_id
     */
    private $labelId;

    /**
     * @column display_order
     */
    private $displayOrder;

    public function getEntryId()
    {
        return $this->entryId;
    }
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }
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
        if (((int)$displayOrder) >= EntryLabel::ORDER_MAX) {
            return;
        }
        $this->displayOrder = $displayOrder;
    }
    public function setMaxDisplayOrder()
    {
        $this->displayOrder = EntryLabel::ORDER_MAX;
    }
}
