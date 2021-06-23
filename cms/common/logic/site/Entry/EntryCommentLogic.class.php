<?php

/**
 * @table Entry inner join EntryComment on (EntryComment.entry_id = Entry.id) inner join EntryLabel on (EntryLabel.entry_id = Entry.id)
 */
class EntryCommentLogic implements SOY2LogicInterface
{
    public static function getInstance($className, $args)
    {
        return SOY2LogicBase::getInstance($className, $args);
    }

    /**
     * @column EntryComment.id
     * @alias id
     */
    private $id;

    /**
     * @column EntryComment.title
     * @alias title
     */
    private $title;

    /**
     * @column EntryComment.author
     * @alias author
     */
    private $author;

    /**
     * @column EntryComment.body
     * @alias body
     */
    private $body;

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
     * @column EntryLabel.label_id
     * @alias label_id
     */
    private $labelId;

    /**
     * @column EntryComment.submitdate
     * @alias submitdate
     */
    private $submitDate;

    /**
     * @column EntryComment.entry_id
     * @alias entry_id
     */
    private $entryId;

    /**
     * @column EntryComment.is_approved as isApproved
     * @alias isApproved
     */
    private $isApproved;

    /**
     * @no_persistent
     */
    private $totalCount;

    /**
     * @column EntryComment.mail_address as mailAddress
     * @alias mailAddress
     */
    private $mailAddress;

    /**
     * @column EntryComment.url
     * @alias url
     */
    private $url;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    public function getAlias()
    {
        return $this->alias;
    }
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    public function getLabelId()
    {
        return $this->labelId;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }

    public function getRecentComments($labelIds, $count = SOYCMS_INI_NUMOF_COMMENT_RECENT)
    {
        $dao = SOY2DAOFactory::create("EntryCommentLogicDAO");
        $dao->setLimit($count);
        $comments = $dao->getOpenCommentByLabelIds($labelIds, SOYCMS_NOW);
        return $comments;
    }

    public function getComments($labelIds, $count, $offset)
    {
        $dao = SOY2DAOFactory::create("EntryCommentLogicDAO");
        $dao->setLimit($count);
        $dao->setOffset($offset);
        $retVal =$dao->getByLabelIds($labelIds);
        $this->setTotalCount($dao->getRowCount());
        return $retVal;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
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

    public function getEntryTitle()
    {
        return $this->entryTitle;
    }
    public function setEntryTitle($entryTitle)
    {
        $this->entryTitle = $entryTitle;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function setBody($body)
    {
        $this->body = $body;
    }
    public function getIsApproved()
    {
        return $this->isApproved;
    }
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getMailAddress()
    {
        return $this->mailAddress;
    }
    public function setMailAddress($mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }

    public function delete($commentId)
    {
        $dao = SOY2DAOFactory::create("cms.EntryCommentDAO");
        return $dao->delete($commentId);
    }

    public function toggleApproved($commentId, $state)
    {
        $dao = SOY2DAOFactory::create("cms.EntryCommentDAO");
        return $dao->setApproved($commentId, $state);
    }
}

/**
 * @entity EntryCommentLogic
 */
abstract class EntryCommentLogicDAO extends SOY2DAO
{
    abstract public function get();
    abstract public function getByLabelId($labelId);

    /**
     * @order #submitDate# DESC
     */
    public function getOpenCommentByLabelIds($labelIds, $time)
    {
        $query = $this->getQuery();
        $labelIds = array_map(function ($val) {
            return (int)$val;
        }, $labelIds);
        if (count($labelIds)>0) {
            $query->where = " EntryLabel.label_id in (" . implode(",", $labelIds) .") AND ";
        }
        $query->where .= " Entry.isPublished = 1 ";
        $query->where .= "AND (openPeriodEnd > :now AND openPeriodStart < :now)";
        $query->where .= "AND is_approved = 1";

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

    /**
     * @order #submitDate# DESC
     */
    public function getByLabelIds($labelIds)
    {
        $query = $this->getQuery();

        $labelIds = array_map(function ($val) {
            return (int)$val;
        }, $labelIds);

        if (count($labelIds)>0) {
            $query->where = " EntryLabel.label_id in (" . implode(",", $labelIds) .") ";
        }

        $result = $this->executeQuery($query, array());
        $array = array();
        foreach ($result as $row) {
            $array[] = $this->getObject($row);
        }

        return $array;
    }
}
