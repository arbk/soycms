<?php

class DeletePage extends CMSWebPageBase
{
    public function doPost()
    {
        //パス
        $path = $_POST["path"];

        //返り値
        $flag = 1;
        echo $flag; //成功もしくは失敗を返す

        exit;
    }

    public function __construct()
    {
        parent::__construct();
    }
}
