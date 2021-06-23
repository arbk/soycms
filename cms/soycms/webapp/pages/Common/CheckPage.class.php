<?php

class CheckPage extends CMSWebPageBase
{
    public function __construct()
    {
        $site = UserInfoUtil::getSite();

        if ($site) {
            echo $site->getId();
        } else {
            echo 0;
        }

        exit;
    }
}
