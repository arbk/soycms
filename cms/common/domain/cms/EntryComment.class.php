<?php

/**
 * @table EntryComment
 */
class EntryComment
{
    /**
     * @id
     */
    private $id;

    /**
     * @column entry_id
     */
    private $entryId;

    private $title;

    private $author;

    private $body;

    /**
     * @column submitdate
     */
    private $submitDate;

    /**
     * @column is_approved
     */
    private $isApproved;

    /**
     * @column mail_address
     */
    private $mailAddress;

    private $url;

    /**
     * @column extra_values
     */
    private $extraValues;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEntryId()
    {
        return $this->entryId;
    }
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getBody()
    {
        return $this->body;
    }
    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getSubmitDate()
    {
        return $this->submitDate;
    }
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;
    }
    public function getIsApproved()
    {
        return $this->isApproved;
    }
    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
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

    public function getExtraValues()
    {
        return $this->extraValues;
    }
    public function setExtraValues($extraValues)
    {
        return $this->extraValues = $extraValues;
    }
    public function getExtraValuesArray()
    {
        $res = soy2_unserialize($this->extraValues);
        if (is_array($res)) {
            return $res;
        } else {
            return array();
        }
    }
    public function setExtraValuesArray($extraValues)
    {
        if (is_array($extraValues)) {
            $this->extraValues = soy2_serialize($extraValues);
        } else {
            $this->extraValues = soy2_serialize(array());
        }
    }
}
