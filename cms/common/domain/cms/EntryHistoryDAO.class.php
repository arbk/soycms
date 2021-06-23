<?php

/**
 * @entity cms.EntryHistory
 */
abstract class EntryHistoryDAO extends SOY2DAO
{
    /**
     * @trigger onInsert
     */
    abstract public function insert(EntryHistory $bean);

    abstract public function delete($id);

    abstract public function deleteByEntryId($entryId);

    /**
     * @order id desc
     */
    abstract public function getByEntryId($entryId);

    /**
     * @return column_count
     * @columns count(id) as count
     */
    abstract public function countByEntryId($entryId);

    /**
     * @return object
     * @order id desc
     */
    abstract public function getLatestByEntryId($entryId);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function get();

    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[':userId'] = UserInfoUtil::getUserId();
        $binds[':author'] = UserInfoUtil::getUserName();
        $binds[':cdate'] = SOYCMS_NOW;
        return array($query,$binds);
    }
}
