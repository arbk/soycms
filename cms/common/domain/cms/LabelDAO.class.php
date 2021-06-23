<?php

/**
 * @entity cms.Label
 */
abstract class LabelDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(Label $bean);

    /**
     * @trigger onUpdate
     */
    abstract public function update(Label $bean);

    /**
     * @trigger onDelete
     */
    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @query ##alias## LIKE :alias
     * @order id
     * @return object
     */
    abstract public function getByAlias($alias);

    /**
     * @index id
     * @order #displayOrder#, id
     */
    abstract public function get();

    /**
     * @return column_label_count
     * @columns count(id) as label_count
     */
    abstract public function countLabel();

    /**
     * @return object
     */
    abstract public function getByCaption($caption);

    /**
     * @final
     */
    public function onDelete($query, $binds)
    {
        $dao = SOY2DAOFactory::create("cms.EntryLabelDAO");
        $dao->deleteByLabelId($binds[":id"]);

        return array($query,$binds);
    }

    /**
     * @final
     */
    public function onUpdate($query, $binds)
    {
        if (!isset($binds[":displayOrder"]) || !strlen($binds[":displayOrder"])) {
            $binds[":displayOrder"] = Label::ORDER_MAX;
        }
        return array($query,$binds);
    }

    /**
     * @final
     */
    public function getEntryCount($id)
    {
        static $ids;
        if ((null===$ids)) {
            $ids = array();
        }
        if (isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PATH_INFO"], "Entry/List") !== false) {
            $v = trim(substr($_SERVER["PATH_INFO"], strpos($_SERVER["PATH_INFO"], "/Entry/List/") + 12), "/");
            $values = explode("/", $v);
            if (count($values)) {
                foreach ($values as $v) {
                    if (!is_numeric($v)) {
                        continue;
                    }
                    $ids[] = (int)$v;
                }
            }
        }
        if (!count($ids)) {
            try {
                $res = $this->executeQuery("SELECT COUNT(entry_id) AS COUNT FROM EntryLabel WHERE label_id = :id", array(":id" => $id));
                return (isset($res[0]["COUNT"])) ? (int)$res[0]["COUNT"] : 0;
            } catch (Exception $e) {
                return 0;
            }
        } else {
            //URLの末尾に数字が有る場合
            $labelIds = array_merge($ids, array($id));
            try {
                $results = $this->executeQuery("SELECT * FROM EntryLabel WHERE label_id IN (" . implode(",", $labelIds) . ") ");
            } catch (Exception $e) {
                return 0;
            }

            if (!count($results)) {
                return 0;
            }

            //ラベル毎にentry_idを集めてみる
            $list = array();
            foreach ($results as $res) {
                $list[(int)$res["label_id"]][] = $res["entry_id"];
            }

            //共通項目をピックアップする配列
            $array = array();
            $labelCnt = count($labelIds);
            for ($i = 0; $i < $labelCnt; ++$i) {
                if (!isset($list[$labelIds[$i]]) || !is_array($list[$labelIds[$i]])) {
                    continue;
                }
                if (!count($array)) {
                    $array = $list[$labelIds[$i]];
                } else {
                    $array = array_intersect($array, $list[$labelIds[$i]]);
                }
            }

            return count($array);
        }
    }

    /**
     * @columns #displayOrder#
     * @query #id# = :id
     */
    abstract public function updateDisplayOrder($id, $displayOrder);
}
