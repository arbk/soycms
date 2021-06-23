<?php
/**
 * @table Memo
 */
class Memo
{
    // メモ　アーカイブやログインしているユーザ毎にメモの表示を変えるといった対策が必要になるかもしれないから、idカラムを追加しておいた

    /**
     * @id
     */
    private $id;
    private $content;

    /**
     * @column create_date
     */
    private $createDate;

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
        return $this->createDate;
    }
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
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
