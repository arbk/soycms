<?php
// SOY2::import("site.page.Entry.ListPage");
require_once(dirname(__DIR__) . "/ListPage.class.php");

class OutOfDatePage extends ListPage
{
    public function __construct($arg)
    {
        parent::__construct(array("OutOfDate"));

        DisplayPlugin::hide("no_label");
        $this->addLabel("label_state", array(
        "text"=>CMSMessageManager::get("SOYCMS_OUTOFDATE_ENTRY_LIST")
        ));
    }

    public function getTemplateFilePath()
    {
//      if(!defined("SOYCMS_LANGUAGE")||SOYCMS_LANGUAGE=="ja"){
            return dirname(__DIR__) . "/ListPage.html";
//      }else{
//          return  SOYCMS_LANGUAGE_DIR. SOYCMS_LANGUAGE . "/Entry/ListPage.html";
//      }
    }

    public function getEntries($offset, $limit, $labelIds)
    {
        $result = $this->run("Entry.OutOfDateEntryListActoin", array(
            "offset"=>$offset,
            "limit"=>$limit,
            "ignoreColumns"=>array("more")
        ));

        $entities = $result->getAttribute("Entities");
        $totalCount = $result->getAttribute("total");

        return array($entities,$totalCount,min($offset, $totalCount));
    }

    /**
     * Get child labels
     */
    public function getNarrowLabels()
    {
        if (empty($this->labelIds)) {
            return array();
        } else {
            return parent::getNarrowLabels();
        }
    }
}
