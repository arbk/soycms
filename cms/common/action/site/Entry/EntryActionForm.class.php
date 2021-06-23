<?php
class EntryActionForm extends SOY2ActionForm
{
    public $id;
    public $title;
    public $content;
    public $more;
    public $cdate;
    public $openPeriodStart;
    public $openPeriodEnd;
    public $isPublished;
    public $style;
    public $description;

    //2009-02-12追加
    public $alias;

    public function setId($value)
    {
        $this->id = $value;
    }
    public function setTitle($value)
    {
        $this->title = $value;
    }
    public function setContent($value)
    {
        $this->content = $value;
    }
    public function setMore($value)
    {
        $this->more = $value;
    }
    public function setCdate($cdate)
    {
        $this->cdate = $cdate;
    }
    public function setOpenPeriodStart($start)
    {
        $utime = (strlen($start)) ? strtotime($start) : false;
        if (!($utime === false)) {
            $this->openPeriodStart = $utime;
        }
    }
    public function setOpenPeriodEnd($end)
    {
        $utime = (strlen($end)) ? strtotime($end) : false;
        if (!($utime === false)) {
            $this->openPeriodEnd = $utime;
        }
    }
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    public function setStyle($style)
    {
        $this->style= $style;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
}
