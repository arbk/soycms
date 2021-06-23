<?php

/**
 * @table Entry
 */
class Entry
{
    const ENTRY_ACTIVE = 1;
    const ENTRY_OUTOFDATE = -1;
    const ENTRY_NOTPUBLIC = -2;

    const PERIOD_START = 0;
    const PERIOD_END = 2147483647;

    /**
     * @id
     */
    private $id;
    private $title;
    private $alias;
    private $content;
    private $more;
    private $cdate;
    private $udate;
    private $openPeriodStart;
    private $openPeriodEnd;
    private $isPublished;
    private $style;
    private $author;
    private $description;

    /**
     * @no_persistent
     * 割り当てられているラベルIDを保存
     */
    private $labels = array();

    /**
     * @no_persistent
     */
    private $url;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function getMore()
    {
        return $this->more;
    }
    public function setMore($more)
    {
        $this->more = $more;
    }
    public function getCdate()
    {
        if ((null===$this->cdate)) {
            return SOYCMS_NOW;
        }

        if (is_numeric($this->cdate)) {
            return $this->cdate;
        }

        return null;
    }
    public function setCdate($cdate)
    {
        $this->cdate = $cdate;
    }
    public function getOpenPeriodStart()
    {
        return $this->openPeriodStart;
    }
    public function setOpenPeriodStart($openPeriodStart)
    {
        $this->openPeriodStart = $openPeriodStart;
    }

    public function getOpenPeriodEnd()
    {
        return $this->openPeriodEnd;
    }
    public function setOpenPeriodEnd($openPeriodEnd)
    {
        $this->openPeriodEnd = $openPeriodEnd;
    }
    public function getIsPublished()
    {
        return $this->isPublished;
    }
    public function setIsPublished($isPublished)
    {
        $this->isPublished = (int)$isPublished;
    }

    /**
     * 設定されているラベルIDを返す
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * 設定されているラベルIDを返す
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    public function getUdate()
    {
        return $this->udate;
    }
    public function setUdate($udate)
    {
        $this->udate = $udate;
    }

    public function getAlias()
    {
        if (strlen($this->alias)<1) {
            return $this->getId();
        }
        return $this->alias;
    }
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
    public function isEmptyAlias()
    {
        return (strlen($this->alias)==0);
    }

    public function getStyle()
    {
        return $this->style;
    }
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * 現在このエントリーの状態を返します
     * @return ENTRY_ACTIVE 公開状態
     * @return ENTRY_OUTOFDATE 期間外
     * @return ENTRY_NOTPUBLIC 未公開状態
     *
     * if(isActive() > 0){
     *     //公開状態のときの処理
     *  } else{
     *     //未公開状態の時の処理
     *  }
     */
    public function isActive()
    {
        if (!$this->isPublished) {
            return self::ENTRY_NOTPUBLIC;
        }
        $now = SOYCMS_NOW;

       /*
        * CMSUtil::decodeDateによってopenPeriodStartとopenPeriodEndのDATE_MIN/DATE_MAXはnullに書き換えられている
        */
        if (((null===$this->openPeriodStart) || $this->openPeriodStart <= $now)
        &&
        ((null===$this->openPeriodEnd)   || $now < $this->openPeriodEnd)
        ) {
            return self::ENTRY_ACTIVE;
        } else {
            return self::ENTRY_OUTOFDATE;
        }
    }

    public function getStateMessage()
    {
        switch ($this->isActive()) {
            case self::ENTRY_ACTIVE:
                return CMSMessageManager::get("SOYCMS_STAY_PUBLISHED");
            case self::ENTRY_OUTOFDATE:
                return CMSMessageManager::get("SOYCMS_OUTOFDATE");
            case self::ENTRY_NOTPUBLIC:
                return CMSMessageManager::get("SOYCMS_NOT_PUBLISHED");
            default:
                return CMSMessageManager::get("SOYCMS_OMG");
        }
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
