<?php

class CreateCategoryPage extends CMSWebPageBase
{
    public function doPost()
    {
        if (soy2_check_token()) {
            $this->run("Plugin.CreateCategoryAction");
            $this->jump("Plugin");
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->jump("Plugin");
    }
}
