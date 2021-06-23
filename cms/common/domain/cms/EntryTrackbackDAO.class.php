<?php

/**
 * @entity cms.EntryTrackback
 */
abstract class EntryTrackbackDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(EntryTrackback $bean);
    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @query_type update
     * @columns certification
     * @query id = :id
     *
     */
    abstract public function setCertification($id, $certification);

    abstract public function getByEntryId($entryId);

    /**
     * @query certification = 1 AND entry_id = :entryId
     * @order #submitdate# DESC
     */
    abstract public function getCertificatedTrackbackByEntryId($entryId);

    abstract public function get();

    abstract public function deleteByEntryId($entryId);

    /**
     * @columns count(id) as count
     */
    public function getTrackbackCountByEntryId($entryId)
    {
        $this->setLimit(1);
        $result = $this->executeQuery($this->getQuery(), $this->getBinds());

        if (count($result)<1) {
            return 0;
        }

        return $result[0]["count"];
    }


    /**
     * @query certification = 1 AND entry_id = :entryId
     * @columns count(id) as count
     * @order #submitdate# DESC
     */
    public function getCertificatedTrackbackCountByEntryId($entryId)
    {
        $this->setLimit(1);
        $result = $this->executeQuery($this->getQuery(), $this->getBinds());

        if (count($result)<1) {
            return 0;
        }

        return $result[0]["count"];
    }
}
