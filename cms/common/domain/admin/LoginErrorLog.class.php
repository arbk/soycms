<?php

/**
 * @table LoginErrorLog
 */
class LoginErrorLog
{
    /**
     * @id
     */
    private $id;

    private $ip;
    private $count;  //ログインを何回挑戦したか？

    private $successed;

    /**
     * @column start_date
     */
    private $startDate;

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

    public function getIp()
    {
        return $this->ip;
    }
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getCount()
    {
        return $this->count;
    }
    public function setCount($count)
    {
        $this->count = $count;
    }

    public function getSuccessed()
    {
        return $this->successed;
    }

    public function setSuccessed($successed)
    {
        $this->successed = $successed;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
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
