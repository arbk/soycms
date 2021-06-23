<?php
/**
 * @table Entry inner join EntryTrackback on (EntryTrackback.entry_id = Entry.id) inner join EntryLabel on (EntryLabel.entry_id = Entry.id)
 */
class EntryTrackbackLogic implements SOY2LogicInterface
{
    public static function getInstance($className, $args)
    {
        return SOY2LogicBase::getInstance($className, $args);
    }

    /**
     * @column EntryTrackback.id
     * @alias id
     */
    private $id;

    /**
     * @column EntryTrackback.title
     * @alias title
     */
    private $title;

    /**
     * @column EntryTrackback.excerpt
     * @alias excerpt
     */
    private $excerpt;

    /**
     * @column Entry.alias
     * @alias alias
     */
    private $alias;

    /**
     * @column Entry.title as entryTitle
     * @alias entryTitle
     */
    private $entryTitle;

    /**
     * @column EntryTrackback.submitdate
     * @alias submitdate
     */
    private $submitDate;

    /**
     * @column EntryTrackback.entry_id
     * @alias entry_id
     */
    private $entryId;

    /**
     * @column EntryTrackback.blog_name
     * @alias blog_name
     */
    private $blogName;

    /**
     * @column EntryTrackback.certification as isCertification
     * @alias isCertification
     */
    private $isCertification;

    /**
     * @column EntryTrackback.url
     * @alias url
     */
    private $url;

    /**
     * @no_persistent
     */
    private $totalCount;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getExcerpt()
    {
        return $this->excerpt;
    }
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }
    public function getAlias()
    {
        return $this->alias;
    }
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    public function getEntryTitle()
    {
        return $this->entryTitle;
    }
    public function setEntryTitle($entryTitle)
    {
        $this->entryTitle = $entryTitle;
    }
    public function getLabelId()
    {
        return $this->labelId;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }
    public function getSubmitDate()
    {
        return $this->submitDate;
    }
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }
    public function getEntryId()
    {
        return $this->entryId;
    }
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }
    public function getIsCertification()
    {
        return $this->isCertification;
    }
    public function setIsCertification($isCertification)
    {
        $this->isCertification = $isCertification;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getByLabelIds($labelIds, $count, $offset)
    {
        $dao = SOY2DAOFactory::create("EntryTrackbackLogicDAO");
        $dao->setLimit($count);
        $dao->setOffset($offset);
        $retVal =$dao->getByLabelIds($labelIds);
        $this->setTotalCount($dao->getRowCount());
        return $retVal;
    }

    public function getRecentTrackbacks($labelIds, $count = SOYCMS_INI_NUMOF_TRACKBACK_RECENT)
    {
        $dao = SOY2DAOFactory::create("EntryTrackbackLogicDAO");
        $dao->setLimit($count);
        $trackbacks = $dao->getOpenCertificatedTrackbackByLabelIds($labelIds, SOYCMS_NOW);
        return $trackbacks;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function getBlogName()
    {
        return $this->blogName;
    }
    public function setBlogName($blogName)
    {
        $this->blogName = $blogName;
    }
}


abstract class EntryTrackbackLogicDAO extends SOY2DAO
{

    /**
     * @distinct
     * @order #submitDate# DESC
     */
    public function getByLabelIds($labelIds)
    {
        $query = $this->getQuery();
        $labelIds = array_map(function ($val) {
            return (int)$val;
        }, $labelIds);

        if (count($labelIds)) {
            $query->where = " EntryLabel.label_id in (" . implode(",", $labelIds) .") ";
        }
        $result = $this->executeQuery($query, array());
        $array = array();
        foreach ($result as $row) {
            $array[] = $this->getObject($row);
        }
        return $array;
    }

    /**
     * @order #submitdate# DESC
     */
    public function getOpenCertificatedTrackbackByLabelIds($labelIds, $time)
    {
        $query = $this->getQuery();
        $labelIds = array_map(function ($val) {
            return (int)$val;
        }, $labelIds);
        $query->where = " EntryLabel.label_id in (" . implode(",", $labelIds) .") ";
        $query->where .= "AND Entry.isPublished = 1 ";
        $query->where .= "AND (openPeriodEnd > :now AND openPeriodStart < :now)";
        $query->where .= "AND certification = 1";

        $binds = array(
        ":now" => $time
        );

        $result = $this->executeQuery($query, $binds);

        $array = array();
        foreach ($result as $row) {
            $array[] = $this->getObject($row);
        }

        return $array;
    }
}
