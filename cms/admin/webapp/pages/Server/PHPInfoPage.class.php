<?php

class PHPInfoPage extends CMSWebPageBase
{
    public function __construct()
    {
        if (!UserInfoUtil::isDefaultUser()) {
            $this->jump("");
        }
        phpinfo();
        exit();
    }
}
