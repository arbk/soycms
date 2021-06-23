<?php

class PagerLogic
{
    private $pageURL;
    private $query;
    private $queryString;

    private $page = 1;
    private $start;
    private $end;
    private $total;
    private $limit = 15;

    public function __construct()
    {
    }

    public function setPageURL($value)
    {
        $this->pageURL = SOY2PageController::createLink($value);
    }
    public function getQuery()
    {
        return $this->query;
    }
    public function setQuery($query)
    {
        $this->query = $query;
        $this->queryString = (count($query) > 0) ? "?".http_build_query($query) : "" ;
    }
    public function getQueryString()
    {
        return $this->queryString;
    }
    public function setPage($value)
    {
        $this->page = $value;
    }
    public function setStart($value)
    {
        $this->start = $value;
    }
    public function setEnd($value)
    {
        $this->end = $value;
    }
    public function setTotal($value)
    {
        $this->total = $value;
    }
    public function setLimit($value)
    {
        $this->limit = $value;
    }

    public function getCurrentPageURL()
    {
        return $this->pageURL."/".$this->page;
    }
    public function getPageURL()
    {
        return $this->pageURL;
    }
    public function getPage()
    {
        return $this->page;
    }
    public function getStart()
    {
        return $this->start;
    }
    public function getEnd()
    {
        return $this->end;
    }
    public function getTotal()
    {
        return $this->total;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getOffset()
    {
        return ($this->page - 1) * $this->limit;
        ;
    }

    public function getNextParam()
    {
        return array(
            "link" => ( ($this->total > $this->end) ? $this->pageURL . "/" . ($this->page + 1) : $this->pageURL . "/" . $this->page ) . $this->queryString,
            "class" => ($this->total <= $this->end) ? "pager_disable" : ""
        );
    }
    public function getPrevParam()
    {
        return array(
            "link" => ( ($this->page > 1) ? $this->pageURL . "/" . ($this->page - 1) : $this->pageURL . "/" . ($this->page) ) . $this->queryString,
            "class" => ($this->page <= 1) ? "pager_disable" : ""
        );
    }
    public function getPagerParam()
    {
        $pagers = range(
            max(1, $this->page - 4),
            max(1, min(ceil($this->total / $this->limit), max(1, $this->page - 4) +9))
        );

        return array(
            "url" => $this->pageURL,
            "queryString" => $this->queryString,
            "current" => $this->page,
            "list" => $pagers
        );
    }
    public function getSelectArray()
    {
        $pagers = range(
            1,
            ceil($this->total / $this->limit)
        );

        $array = array();
        foreach ($pagers as $page) {
            $array[ $this->pageURL."/".$page . $this->queryString ] = $page;
//          $array[ $page ] = $page;
        }

        return $array;
    }
}

class SimplePager extends HTMLList
{

    private $url;
    private $queryString;
    private $current;

    protected function populateItem($bean)
    {
        $this->createAdd("target_link", "HTMLLink", array(
            "text" => $bean,
            "link" => $this->url . "/" . $bean . $this->queryString,
            "class" => ($this->current == $bean) ? "pager_current" : ""
        ));
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }
    public function getCurrent()
    {
        return $this->current;
    }
    public function setCurrent($cuttent)
    {
        $this->current = $cuttent;
    }
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }
}
