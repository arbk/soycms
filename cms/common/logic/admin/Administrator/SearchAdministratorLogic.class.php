<?php

class SearchAdministratorLogic extends SOY2LogicBase
{
    private $limit;
    private $offset;
    private $order;
    private $where = array();
    private $binds = array();

    public function __construct()
    {
    }

    public function setSearchCondition($search)
    {
        if (!is_array($search) || !count($search)) {
            return;
        }

        foreach ($search as $key => $cnd) {
            $this->where[$key] = $key . " LIKE :" . $key;
            $this->binds[":" . $key] = "%" . $cnd . "%";
        }
    }

    public function get()
    {
        $dao = SOY2DAOFactory::create("admin.AdministratorDAO");
        $sql = "SELECT * FROM Administrator ";

        if (count($this->where)) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }

        $sql .= " LIMIT " . $this->limit;
        $sql .= " OFFSET " . $this->offset;

        try {
            $res = $dao->executeQuery($sql, $this->binds);
        } catch (Exception $e) {
            $res = array();
        }

        if (!count($res)) {
            return array();
        }

        $admins = array();
        foreach ($res as $v) {
            $admin = $dao->getObject($v);
            $admin->sites = array();    //サイトに関しては空の配列を入れておく
            $admins[] = $admin;
        }

        return $admins;
    }

    public function total()
    {
        $dao = SOY2DAOFactory::create("admin.AdministratorDAO");
        $sql = "SELECT COUNT(id) AS COUNT FROM Administrator ";

        if (count($this->where)) {
            $sql .= " WHERE " . implode(" AND ", $this->where);
        }

        try {
            $res = $dao->executeQuery($sql, $this->binds);
        } catch (Exception $e) {
            $res = array();
        }

        return (isset($res[0]["COUNT"])) ? (int)$res[0]["COUNT"] : 0;
    }

    public function getLimit()
    {
        return $this->limit;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function getOffset()
    {
        return $this->offset;
    }
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
    public function getOrder()
    {
        return $this->order;
    }
}
