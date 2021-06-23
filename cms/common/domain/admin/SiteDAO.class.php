<?php
/**
 * @entity admin.Site
 * @date 2007-08-22 18:42:19
 */
abstract class SiteDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(Site $bean);

    /**
     * @no_persistent #siteId#
     */
    abstract public function update(Site $bean);

    abstract public function delete($id);

    /**
     * @index id
     */
    abstract public function get();

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return object
     */
    abstract public function getBySiteId($siteId);

    /**
     * @index id
     * @column id,#siteId#
     */
    abstract public function getNameMap();

    /**
     * @columns isDomainRoot
     */
    abstract public function resetDomainRootSite($isDomainRoot = 0);

    /**
     * @columns isDomainRoot
     * @query id = :id
     */
    abstract public function updateDomainRootSite($id, $isDomainRoot = 1);

    /**
     * @query isDomainRoot = 1
     * @return object
     */
    abstract public function getDomainRootSite();

    /**
     * @index id
     */
    abstract public function getBySiteType($siteType);
}
