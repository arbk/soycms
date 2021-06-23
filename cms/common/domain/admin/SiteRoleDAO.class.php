<?php
/**
 * @entity admin.SiteRole
 * @date 2007-08-22 18:42:19
 */
abstract class SiteRoleDAO extends SOY2DAO
{
    abstract public function insert(SiteRole $bean);

    abstract public function update(SiteRole $bean);

    abstract public function delete($id);

    /**
     * @order #userId#,#siteId#
     */
    abstract public function get();

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return object
     * @query ##siteId## = :siteId and ##userId## = :userId
     */
    abstract public function getSiteRole($siteId, $userId);

    abstract public function getByUserId($userId);

    abstract public function getBySiteId($siteId);

    abstract public function deleteByUserId($userId);

    abstract public function deleteBySiteId($siteId);

    /**
     * @query ##siteId## = :siteId and ##userId## = :userId
     */
    abstract public function deleteSiteRole($userId, $siteId);
}
