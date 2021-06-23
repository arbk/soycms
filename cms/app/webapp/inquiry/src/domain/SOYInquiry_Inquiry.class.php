<?php
/**
 * @table soyinquiry_inquiry
 */
class SOYInquiry_Inquiry
{
    const FLAG_NEW     = 0;//未読
    const FLAG_READ    = 1;//既読
    const FLAG_DELETED = 2;//削除済み

    const COMMENT_HAS = 1;  //コメント有り
    const COMMENT_NONE = 2; //コメント無し

    /**
     * @id
     */
    private $id;

    /**
     * @column tracking_number
     */
    private $trackingNumber;

    /**
     * @column form_id
     */
    private $formId;

    /**
     * @column ip_address
     */
    private $ipAddress;

    private $content;

    private $data;

    private $flag = 0;  //未読

    /**
     * @column create_date
     */
    private $createDate;

    /**
     * @column form_url
     */
    private $formUrl;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getFormId()
    {
        return $this->formId;
    }
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
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

    public function getData()
    {
        return $this->data;
    }
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getDataArray()
    {
        return unserialize($this->data);
    }

    public function getFlag()
    {
        return $this->flag;
    }
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    public function getFlagText()
    {
        switch ($this->flag) {
            case self::FLAG_NEW:
                return "未読";
            case self::FLAG_READ:
                return "既読";
            case self::FLAG_DELETED:
                return "削除済";
        }
    }

    /**
     * 未読かどうか
     * @return boolean
     */
    public function isUnread()
    {
        return ($this->flag == 0);
    }

    public function getFormUrl()
    {
        return $this->formUrl;
    }
    public function setFormUrl($formUrl)
    {
        $this->formUrl = $formUrl;
    }

    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }
}
