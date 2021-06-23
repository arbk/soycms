<?php
/**
 * @table soyinquiry_comment
 */
class SOYInquiry_Comment
{
    /**
     * @id
     */
    private $id;

    /**
     * @column inquiry_id
     */
    private $inquiryId;

    private $title;

    private $author;

    private $content;

    /**
     * @column create_date
     */
    private $createDate;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getInquiryId()
    {
        return $this->inquiryId;
    }
    public function setInquiryId($inquiryId)
    {
        $this->inquiryId = $inquiryId;
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
    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function getCreateDate()
    {
        if (!$this->createDate) {
            return SOYCMS_NOW;
        }
        return $this->createDate;
    }
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }
}
