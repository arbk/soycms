<?php

class FileUploadPage extends WebPage {

	function doPost(){
		
		$res = $this->run("Entry.UploadFileAction");
		
		echo json_encode($res->getAttribute("result"));
		exit;
	}
    function __construct($arg) {
    	WebPage::WebPage();
		
		$this->createAdd("popupScript","HTMLModel",array(
			"type" => "text/JavaScript",
			"src" => SOY2PageController::createRelativeLink("./js/tiny_mce/tiny_mce_popup.js")
		));
		
		$this->createAdd("prototypejs","HTMLModel",array(
			"type" => "text/JavaScript",
			"src" => SOY2PageController::createRelativeLink("./js/prototype.js")
		));
		$this->createAdd("commonjs","HTMLModel",array(
			"type" => "text/JavaScript",
			"src" => SOY2PageController::createRelativeLink("./js/common.js")
		));
		
		$this->createAdd("applyForm","HTMLForm",array(
			"action"=>SOY2PageController::createLink("Entry.Editor.UploadApply")
		));
		
		$this->createAdd("cancelForm","HTMLForm",array(
			"action"=>SOY2PageController::createLink("Entry.Editor.UploadCancel")
		));
		
		$this->createAdd("uploadForm","HTMLForm");
		
//		$this->createAdd("parameters","HTMLScript",array(
//			"lang" => "text/JavaScript",
//			"script" => 'var remotoURI = "'.UserInfoUtil::getSiteURL().substr($this->getDefaultUpload(),1).'";'
//		));
		
		$this->createAdd("file_manager_iframe","HTMLModel",array(
			"target_src"=>SOY2PageController::createLink("FileManager.File")
		));
    }
    
    function getDefaultUpload(){
    	
    	$dao = SOY2DAOFactory::create("cms.SiteConfigDAO");
    	$config = $dao->get();
    	$dir = $config->getUploadDirectory();
    	
    	return $dir;
    }
}
?>