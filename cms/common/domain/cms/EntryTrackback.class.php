<?php
/**
 * @table EntryTrackback
 */
class EntryTrackback
{
    /**
     * @id
     */
    private $id;

    /**
     * @column entry_id
     */
    private $entryId;

    private $excerpt;

    private $url;

    private $title;

    /**
     * @column blog_name
     */
    private $blogName;

    private $certification;

    private $submitdate;

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

    public function getExcerpt()
    {
        return $this->excerpt;
    }
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getBlogName()
    {
        return $this->blogName;
    }
    public function setBlogName($blogName)
    {
        $this->blogName = $blogName;
    }

    public function getCertification()
    {
        return $this->certification;
    }
    public function setCertification($certification)
    {
        $this->certification = $certification;
    }

    public function getSubmitdate()
    {
        return $this->submitdate;
    }
    public function setSubmitdate($submitdate)
    {
        $this->submitdate = $submitdate;
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
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
