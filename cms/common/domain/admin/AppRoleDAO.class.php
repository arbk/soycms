<?php
/**
 * @entity admin.AppRole
 */
abstract class AppRoleDAO extends SOY2DAO
{
    abstract public function insert(AppRole $bean);

    abstract public function update(AppRole $bean);

    abstract public function delete($id);

    /**
     * @order #userId#,#appId#
     */
    abstract public function get();

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return object
     * @query ##appId## = :appId and ##userId## = :userId
     */
    abstract public function getRole($appId, $userId);

    /**
     * @index appId
     */
    abstract public function getByUserId($userId);

    /**
     * @index userId
     */
    abstract public function getByAppId($appId);

    abstract public function deleteByUserId($userId);

    abstract public function deleteByAppId($appId);
}
