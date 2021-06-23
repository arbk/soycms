<?php
/**
 * @entity admin.Memo
 */
abstract class MemoDAO extends SOY2DAO
{
    /**
     * @trigger onInsert
     */
    abstract public function insert(Memo $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(Memo $bean);

    public function getLatestMemo()
    {
        try {
            $res = $this->executeQuery("SELECT * FROM Memo ORDER BY id DESC LIMIT 1");
        } catch (Exception $e) {
            $res = array();
        }
        return (isset($res[0])) ? $this->getObject($res[0]) : new Memo();
    }

    /**
     * @final
     */
    public function onInsert($query, $binds)
    {
        $binds[":createDate"] = time();
        $binds[":updateDate"] = time();
        return array($query, $binds);
    }

    /**
     * @final
     */
    public function onUpdate($query, $binds)
    {
        $binds[":updateDate"] = time();
        return array($query, $binds);
    }
}
