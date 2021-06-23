<?php
/**
 * @table AutoLogin
 */
class AutoLogin
{
    /**
     * @column user_id
     */
    private $userId;

    private $token;

    /**
     * @column time_limit
     */
    private $limit;

    public function getUserId()
    {
        return $this->userId;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function getToken()
    {
        return $this->token;
    }
    public function setToken($token)
    {
        $this->token = $token;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}
