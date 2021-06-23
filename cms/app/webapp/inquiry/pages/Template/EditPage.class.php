<?php

class EditPage extends WebPage
{
    public $target;

    public function doPost()
    {
        if (soy2_check_token()) {
            $target = $this->target;
            $dir = SOY2::RootDir() . "template/";
            if (!file_exists($dir . $target) || !is_writable($dir.$target)) {
                CMSApplication::jump("Template");
                exit;
            }

            $path = $dir . $target;

        //bk
            $content = file_get_contents($path);
            $bkPath = $path."_".date("YmdHis");
            file_put_contents($bkPath, $content);
            @chmod($bkPath, F_MODE_FILE);

            $content = $_POST["content"];
            file_put_contents($path, $content);
            @chmod($path, F_MODE_FILE);

            CMSApplication::jump("Template");
            exit;
        }
    }

    public function __construct()
    {

        $target = (isset($_GET["target"])) ? $_GET["target"] : array();
        $target = str_replace(array("..", "\\"), "", $target);
        $this->target = $target;
        $dir = SOY2::RootDir() . "template/";
        if (!file_exists($dir . $target) || !is_writable($dir.$target)) {
            CMSApplication::jump("Template");
            exit;
        }

        parent::__construct();

        $path = $dir . $target;

        $content = file_get_contents($path);

        $this->addForm("form");

        $this->createAdd("target", "HTMLLabel", array(
            "text" => $target
        ));

        $this->createAdd("content", "HTMLTextArea", array(
            "name" => "content",
            "value" => $content
        ));
    }
}
