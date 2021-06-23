<?php

/**
 * @entity cms.EntryLabel
 */
abstract class EntryLabelDAO extends SOY2DAO
{
    abstract public function insert(EntryLabel $bean);

    abstract public function update(EntryLabel $bean);
    abstract public function deleteByEntryId($entryId);
    abstract public function deleteByLabelId($labelId);

    /**
     * @query #labelId# = :labelId and #entryId# = :entryId
     */
    abstract public function deleteByParams($entryId, $labelId);

    /**
     * @index labelId
     */
    abstract public function getByEntryId($entryId);

    abstract public function getByLabelId($labelId);

    /**
     * @return column_count_id
     * @columns count(entry_id) as count_id
     * @query ##labelId## = :labelId
     */
    abstract public function countByLabelId($labelId);

    /**
     * @return object
     * @query #entryId# = :entryId AND #labelId# = :labelId
     */
    abstract public function getByEntryIdLabelId($entryId, $labelId);

    abstract public function get();

    /**
     * @query #labelId# = :labelId AND #entryId# =:entryId
     * @return object
     */
    abstract public function getByParam($labelId, $entryId);

    /**
     * @final
     */
    public function setByParams($entryId, $labelId, $displayOrder = null)
    {
        $obj = new EntryLabel();

        $obj->setEntryId($entryId);
        $obj->setLabelId($labelId);
        $obj->setMaxDisplayOrder();

        try {
            $currentObj = $this->getByParam($labelId, $entryId);
          //do noting
        } catch (Exception $e) {
            $this->insert($obj);
        }

        if ($displayOrder) {
            $this->updateDisplayOrder($entryId, $labelId, $displayOrder);
        }
    }

    /**
     * @distinct
     * @columns ##entryId#
     * @distinct
     * @query ##labelId## in (<?php implode(',',:labelids) ?>)
     * @group #entryId#
     * @having count(#entryId#) = <?php count(:labelids) ?>
     */
    public function getNarrowLabels($labelids)
    {
        $tmpQuery = array();
        $binds = array();

        $query = $this->getQuery();

        try {
            $result = $this->executeQuery($query, $binds);
        } catch (Exception $e) {
            $result = array();
        }

        $entryIds = array();
        foreach ($result as $row) {
            $entryIds[] = $row["entry_id"];
        }

        if (empty($entryIds)) {
            return array();
        }

        $sql = "select distinct EntryLabel.label_id from EntryLabel inner join Label on EntryLabel.label_id = Label.id where EntryLabel.entry_id in (".implode(",", $entryIds).") order by Label.display_order";

        $result = $this->executeQuery($sql, $binds);

        $array = array();
        foreach ($result as $row) {
            $obj = $this->getObject($row);
            $array[$obj->getLabelId()] = $obj;
        }

        return $array;
    }

    /**
     * @query #labelId# = :labelId AND #entryId# =:entryId
     */
    abstract public function updateDisplayOrder($entryId, $labelId, $displayOrder);
}
