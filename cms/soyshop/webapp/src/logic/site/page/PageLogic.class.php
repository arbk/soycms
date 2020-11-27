<?php

class PageLogic extends SOY2LogicBase{

	private $errors = array();

    function validate(SOYShop_Page $obj){

		$errors = array();

		if(strlen($obj->getName()) < 1){
			$errors["name"] = MessageManager::get("ERROR_REQUIRE");
		}

		if(!is_null($obj->getUri())){
			if(strlen($obj->getUri()) < 1){
				$errors["uri"] = MessageManager::get("ERROR_REQUIRE");
			}else if(!preg_match('/^[a-zA-Z0-9\.\/\_-]+$/', $obj->getUri())){
				$errors["uri"] = MessageManager::get("ERROR_INVALID");
			}else if(preg_match('/^user\/?/', $obj->getUri())){
				$errors["uri"] = MessageManager::get("ERROR_INVALID");
			}
		}

		if(strlen($obj->getType()) < 1){
			$errors["type"] = MessageManager::get("ERROR_REQUIRE");
		}


		$this->setErrors($errors);

		return (empty($errors));
    }

    function update(SOYShop_Page $obj){
		SOY2DAOFactory::create("site.SOYShop_PageDAO")->update($obj);
		self::onUpdate($obj);
    }

    function onUpdate(SOYShop_Page $obj){
    	self::_generatePageDirectory($obj);
		self::updatePageObject($obj);
		self::_updatePageMapping();
    }

    function getErrors() {
    	return $this->errors;
    }
    function setErrors($errors) {
    	$this->errors = $errors;
    }

	/**
	 * ディレクトリを自動で生成する
	 */
    private function _generatePageDirectory(SOYShop_Page $obj, $force = false){

		/* プログラムファイル出力 */
		$classFilePath = SOYSHOP_SITE_DIRECTORY . ".page/" . $obj->getCustomClassFileName();
		if(!file_exists($classFilePath) || $force){
			$code = file_get_contents(dirname(__FILE__) . "/default/default.php");

			//replace
			$code = str_replace("%class%", $obj->getCustomClassName(), $code);
			$code = str_replace("%baseclass%", $obj->getBaseClassName(), $code);

			$header = "<?php //generated by soyshop " . date("Y-m-d H:i:s") . "\n\n";
			$footer = "\n\n?>";
			file_put_contents($classFilePath, $header . $code . $footer);
		}
	}

	/**
	 * generate css file
	 */
	private function _generateCSSFile(SOYShop_Page $obj, $force = false){
		/* CSSの出力 */
    	$uri = $obj->getUri();

		$size = strlen ($uri);
    	$pos = strpos (strrev($uri), "/");

		$dir = ($pos) ? substr($uri, 0, $size - $pos) : "";
		$start = ($pos) ? $size - $pos : 0;
		$file = substr($uri, $start);

		$targetDir = SOYSHOP_SITE_DIRECTORY . $dir;
		if(!file_exists($targetDir)){
			mkdir($targetDir, 0777, true);
		}

		$cssFile = $targetDir . "/" . preg_replace('/\.html$/', "", $file) . ".css";
		if(!file_exists($cssFile) || $force){
			file_put_contents($cssFile, file_get_contents(dirname(__FILE__) . "/default/default.css"));
		}
	}

	/**
	 * objectの保存
	 */
	function updatePageObject($page){
		$obj = $page->getPageObject();
		$plain = SOY2::cast("object", $obj);

		$filepath = SOYSHOP_SITE_DIRECTORY . ".page/" . $page->getCustomClassName() . ".conf";

		file_put_contents($filepath, json_encode($plain));
	}

	private function _updatePageMapping(){
		$pages = SOY2DAOFactory::create("site.SOYShop_PageDAO")->get();

		$mapping = array();
		foreach($pages as $id => $page){
			$mapping[$id] = array(
				"type" => $page->getType(),
				"uri" => $page->getUri()
			);
		}

		SOYShop_DataSets::put("site.url_mapping", $mapping);
	}
}
