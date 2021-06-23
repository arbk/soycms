<?php

/**
 * エントリーの一覧を取得
 * @attribute Entities
 * @attribute total
 */
class EntryListAction extends SOY2Action
{
    /**
     * ラベルID
     */
    private $id = null;

    /**
     * ラベルID（複数指定）
     */
    private $ids = array();

    private $offset;
    private $limit;

    /**
     * 取得しない列項目
     */
    private $ignoreColumns;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setIds($ids)
    {
        $this->ids = $ids;
    }
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function setIgnoreColumns($ignoreColumns)
    {
        $this->ignoreColumns = $ignoreColumns;
    }

    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic", array("offset" => $this->offset, "limit" => $this->limit));
        $logic->setIgnoreColumns($this->ignoreColumns);

        try {
            if (null!==$this->id && (is_string($this->id) || is_numeric($this->id))) {
                // ラベルIDに対するエントリーオブジェクトのリストを返す
                $entries = $logic->getByLabelId($this->id);
                $this->setAttribute("Entities", $entries);
            } elseif (is_array($this->ids) && count($this->ids) > 0) {
                // ラベルIDを複数指定した場合
                $entries = $logic->getByLabelIds($this->ids);
                $this->setAttribute("Entities", $entries);
            } else {
                // エントリーオブジェクトの配列を返す
                $entries = $logic->getRecentEntries();
                $this->setAttribute("Entities", $entries);
            }

            // 合計件数を返す
            $this->setAttribute("total", $logic->getTotalCount());
        } catch (Exception $e) {
            $this->setErrorMessage('failed', CMSMessageManager::get("SOYCMS_FAILED_TO_GET_ENTRY_LIST"));
            return SOY2Action::FAILED;
        }

        return SOY2Action::SUCCESS;
    }
}
