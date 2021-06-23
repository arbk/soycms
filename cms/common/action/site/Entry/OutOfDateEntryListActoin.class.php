<?php

class OutOfDateEntryListActoin extends SOY2Action
{
    private $offset;
    private $limit;

    /**
     * 取得しない列項目
     */
    private $ignoreColumns;

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
            $list = $logic->getOutOfDateEntryList();
            $this->setAttribute("Entities", $list);
            // 合計件数を返す
            $this->setAttribute("total", $logic->getTotalCount());
        } catch (Exception $e) {
            $this->setErrorMessage('failed', CMSMessageManager::get("SOYCMS_FAILED_TO_GET_ENTRY_LIST"));
            return SOY2Action::FAILED;
        }

        return SOY2Action::SUCCESS;
    }
}
