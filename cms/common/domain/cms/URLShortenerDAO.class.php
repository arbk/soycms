<?php

/**
 * @entity cms.URLShortener
 */
abstract class URLShortenerDAO extends SOY2DAO
{
    /**
     * @return id
     * @trigger onInsert
     */
    abstract public function insert(URLShortener $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(URLShortener $bean);

    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function get();

    /**
     * @return object
     */
    abstract public function getByFrom($from);

    /**
     * @return object
     * @query #targetType# = :targetType AND #targetId# = :targetId
     */
    abstract public function getByTargetTypeANDTargetId($targetType, $targetId);


    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[':cdate'] = SOYCMS_NOW;
        $binds[':udate'] = SOYCMS_NOW;
        return array($query,$binds);
    }

    /**
     * @final
     */
    public function onUpdate($query, $binds)
    {
        $binds[':udate'] = SOYCMS_NOW;
        return array($query,$binds);
    }
}
