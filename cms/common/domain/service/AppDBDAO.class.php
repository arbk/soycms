<?php

/**
 * @entity service.AppDB
 */
abstract class AppDBDAO extends SOY2DAO
{
    /**
     * @return id
     * @trigger onInsert
     */
    abstract public function insert(AppDB $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(AppDB $bean);

    abstract public function get();

    /**
     * @return object
     */
    abstract public function getByAccountId($accountId);

    /**
     * @return object
     */
    abstract public function getBySign($sign);

    abstract public function deleteById($id);

    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[":registerDate"] = SOYCMS_NOW;
        $binds[":updateDate"] = SOYCMS_NOW;

        return array($query, $binds);
    }

    /**
     * @final
     */
    public function onUpdate($query, $binds)
    {
        $binds[":updateDate"] = SOYCMS_NOW;

        return array($query, $binds);
    }
}
