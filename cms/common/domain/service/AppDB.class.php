<?php

/**
 * @table AppDB
 */
class AppDB
{
    /**
     * @id
     */
    private $id;

    /**
     * @column account_id
     */
    private $accountId;
    private $sign;

    /**
     * @column register_date
     */
    private $registerDate;

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

    public function getAccountId()
    {
        return $this->accountId;
    }
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    public function getSign()
    {
        return $this->sign;
    }
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    public function getRegisterDate()
    {
        return $this->registerDate;
    }
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;
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
