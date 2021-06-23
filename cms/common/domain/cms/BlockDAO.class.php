<?php

/**
 * @entity cms.Block
 */
abstract class BlockDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(Block $bean);

    /**
     * @no_persistent #pageId#
     */
    abstract public function update(Block $bean);

    /**
     * @columns id,object
     */
    abstract public function updateObject(Block $bean);

    abstract public function delete($id);

    abstract public function deleteByPageId($pageId);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function get();

    abstract public function getByPageId($pageId);

    /**
     * @query page_id = :pageId and soy_id = :soyId
     * @return object
     */
    abstract public function getPageBlock($pageId, $soyId);
}
