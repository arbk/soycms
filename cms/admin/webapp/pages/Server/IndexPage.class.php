<?php

class IndexPage extends CMSWebPageBase
{
    public function __construct()
    {
        parent::__construct();

        if (!UserInfoUtil::isDefaultUser()) {
            $this->jump("");
        }

        require_once(SOY2::RootDir() . "error/error.func.php");

        $this->addTextArea("server_info", array(
        "text"=>get_soycms_report() . "\n\n" . get_soycms_options() . "\n\n" . get_environment_report(),
        "style" => "width:100%;height:1000px;border-style:none;",
        "readonly"=>"readonly"
        ));

  //    $this->addModel("php_info", array(
  //      "src" => SOY2PageController::createLink("Server.PHPInfo"),
  //      "style" => "width:100%;height:1000px;border-style:none;",
  //    ));

        $this->addLink("php_info_link", array(
        "link"=>SOY2PageController::createLink("Server.PHPInfo"),
        "target"=>"_blank",
        "rel"=>"noopener"
        ));
    }
}
