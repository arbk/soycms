<?php

class RecentEntryListByLabelId extends SOY2Action
{
    public $limit = SOYCMS_INI_NUMOF_ENTRY_RECENT;
    public $labelId;

    /**
     * 取得しない列項目
     */
    private $ignoreColumns;

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }

    public function setIgnoreColumns($ignoreColumns)
    {
        $this->ignoreColumns = $ignoreColumns;
    }

    public function execute()
    {
        //記事管理者の場合
        if (class_exists("UserInfoUtil") && !UserInfoUtil::hasSiteAdminRole()) {
            $prohibitedLabelIds = SOY2Logic::createInstance("logic.site.Label.LabelLogic")->getProhibitedLabelIds();
            if (in_array($this->labelId, $prohibitedLabelIds)) {
                $this->setAttribute("entries", array());
                return SOY2Action::FAILED;
            }
        }

        //最新エントリーを取得
//      $dao = SOY2DAOFactory::create("cms.EntryDAO");

        $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic", array("limit" => $this->limit));
        $logic->setIgnoreColumns($this->ignoreColumns);
        $entries = $logic->getRecentEntriesByLabelId($this->labelId);

        //記事管理者の場合
        if (count($entries) && isset($prohibitedLabelIds) && count($prohibitedLabelIds)) {
            foreach ($entries as $key => $entry) {
                $labels = $logic->getLabelIdsByEntryId($entry->getId());
                //非表示のラベルの付いた記事は飛ばす
                if (count(array_intersect($labels, $prohibitedLabelIds))) {
                    unset($entries[$key]);
                }
            }
        }

        $this->setAttribute("entries", $entries);
        return SOY2Action::SUCCESS;
    }
}
