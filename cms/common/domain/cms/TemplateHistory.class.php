<?php
/**
 * @table TemplateHistory
 */
class TemplateHistory
{
    /**
     * @id
     */
    private $id;

    /**
     * @column page_id
     */
    private $pageId;

    private $contents;

    /**
     * @column update_date
     */
    private $updateDate;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getPageId()
    {
        return $this->pageId;
    }
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
    public function getContents()
    {
        return $this->contents;
    }
    public function setContents($contents)
    {
        $this->contents = $contents;
    }
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    }
}
