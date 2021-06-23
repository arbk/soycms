<?php

class EntryLogic extends SOY2LogicBase
{
    private $offset;
    private $limit;

    private $ignoreColumns = null;  // データを取得しないカラム名を指定

    private $orderColumns = null;   // 並び順指定の優先順位: displayOrder > orderColumns > 既定順 (reverse の場合は逆順)
    private $reverse = false;       // reverse の指定は既定順にのみ有効
    private $blockClass;    //ブロックのクラス
    private $totalCount;

    private $entryDAO;
    private $entryLabelDAO;
    private $labeledEntryDAO;

    public function __construct()
    {
      /** @ToDo LabeledEntryで最新版のSQLiteに対応したい **/
        SOY2::import("logic.site.Entry.class.new.LabeledEntryDAO");
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @param array $ignoreColumns array("column_name", "column_name", ...)
     */
    public function setIgnoreColumns($ignoreColumns)
    {
        $this->ignoreColumns = $ignoreColumns;
    }

    /**
     * @param array $orderColumns array("column_name"=>"asc", "column_name"=>"desc", ...)
     */
    public function setOrderColumns($orderColumns)
    {
        $this->orderColumns = $orderColumns;
    }
    /**
     * @return NULL|string
     */
    public function buildOrderColumns()
    {
        $order = null;
        if (is_array($this->orderColumns) && 0 < count($this->orderColumns)) {
            $order = "";
            foreach ($this->orderColumns as $k => $v) {
                $lv = mb_strtolower($v);
                if (("" !== $v && "asc" !== $lv && "desc" !== $lv)
                || (1 === preg_match("/[^a-zA-Z0-9_]/", $k))) {
                    continue;
                }
                $order = $order . $k . " " . $v . ",";
            }
            $order = rtrim($order, ",");
        }
        return $order;
    }

    /**
     * @param boolean $reverse
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
    }

    public function setBlockClass($blockClass)
    {
        $this->blockClass = $blockClass;
    }

    /**
     * エントリーを新規作成
     */
    public function create($bean)
    {
        $dao = $this->entryDao();

        $bean->setContent($this->cleanupMCETags($bean->getContent()));
        $bean->setMore($this->cleanupMCETags($bean->getMore()));

        //数値以外（空文字列を含む）がcdateに入っていれば現在時刻を作成日時にする
        if (!is_numeric($bean->getCdate())) {
            $bean->setCdate(SOYCMS_NOW);
        }

        if (UserInfoUtil::hasEntryPublisherRole() != true) {
            $bean->setOpenPeriodEnd(CMSUtil::encodeDate(null, false));
            $bean->setOpenPeriodStart(CMSUtil::encodeDate(null, true));

            $bean->setIsPublished(false);
        }

        //仮で今の時間を入れておく カスタムエイリアス　SQLite対策
        if ($bean->getId() == $bean->getAlias()) {
            $bean->setAlias(SOYCMS_NOW);
        }
        $id = $dao->insert($bean);

        //if($bean->isEmptyAlias()){    //ここのif文は必要ない
            $bean->setId($id);//updateを実行するため
            $bean->setAlias($this->getUniqueAlias($id, $bean->getTitle()));
            $dao->update($bean);
        //}

        return $id;
    }

    /**
     * エントリーを更新
     */
    public function update($bean)
    {
        $dao = $this->entryDao();

        //数値以外（空文字列を含む）がcdateに入っていれば現在時刻を作成日時にする
        if (!is_numeric($bean->getCdate())) {
            $bean->setCdate(SOYCMS_NOW);
        }

        if ($bean->isEmptyAlias()) {
            $bean->setAlias($this->getUniqueAlias($bean->getId(), $bean->getTitle()));
        }

        $bean->setContent($this->cleanupMCETags($bean->getContent()));
        $bean->setMore($this->cleanupMCETags($bean->getMore()));

        if (UserInfoUtil::hasEntryPublisherRole() != true) {
            $old = $dao->getById($bean->getId());
            $bean->setOpenPeriodEnd(CMSUtil::encodeDate($old->getOpenPeriodEnd(), false));
            $bean->setOpenPeriodStart(CMSUtil::encodeDate($old->getOpenPeriodStart(), true));

            $bean->setIsPublished($old->getIsPublished());
        } else {
            $bean->setOpenPeriodEnd(CMSUtil::encodeDate($bean->getOpenPeriodEnd(), false));
            $bean->setOpenPeriodStart(CMSUtil::encodeDate($bean->getOpenPeriodStart(), true));
        }

        $dao->update($bean);

        return $bean->getId();
    }

    public function deleteByIds($ids)
    {
        $dao = $this->entryDao();
        $entryLabelDao = $this->entryLabelDao();
        $entryTrackbackDAO = SOY2DAOFactory::create("cms.EntryTrackbackDAO");
        $entryCommentDAO = SOY2DAOFactory::create("cms.EntryCommentDAO");
        $entryHistoryLogic = SOY2LogicContainer::get("logic.site.Entry.EntryHistoryLogic");

        try {
            $dao->begin();

            foreach ($ids as $id) {
                $dao->delete($id);
                $entryLabelDao->deleteByEntryId($id);
                $entryHistoryLogic->onRemove($id);

                //@TODO トラックバックとコメントは削除しない方がいい？
                $entryTrackbackDAO->deleteByEntryId($id);
                $entryCommentDAO->deleteByEntryId($id);
            }
            $dao->commit();
            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $dao->rollback();
            return false;
        }
    }

    /**
     * エントリーを1件取得
     * 2008-10-29 内部使用のため、無限遠時刻の変換処理の追加
     */
    public function getById($id, $flag = true)
    {
        $dao = $this->entryDao();
        $dao->setIgnoreColumns($this->ignoreColumns);
        $entry = $dao->getById($id);

        //無限遠時刻をnullになおす
        if ($flag) {
            $entry->setOpenPeriodEnd(CMSUtil::decodeDate($entry->getOpenPeriodEnd()));
            $entry->setOpenPeriodStart(CMSUtil::decodeDate($entry->getOpenPeriodStart()));
        }

        $entry->setLabels($this->getLabelIdsByEntryId($entry->getId()));

        return $entry;
    }

    /**
     * 全て返す
     */
    public function get()
    {
        $dao = $this->entryDao();

        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);
        $array = $dao->get();
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return $array;
    }

    /**
     * ラベルの割り当てられたエントリーを全て返す
     *
     * 2007/12/21 getByLabelIdsのエイリアスとして定義
     */
    public function getByLabelId($labelid)
    {
        return $this->getByLabelIds(array($labelid));
    }

    /**
     * 非公開のエントリーを取得
     */
    public function getClosedEntryList()
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

        $array = $dao->getClosedEntries();
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return $array;
    }

    /**
     * 公開期間外のエントリー一覧を取得
     */
    public function getOutOfDateEntryList()
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

        $array = $dao->getOutOfDateEntries(SOYCMS_NOW);
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return $array;
    }

    /**
     * ラベルのついていないエントリー一覧を取得
     */
    public function getNoLabelEntryList()
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

        $array = $dao->getNoLabelEntries();
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return $array;
    }

    /**
     * ラベルを複数指定してエントリーをすべて取得
     */
    public function getByLabelIds($labelIds, $flag = true, $start = null, $end = null)
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);

//      $array = $dao->getByLabelIdsOnlyId($labelIds, $this->reverse, $this->limit, $this->offset);
//      $this->totalCount = $dao->countByLabelIdsOnlyId($labelIds);

        // エントリーのidを取得 (columns: Entry.id, EntryLabel.display_order, Entry.cdate)
        // - ここでは id+αのみ を取得するので ignoreColumns を指定しない.
        $array = $dao->getByLabelIdsOnlyId($labelIds, $this->reverse, $this->buildOrderColumns());
        $this->totalCount = $dao->getRowCount();

        foreach ($array as $key => $entry) {
            // エントリーを取得 - $this->getById() にて ignoreColumns が設定される.
            $array[$key] = $this->getById($key, false);
            // ラベルを取得
//          $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId())); // ラベルは $this->getById() で設定済
        }

        return $array;
    }

    /**
     * エントリーに割り当てているラベルIDを全て取得
     */
    public function getLabelIdsByEntryId($entryId)
    {
        $dao = $this->entryLabelDao();

        $entryLabels = $dao->getByEntryId($entryId);
        $result = array();
        foreach ($entryLabels as $obj) {
            $result[] = $obj->getLabelId();
        }

        return $result;
    }

    public function getLabeledEntryByEntryId($entryId)
    {
        return $this->entryLabelDao()->getByEntryId($entryId);
    }

    /**
     * 合計件数を返す
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * エントリーにラベルを割り当てる
     */
    public function setEntryLabel($entryId, $labelId)
    {
        $this->entryLabelDao()->setByParams($entryId, $labelId);
    }

    /**
     * エントリーについているラベルを全て削除
     */
    public function clearEntryLabel($entryId)
    {
        $this->entryLabelDao()->deleteByEntryId($entryId);
    }

    /**
     * エントリーからラベルを削除
     */
    public function unsetEntryLabel($entryId, $labelId)
    {
        $this->entryLabelDao()->deleteByParams($entryId, $labelId);
    }


    /**
     * 表示順の更新
     */
    public function updateDisplayOrder($entryId, $labelId, $displayOrder)
    {
        $dao = $this->entryLabelDao();
        $dao->deleteByParams($entryId, $labelId);
        $dao->setByParams($entryId, $labelId, $displayOrder);
    }

    /**
     * ラベルとエントリーに対応する表示順を返す
     */
    public function getDisplayOrder($entryId, $labelId)
    {
        try {
            return $this->entryLabelDao()->getByEntryIdLabelId($entryId, $labelId)->getDisplayOrder();
        } catch (Exception $e) {
//          error_log($e->getMessage());
            return null;
        }
    }

    /**
     * 表示期間を含めたラベル付けされたエントリーを取得
     */
    public function getOpenEntryByLabelId($labelId)
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

//      //仕様変更により、記事取得関数実行時に念の為にlimitとoffsetを渡しておく
//      $array = $dao->getOpenEntryByLabelId($labelId,SOYCMS_NOW,$this->reverse, $this->limit, $this->offset);

        $array = $dao->getOpenEntryByLabelId($labelId, SOYCMS_NOW, $this->reverse, $this->buildOrderColumns());
        $this->totalCount = $dao->getRowCount();
        return $array;
    }

    /**
     * 表示期間を含めてラベル付けされたエントリーを取得（ラベルIDを複数指定）
     */
    public function getOpenEntryByLabelIds($labelIds, $isAnd = true, $start = null, $end = null)
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);

        if ($isAnd) {
            // $labelIdsのラベルがすべて設定されている記事のみ取得
//          $array = $dao->getOpenEntryByLabelIds($labelIds,SOYCMS_NOW,$start,$end,$this->reverse, $this->limit, $this->offset);
//          $this->totalCount = $dao->countOpenEntryByLabelIds($labelIds, SOYCMS_NOW, $isAnd, $start, $end);

            $array = $dao->getOpenEntryByLabelIds($labelIds, SOYCMS_NOW, $start, $end, $this->reverse, $this->buildOrderColumns());
        } else {
            // $labelIdsのラベルがどれか１つでも設定されている記事を取得
//          $array = $dao->getOpenEntryByLabelIdsImplements($labelIds,SOYCMS_NOW,false,$start,$end,$this->reverse, $this->limit, $this->offset);
//          $this->totalCount = $dao->countOpenEntryByLabelIds($labelIds, SOYCMS_NOW, $isAnd, $start, $end);

            $array = $dao->getOpenEntryByLabelIdsImplements($labelIds, SOYCMS_NOW, false, $start, $end, $this->reverse, $this->buildOrderColumns());
        }
        foreach ($array as $key => $entry) {
            $array[$key]->setCommentCount($this->getApprovedCommentCountByEntryId($entry->getId()));
            $array[$key]->setEveryCommentCount($this->getCommentCount($entry->getId()));
            $array[$key]->setTrackbackCount($this->getCertificatedTrackbackCountByEntryId($entry->getId()));
        }
        $this->totalCount = $dao->getRowCount();
        return $array;
    }

    /**
     * ブログのエントリーを取得
     */
    public function getBlogEntry($blogLabelId, $entryId)
    {
        $dao = $this->entryDao();
        $dao->setIgnoreColumns($this->ignoreColumns);

        try {
//          if(defined("CMS_PREVIEW_ALL")){
//            if(is_numeric($entryId)){
//              try{
//                $entry = $dao->getById($entryId);
//              }catch(Exception $e){
//                $entry = $dao->getByAlias($entryId);
//              }
//            }else{
//              $entry = $dao->getByAlias($entryId);
//            }
//          }else{
            if (is_numeric($entryId)) {
                try {
                    $entry = $dao->getOpenEntryById($entryId, SOYCMS_NOW);
                } catch (Exception $e) {
                    //記事IDで取得できなければ、エイリアスの方でも取得を試みる
                    $entry = $dao->getOpenEntryByAlias($entryId, SOYCMS_NOW);
                }
            } else {
                $entry = $dao->getOpenEntryByAlias($entryId, SOYCMS_NOW);
            }
//          }

            $entry = SOY2::cast("LabeledEntry", $entry);

            //ブログに所属しているエントリーかどうかチェックする
            $labelIds = $this->getLabelIdsByEntryId($entry->getId());
            if (!in_array($blogLabelId, $labelIds)) {
                throw new Exception("This entry (id: {$entryId}) does not belong to the designated blog (label: {$blogLabelId}).");
            }
        } catch (Exception $e) {
            //該当エントリーが見つからない場合は
            throw $e;
        }

        return $entry;
    }

    public function getBlogEntryWithoutExecption($blogLabelId, $entryId)
    {
        $dao = $this->entryDao();
        if (is_numeric($entryId)) {
            try {
                return $dao->getOpenEntryById($entryId, SOYCMS_NOW);
            } catch (Exception $e) {
                //記事IDで取得できなければ、エイリアスの方でも取得を試みる
                try {
                    return $dao->getOpenEntryByAlias($entryId, SOYCMS_NOW);
                } catch (Exception $e) {
                    //
                }
            }
        } else {
            try {
                return $dao->getOpenEntryByAlias($entryId, SOYCMS_NOW);
            } catch (Exception $e) {
                //
            }
        }
        return new Entry();
    }

    /**
     * 作成日順で次のエントリーを取得
     */
    public function getNextOpenEntry($blogLabelId, $entry)
    {
        return $this->getNeighborOpenEntry($blogLabelId, $entry, true, LabeledEntryDAO::ORDER_TYPE_CDT);
    }

    /**
     * 作成日順で前のエントリーを取得
     */
    public function getPrevOpenEntry($blogLabelId, $entry)
    {
        return $this->getNeighborOpenEntry($blogLabelId, $entry, false, LabeledEntryDAO::ORDER_TYPE_CDT);
    }

    /**
     * タイトル順で次のエントリーを取得
     */
    public function getNextOpenEntrySortTitle($blogLabelId, $entry)
    {
        return $this->getNeighborOpenEntry($blogLabelId, $entry, true, LabeledEntryDAO::ORDER_TYPE_TTL);
    }

    /**
     * タイトル順で前のエントリーを取得
     */
    public function getPrevOpenEntrySortTitle($blogLabelId, $entry)
    {
        return $this->getNeighborOpenEntry($blogLabelId, $entry, false, LabeledEntryDAO::ORDER_TYPE_TTL);
    }

    /**
     * 次／前のエントリーを取得
     */
    private function getNeighborOpenEntry($blogLabelId, $entry, $next, $type)
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit(1);
        $dao->setIgnoreColumns($this->ignoreColumns);

        try {
            $neighbor = $dao->getNeighborOpenEntry($blogLabelId, $entry, SOYCMS_NOW, ($next xor $this->reverse), $type);
        } catch (Exception $e) {
//          error_log($e->getMessage());
            return new LabeledEntry();
        }
        return $neighbor;
    }

    /**
     * 指定されたIDの公開状態をpublicに変更
     */
    public function setPublish($id, $publish)
    {
        $dao = $this->entryDao();
        if (is_array($id)) {
            //配列だったらそれぞれを設定
            try {
                $dao->begin();
                foreach ($id as $pId) {
                    $dao->setPublish($pId, $publish);
                }
                $dao->commit();
                return true;
            } catch (Exception $e) {
                error_log($e->getMessage());
                $dao->rollback();
                return false;
            }
        } else {
            //IDだったらそれを設定
            try {
                $dao->setPublish($id, $publish);
                return true;
            } catch (Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }
    }

    /**
     * 月別アーカイブを数える
     */
    public function getCountMonth($labelIds)
    {
        return $this->labeledEntryDao()->getCountMonth($labelIds);
    }

    public function getMonth($labelIds)
    {
        return $this->labeledEntryDao()->getMonth($labelIds);
    }

    /**
     * 年別アーカイブを数える
     */
    public function getCountYear($labelIds)
    {
        return $this->labeledEntryDao()->getCountYear($labelIds);
    }

    /**
     * ラベルIDを複数指定し、公開しているエントリー数を数え上げる
     */
    public function getOpenEntryCountByLabelIds($labelIds)
    {
        $dao = $this->labeledEntryDao();
        $dao->getOpenEntryCountByLabelIds($labelIds, SOYCMS_NOW);
        $count = $dao->getRowCount();
        return $count;
    }

    /**
     * ラベルID（複数）からエントリーを取得
     */
    public function getEntryByLabelIds($labelIds)
    {
        $dao = $this->entryDao();

        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);
        $array = $dao->getEntryByLabelIds($labelIds);
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return   $array;
    }

    /**
     * 最近使用されたラベルを取得（管理側で使用）
     */
    public function getRecentLabelIds()
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        try {
            $array = $dao->getRecentLabelIds();

            $res = array();
            foreach ($array as $row) {
                $res[] = $row["label_id"];
            }
            $array = $res;
        } catch (Exception $e) {
//          error_log($e->getMessage());
            $array = array();
        }
        return $array;
    }

    /**
     * 最近使用されたエントリーを取得（管理側で使用）
     */
    public function getRecentEntriesByLabelId($labelId)
    {
        $dao = $this->labeledEntryDao();
        $dao->setLimit($this->limit);
        $dao->setIgnoreColumns($this->ignoreColumns);
        return $dao->getRecentEntriesByLabelId($labelId);
    }

    /**
     * 最近使用されたエントリーを取得（管理側で使用）
     */
    public function getRecentEntries()
    {
        $dao = $this->entryDao();
        $dao->setLimit($this->limit);
        $dao->setOffset($this->offset);
        $dao->setIgnoreColumns($this->ignoreColumns);
        $array = $dao->getRecentEntries();
        $this->totalCount = $dao->getRowCount();

        //ラベルを取得
        foreach ($array as $key => $entry) {
            $array[$key]->setLabels($this->getLabelIdsByEntryId($entry->getId()));
        }

        return $array;
    }

    /**
     * MCEの特殊なタグを取り除く
     * 空の<p></p>または<p />は<br>に変換
     */
    public function cleanupMCETags($html)
    {
        return  preg_replace('/<p><\/p>|<p\s+\/>/', '<br>', preg_replace('/\s?mce_[a-zA-Z0-9_]+\s*=\s*"[^"]*"/', '', $html));
    }

    /**
     * コメント数を取得
     */
    public function getCommentCount($entryId)
    {
        return SOY2DAOFactory::create("cms.EntryCommentDAO")->getCommentCountByEntryId($entryId);
    }

    public function getApprovedCommentCountByEntryId($entryId)
    {
        return SOY2DAOFactory::create("cms.EntryCommentDAO")->getApprovedCommentCountByEntryId($entryId);
    }

    /**
     * トラックバック数を取得
     */
    public function getTrackbackCount($entryId)
    {
        return SOY2DAOFactory::create("cms.EntryTrackbackDAO")->getTrackbackCountByEntryId($entryId);
    }

    public function getCertificatedTrackbackCountByEntryId($entryId)
    {
        return SOY2DAOFactory::create("cms.EntryTrackbackDAO")->getCertificatedTrackbackCountByEntryId($entryId);
    }

    /**
     * getUniqueAlias
     * ユニークなエイリアスを取得
     */
    public function getUniqueAlias($id, $title)
    {
        $dao = $this->entryDao();

        //[?#\/%\&]は取り除く
        //2009-02-17 CGIモードで不具合が出るので & も削除
        //2009-02-17 Labelでも使うのでCMSUtil::sanitizeAliasに移動
        $title = CMSUtil::sanitizeAlias($title);

        //数字だけの場合は_を前につける
        if (is_numeric($title)) {
            $title = "_".$title;
        }

        try {
            $bean = $dao->getByAliasOnlyId($title);

            if ($bean->getId() == $id) {
                return $title;
            }
        } catch (Exception $e) {
//          error_log($e->getMessage());
            return $title;
        }

        return $title."_".$id;
    }

    private function entryDao()
    {
        if (!$this->entryDAO) {
            $this->entryDAO = SOY2DAOFactory::create("cms.EntryDAO");
        }
        return $this->entryDAO;

        // static $dao;
        // if ((null===$dao)) {
        //     $dao = SOY2DAOFactory::create("cms.EntryDAO");
        // }
        // return $dao;
    }
    private function entryLabelDao()
    {
        if (!$this->entryLabelDAO) {
            $this->entryLabelDAO = SOY2DAOFactory::create("cms.EntryLabelDAO");
        }
        return $this->entryLabelDAO;

        // static $dao;
        // if ((null===$dao)) {
        //     $dao = SOY2DAOFactory::create("cms.EntryLabelDAO");
        // }
        // return $dao;
    }
    private function labeledEntryDao()
    {
        if (!$this->labeledEntryDAO) {
            $this->labeledEntryDAO = SOY2DAOFactory::create("LabeledEntryDAO");
        }
        return $this->labeledEntryDAO;

        // static $dao;
        // if ((null===$dao)) {
        //     $dao = SOY2DAOFactory::create("LabeledEntryDAO");
        // }
        // return $dao;
    }
}
