<?php

class CreatePage extends CMSWebPageBase
{
    private $labels;

    public function __construct($arg)
    {
        $this->labels = $arg;
        $this->jump("Entry.Detail", array(
        "initLabelList" => $arg
        ));
        exit;
    }
}
