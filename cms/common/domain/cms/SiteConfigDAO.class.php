<?php

/**
 * @entity cms.SiteConfig
 */
abstract class SiteConfigDAO extends SOY2DAO
{
    abstract public function insert(SiteConfig $bean);

    /**
     * @trigger syncSiteName
     */
    abstract public function update(SiteConfig $bean);

    public function updateSiteConfig(SiteConfig $bean)
    {
        $this->executeUpdateQuery(
            "update SiteConfig set siteConfig = :siteConfig",
            array(
            ":siteConfig"=>$bean->getSiteConfig()
            )
        );
    }

    /**
     * @final
     */
    public function notifyUpdate()
    {
        $bean = $this->get();
        $bean->notifyUpdate();
        return $this->update($bean);
    }

    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function get();

    /**
     * @final
     */
    public function syncSiteName($query, $bind)
    {
        try {
            $siteName = $bind[":name"];

            $oldDsn = SOY2DAOConfig::Dsn();
            $oldUser = SOY2DAOConfig::user();
            $oldPass = SOY2DAOConfig::pass();

//          if(defined("SOYCMS_ASP_MODE")){
//              SOY2DAOConfig::Dsn(SOYCMS_ASP_DSN);
//              SOY2DAOConfig::user(SOYCMS_ASP_USER);
//              SOY2DAOConfig::pass(SOYCMS_ASP_PASS);
//          }else{
                SOY2DAOConfig::Dsn(ADMIN_DB_DSN);
//          }

            $dao = SOY2DAOFactory::create("admin.SiteDAO");
            $site = $dao->getById(UserInfoUtil::getSite()->getId());
            $site->setSiteName($siteName);
            $dao->update($site);

            $con = &$dao->getDataSource();
            $con = null;
        } catch (Exception $e) {
        }
        SOY2DAOConfig::Dsn($oldDsn);
        SOY2DAOConfig::user($oldUser);
        SOY2DAOConfig::pass($oldPass);

        return array($query, $bind);
    }
}
