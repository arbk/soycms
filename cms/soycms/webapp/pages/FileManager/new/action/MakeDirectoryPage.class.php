<?php

class MakeDirectoryPage extends CMSWebPageBase
{
    public function doPost()
    {
        //パス
        $path = $_POST["path"];

        //ディレクトリ名
        $dirname = $_POST["name"];

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
