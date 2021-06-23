<?php

/**
 * @entity cms.EntryComment
 */
abstract class EntryCommentDAO extends SOY2DAO
{
    /**
     * @trigger setupComment
     */
    abstract public function insert(EntryComment $bean);

    abstract public function update(EntryComment $bean);

    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @order id
     */
    abstract public function getByEntryId($entryId);

    /**
     * @order id
     * @query ##isApproved## = 1 AND entry_id = :entryId
     */
    abstract public function getApprovedCommentByEntryId($entryId);

    abstract public function get();

    /**
     * @final
     */
    public function setupComment($query, $binds)
    {
        if ((null===$binds[':isApproved']) || !strlen($binds[':isApproved'])) {
            $binds[':isApproved'] = 0;
        }
        $binds[':submitDate'] = SOYCMS_NOW;
        return array($query, $binds);
    }

    /**
     * @query_type update
     * @columns ##isApproved##
     * @query id = :id
     */
    abstract public function setApproved($id, $isApproved);

    abstract public function deleteByEntryId($entryId);

    /**
     * @columns count(id) as count
     */
    public function getCommentCountByEntryId($entryId)
    {
        $this->setLimit(1);
        $result = $this->executeQuery($this->getQuery(), $this->getBinds());

        if (count($result) < 1) {
            return 0;
        }

        return $result[0]["count"];
    }

    /**
     * @query ##isApproved## = 1 AND entry_id = :entryId
     * @columns count(id) as count
     */
    public function getApprovedCommentCountByEntryId($entryId)
    {
        $this->setLimit(1);
        $result = $this->executeQuery($this->getQuery(), $this->getBinds());

        if (count($result) < 1) {
            return 0;
        }

        return $result[0]["count"];
    }
}
