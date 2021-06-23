<?php

/**
 * @entity admin.LoginErrorLog
 */
abstract class LoginErrorLogDAO extends SOY2DAO
{
    /**
     * @return id
     * @trigger onInsert
     */
    abstract public function insert(LoginErrorLog $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(LoginErrorLog $bean);

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return object
     */
    abstract public function getByIp($ip);

    abstract public function deleteByIp($ip);

    public function getCandidates($i)
    {
        $sql = "SELECT * FROM LoginErrorLog ".
        "WHERE count >= :cnt";

        $binds[":cnt"] = (int)$i;

        try {
            $res = $this->executeQuery($sql, $binds);
        } catch (Exception $e) {
            return array();
        }

        if (!count($res)) {
            return array();
        }

        $list = array();
        foreach ($res as $values) {
            $list[] = $this->getObject($values);
        }
        return $list;
    }

    public function hasErrorLogin($cnt = 10)
    {
        $sql = "SELECT * FROM LoginErrorLog ".
        "WHERE count >= ". $cnt . " ".
        "LIMIT 1";

        try {
            $res = $this->executeQuery($sql, array());
        } catch (Exception $e) {
            return false;
        }

        if (!count($res)) {
            return false;
        }

        /**
         * @ToDo　どれくらいの期間でログインを試みたかも見たい
         */
        return true;
    }

    //ログの削除:参照速度を上げるため
    public function clean($cnt)
    {
        //期限のハードコーディング
        $sql = "DELETE FROM LoginErrorLog ".
        "WHERE count <  ". $cnt . " ".
        "AND successed = 0 ".
        "AND update_date < " . strtotime("-1 month");

        try {
            $res = $this->executeUpdateQuery($sql, array());
        } catch (Exception $e) {
          //
        }
    }

    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[":startDate"] = SOYCMS_NOW;
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
