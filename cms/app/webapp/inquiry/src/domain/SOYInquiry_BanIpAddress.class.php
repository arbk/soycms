<?php
/**
 * @table soyinquiry_ban_ip_address
 */
class SOYInquiry_BanIpAddress
{
    /**
     * @column ip_address
     */
    private $ipAddress;

    /**
     * @column log_date
     */
    private $logDate;

    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    public function getLogDate()
    {
        return $this->logDate;
    }
    public function setLogDate($logDate)
    {
        $this->logDate = $logDate;
    }
}
