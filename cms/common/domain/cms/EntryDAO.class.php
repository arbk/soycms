<?php

/**
 * @entity cms.Entry
 */
abstract class EntryDAO extends SOY2DAO
{
    const DATE_MIN = 0;
    const DATE_MAX = 2147483647;

    /**
     * @return id
     * @trigger onUpdate
     */
    abstract public function insert(Entry $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(Entry $bean);

    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return row
     * @columns *
     */
    abstract public function getArrayById($id);

    /**
     * @columns alias
     * @query ##id## = :id
     * @return object
     */
    abstract public function getByIdOnlyAlias($id);

    /**
     * @query ##alias## LIKE :alias
     * @order id
     * @return object
     */
    abstract public function getByAlias($alias);

    /**
     * @columns id
     * @query ##alias## LIKE :alias
     * @order id
     * @return object
     */
    abstract public function getByAliasOnlyId($alias);

    abstract public function getsByTitle($title);

    /**
     * @query ##id## = :id AND Entry.isPublished = 1 AND (openPeriodEnd > :now AND openPeriodStart <= :now)
     * @return object
     */
    abstract public function getOpenEntryById($id, $now);

    /**
     * @query ##alias## LIKE :alias AND Entry.isPublished = 1 AND (openPeriodEnd > :now AND openPeriodStart <= :now)
     * @order id
     * @return object
     */
    abstract public function getOpenEntryByAlias($alias, $now);

    /**
     * @order id desc
     */
    abstract public function get();

    /**
     * @columns id
     * @order id desc
     */
    abstract public function getOnlyId();

    public function setPublish($id, $publish)
    {
        $entity = $this->getById($id);
        $entity->setIsPublished($publish);
        return $this->update($entity);
    }

    /**
     * @final
     */
    public function onUpdate($query, $binds)
    {
//      $i = 0;
//
//      //記事表示の高速化
//      for(;;){
//        try{
//          $res = $this->executeQuery("SELECT id FROM Entry WHERE cdate = :cdate LIMIT 1;", array(":cdate" => $binds[":cdate"] + $i));
//        }catch(Exception $e){
//          $res = array();
//        }
//
//        if(!count($res)) break;
//        $i++;
//      }
//      $binds[":cdate"] += $i;

        if (!isset($binds[':author'])) {
            if (!class_exists("UserInfoUtil")) {
                //プラグインによっては読み込まれていないことがある
                SOY2::import("util.UserInfoUtil");
            }
            $binds[':author'] = UserInfoUtil::getUserName();
        }
        if (!isset($binds[':udate'])) {
            $binds[':udate'] = SOYCMS_NOW;
        }

        if (!isset($binds[":openPeriodStart"])) {
            $binds[":openPeriodStart"] = self::DATE_MIN;
        }
        if (!isset($binds[":openPeriodEnd"])) {
            $binds[":openPeriodEnd"] = self::DATE_MAX;
        }

        return array($query,$binds);
    }

    /**
     * 最新エントリーを取得
     * @order udate desc, id desc
     */
    abstract public function getRecentEntries();

    /**
     * 公開中かつ公開期間内の記事で最も早く公開期間外になる記事
     * @columns min(openPeriodEnd) as openPeriodEndMin
     * @query Entry.isPublished = 1 AND (openPeriodEnd > :now AND openPeriodStart <= :now)
     * @return column_openPeriodEndMin
     */
    abstract public function getNearestClosingEntry($now);

    /**
     * 公開中かつ公開期間外の記事で最も早く公開期間内になる記事
     * @columns min(openPeriodStart) as openPeriodStartMin
     * @query Entry.isPublished = 1 AND (openPeriodStart > :now)
     * @return column_openPeriodStartMin
     */
    abstract public function getNearestOpeningEntry($now);
}
