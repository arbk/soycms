<?php

class SearchAction extends SOY2Action
{
    private $limit;
    private $offset;

    /**
     * 取得しない列項目
     */
    private $ignoreColumns;

    private $totalCount;

    public function getIgnoreColumns()
    {
        return $this->ignoreColumns;
    }
    public function setIgnoreColumns($ignoreColumns)
    {
        $this->ignoreColumns = $ignoreColumns;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function execute($request, $form, $response)
    {
        $this->limit = (is_numeric($form->getLimit()) ? $form->getLimit() : SOYCMS_INI_NUMOF_ENTRY);
        $this->offset = (is_numeric($form->getOffset()) ? $form->getOffset() : 0);

        $entries = $this->searchEntries($form->getFreeword_text(), array(
        "op"=>$form->getLabelOperator(),
        "labels"=>$form->getLabel()
        ));

        $count = $this->totalCount;

        $this->setAttribute("from", $this->offset);

        if (count($entries) < $this->limit) {
            $this->setAttribute("to", $this->offset + count($entries));
        } else {
            $this->setAttribute("to", $this->offset + $this->limit);
        }

        $this->setAttribute("Entities", $entries);
        $this->setAttribute("total", $this->totalCount);
        $this->setAttribute("limit", $this->limit);

        $this->setAttribute("form", $form);
    }

    private function searchEntries($freewordText, $label, $others = null)
    {
        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic", array("offset" => $this->offset, "limit" => $this->limit));
        $logic->setIgnoreColumns($this->ignoreColumns);

        $dao = SOY2DAOFactory::create("LabeledEntryDAO");
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

        $query = new SOY2DAO_Query();
        $query->prefix = "select";
        $query->distinct = true;
        $query->sql = "id,alias,title,content,more,cdate,udate,openPeriodStart,openPeriodEnd,isPublished";
        $query->quoteSql();
        $query->table = "Entry left outer join EntryLabel on(Entry.id = EntryLabel.entry_id)";
        $query->order = "Entry.udate desc";
        $binds = array();
        $where = array();

        // フリーワード検索を作成
        $keywords = preg_split('/[\s,]+/', $freewordText, null, PREG_SPLIT_NO_EMPTY);
        if (!empty($keywords)) {
            $keywordCounts = 0;
            $freewordIgnoreQuery = array();
            $freewordQuery = array();

            foreach (array("title", "content", "more") as $column) {
                $freewordIgnore = array();
                $freeword = array();
                foreach ($keywords as $keyword) {
                    $bind_key = ':freeword' . $keywordCounts;
                    if ($keyword[0] == "-") {
                        $keyword = substr($keyword, 1);
                        $freewordIgnore[] = 'Entry.' . $column . " not like " . $bind_key . "";
                    } else {
                        $freeword[] = 'Entry.' . $column . " like " . $bind_key . "";
                    }
                    $binds[$bind_key] = '%' . $keyword . '%';
                    $keywordCounts++;
                }
                !empty($freewordIgnore) && $freewordIgnoreQuery[] = "(" . implode(' OR ', $freewordIgnore) . ")";
                !empty($freeword) && $freewordQuery[] = "(" . implode(' AND ', $freeword) . ")";
            }
            !empty($freewordIgnoreQuery) && $where[] = " ( " . implode(' AND ', $freewordIgnoreQuery) . " ) ";
            !empty($freewordQuery) && $where[] = " ( " . implode(' OR ', $freewordQuery) . " ) ";
        }

        // 記事管理者に見えないラベルの付いた記事は除外する
        $prohibitedLabelIds = array();
        if (!UserInfoUtil::hasSiteAdminRole()) {
            $labelLogic = SOY2LogicContainer::get("logic.site.Label.LabelLogic");
            $prohibitedLabelIds = $labelLogic->getProhibitedLabelIds();

            $labelQuery = new SOY2DAO_Query();
            $labelQuery->prefix = "select";
            $labelQuery->sql = "EntryLabel.entry_id";
            $labelQuery->table = "EntryLabel";
            $labelQuery->distinct = true;
            if (count($prohibitedLabelIds)) {
                $labelQuery->where = 'EntryLabel.label_id IN (' . implode(",", $prohibitedLabelIds) . ')';
            }
            $where[] = 'Entry.id NOT IN (' . $labelQuery . ')';
        }

        // ラベル絞込みを作成
        if (count($label["labels"])) {
            // int化
            $label["labels"] = array_map(function ($v) {
                return (int)$v;
            }, $label["labels"]);

            $labelQuery = new SOY2DAO_Query();
            $labelQuery->prefix = "select";
            $labelQuery->sql = "EntryLabel.entry_id";
            $labelQuery->table = "EntryLabel";
            $labelQuery->distinct = true;
            $labelQuery->where = 'EntryLabel.label_id IN (' . implode(",", $label["labels"]) . ')';

            if ($label["op"] == "AND" && count($label["labels"])) {
                $labelQuery->having = "count(EntryLabel.entry_id) = " . count($label["labels"]);
                $labelQuery->group = "EntryLabel.entry_id";
            }

            $where[] = 'Entry.id IN (' . $labelQuery . ')';
        }

        $query->where = implode(" AND ", $where);
        try {
            $results = $dao->executeQuery($query, $binds);
        } catch (Exception $e) {
//      var_dump($e);
            error_log($e->getMessage());
            $results = array();
        }

        $this->totalCount = $dao->getRowCount();

        $ret_val = array();
        if (count($results)) {
            foreach ($results as $row) {
                $obj = $dao->getObject($row);
                $obj->setLabels($logic->getLabelIdsByEntryId($obj->getId()));
                $ret_val[] = $obj;
            }
        }
        return $ret_val;
    }
}

class SearchActionForm extends SOY2ActionForm
{
    private $freeword_text;
    private $label;
    private $limit;
    private $offset;

    private $labelOperator;

    public function getLabel()
    {
        if (!isset($_GET["label"]) && isset($_GET["labelOperator"])) {
            $this->label = array();
        } elseif (!count($this->label) && isset($_COOKIE["ENTRY_SEARCH_LABELS"])) {
            $this->label = soy2_unserialize($_COOKIE["ENTRY_SEARCH_LABELS"]);
            if (!is_array($this->label)) {
                $this->label = array();
            }
        }
        return $this->label;
    }
    public function setLabel($label)
    {
        if (isset($_GET["label"]) && is_array($_GET["label"]) && count($_GET["label"])) {
            soy2_setcookie("ENTRY_SEARCH_LABELS", soy2_serialize($_GET["label"]));
        } elseif (isset($_GET["labelOperator"])) {    //ラベルオペレータは検索ボタンを押したら必ずあるので、この値を検索の有無の判定として利用する
            soy2_setcookie("ENTRY_SEARCH_LABELS");  //一度ラベル付き検索した後にラベルを外す処理
        }

        if (!is_array($label)) {
            $label = array();
        }
        $this->label = $label;
    }

    public function getFreeword_text()
    {
        if (null===$this->freeword_text && isset($_COOKIE["FREEWORD_TEXT"])) {
            $this->freeword_text = $_COOKIE["FREEWORD_TEXT"];
        }
        return $this->freeword_text;
    }
    public function setFreeword_text($text)
    {
        if (isset($_GET["freeword_text"])) {
            soy2_setcookie("FREEWORD_TEXT", $_GET["freeword_text"]);
        }

        if (null===$text && isset($_COOKIE["FREEWORD_TEXT"])) {
            $text = $_COOKIE["FREEWORD_TEXT"];
        }

        $this->freeword_text = $text;
    }

    public function getLabelOperator()
    {
        if (null===$this->labelOperator && isset($_COOKIE["LABEL_OPERATOR"])) {
            $this->labelOperator = $_COOKIE["LABEL_OPERATOR"];
        }
        return $this->labelOperator;
    }
    public function setLabelOperator($op)
    {
        if (isset($_GET["labelOperator"])) {
            soy2_setcookie("LABEL_OPERATOR", $_GET["labelOperator"]);
        }
        $this->labelOperator = $op;
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
}
