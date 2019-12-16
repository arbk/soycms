<?php

class InitLogic extends SOY2LogicBase{

	var $siteDirectory;
	var $option = array("dbtype"=>"sqlite");

	function setOption($option){
		$this->option = $option;
	}

	function initDirectory($isOnlyAdmin=false){
		$shopId = SOYSHOP_ID;
		$targetDir = SOYSHOP_SITE_DIRECTORY;

		if($targetDir[strlen($targetDir)-1] != "/")$targetDir .= "/";
		$this->siteDirectory = $targetDir;

		$this->makeDirectories(array(
			$targetDir,
			$targetDir . ".cache/",
			$targetDir . ".db/",
			$targetDir . ".template/",
			$targetDir . ".template/custom/",
			$targetDir . ".page/",
			$targetDir . ".module/",
			$targetDir . "themes/",
			$targetDir . "files/"
		));

		file_put_contents($targetDir . ".db/.htaccess","deny from all");
		$this->initDefaultTemplate($targetDir. ".template/", $isOnlyAdmin);
		if(!$isOnlyAdmin){
			$this->initDefaultTheme($targetDir."themes/");
			$this->initDefaultPartsModule($targetDir.".module/");
			$this->initDefaultIcon($targetDir."files/");
		}
		$this->initController();
	}

	function makeDirectories($dirs){
		foreach($dirs as $dir){
			echo "mkdir $dir";
			echo " ";
			echo ( mkdir($dir) ? "success" :  ( file_exists($dir) ? "exists" : "fail" ) );
			echo "\n";
		}
	}

	function initController($rebuild=false){
		$controller = array();
		$controller[] = "<?php ";

		$tmp[] = "/* @generated by SOY SHOP at " . date("Y-m-d H:i:s") . "*/";

		if(defined("SOYCMS_PHP_CGI_MODE") && SOYCMS_PHP_CGI_MODE == true){

    		$controller[] = 'if(isset($_GET["pathinfo"])){';
			$controller[] = '$_SERVER["PATH_INFO"] = "/" . $_GET["pathinfo"];';
			$controller[] = 'unset($_GET["pathinfo"]);';
			$controller[] = '}';
    	}

    	$configFilePath = SOYSHOP_SITE_CONFIG_FILE;
    	$siteIncludeFilePath = SOYSHOP_WEBAPP . "conf/user.conf.php";

		$controller[] = "include(\"$configFilePath\");";
		$controller[] = "include(\"$siteIncludeFilePath\");";
		$controller[] = "SOY2PageController::run();";
		$controller[]  = "?>";

		$dir = ($rebuild) ? SOYSHOP_SITE_DIRECTORY : $this->siteDirectory;

		$fp = fopen($dir."index.php","w");
		fwrite($fp,implode("\n", $controller));
		fclose($fp);

		$im = array();
		$im[] = "<?php ";
		$im[] = "\$site_root = dirname(__FILE__);";
		$im[] = "include_once(\"" . dirname(SOYSHOP_ROOT) . "/common/im.inc.php\");";
		$im[]  = "?>";

		$fp = fopen($dir."im.php","w");
		fwrite($fp,implode("\n", $im));
		fclose($fp);


		/*
		 * create htaccess
		 */
		$tmp = array();

    	$tmp[] = "# @generated by SOY Shop at " . date("Y-m-d H:i:s");

    	if(defined("SOYCMS_PHP_CGI_MODE") && SOYCMS_PHP_CGI_MODE == true){

    		$tmp[] = "RewriteEngine on";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME} !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.php !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.html !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.htm !-f";
			$tmp[] = "RewriteCond %{REQUEST_URI} !/index.php/";
			$tmp[] = 'RewriteRule ^(.*)$ index.php?pathinfo=$1&%{QUERY_STRING} [L]';

    	}else{

			$tmp[] = "RewriteEngine on";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME} !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.php !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.html !-f";
			$tmp[] = "RewriteCond %{REQUEST_FILENAME}/index.htm !-f";
			$tmp[] = "RewriteCond %{REQUEST_URI} !/index.php/";
			$tmp[] = 'RewriteRule ^(.*)$ index.php/$1 [L]';

    	}

		file_put_contents($dir.".htaccess", implode("\n", $tmp));
	}

    function initDB(){

		if($this->option["dbtype"] == "sqlite"){
			$dbFilePath = SOYSHOP_SITE_DIRECTORY . ".db/sqlite.db";
		}else{
			$dbFilePath = SOYSHOP_SITE_DIRECTORY . ".db/mysql.db";
		}

		//delete db
		if(file_exists($dbFilePath)){
			$res = unlink($dbFilePath);
			if(!$res){
				echo "failed unlink " . $dbFilePath;
				return false;
			}
		}

		$db = new SOY2DAO();

		$sqls = file_get_contents(dirname(__FILE__) . "/" . $this->option["dbtype"].".sql");
		$sqls = explode(";",$sqls);

		foreach($sqls as $sql){
			$sql = trim($sql);
			if(!$sql)continue;

			echo "[SQL]" . str_replace(array("\n","\r","\t"),"",$sql) . "\n";

			try{
				$db->executeQuery($sql);
				echo "success" . "\n\n";
			}catch(SOY2DAOException $e){
				echo "failed ";
				echo $e->getPDOExceptionMessage() . "\n\n";
			}catch(Exception $e){
				echo "failed ";
				echo $e->getMessage() . "\n\n";
			}
		}

		//update config(for mysql)
		touch(SOYSHOP_SITE_DIRECTORY . ".db/" . $this->option["dbtype"].".db");

		return true;
    }

	function initDefaultTemplate($to, $isOnlyAdmin=false){
		if(!defined("SOYSHOP_TEMPLATE_ID"))define("SOYSHOP_TEMPLATE_ID","bryon");
		$path = dirname(__FILE__) . "/template/".SOYSHOP_TEMPLATE_ID."/";

		$files = scandir($path);
		foreach($files as $dir){
			if($dir[0] == ".")continue;

			if(!file_exists($to.$dir))mkdir($to.$dir);
			if(!is_dir($path . $dir))continue;
			$templates = scandir($path . $dir);

			foreach($templates as $file){
				if($file[0] == ".")continue;
				if($isOnlyAdmin && strpos($file, "notfound") === false) continue;
				if(preg_match('/\.ini$/',$file)){
					file_put_contents($to . $dir . "/" . $file,file_get_contents($path . $dir . "/" . $file));
				}else{
					file_put_contents($to . $dir . "/" . $file,$this->replaceTemplate(file_get_contents($path . $dir . "/" . $file)));
				}
			}
		}
	}

	function initDefaultPartsModule($to){
		if(!defined("SOYSHOP_TEMPLATE_ID"))define("SOYSHOP_TEMPLATE_ID","bryon");
		$path = dirname(__FILE__) . "/module/".SOYSHOP_TEMPLATE_ID."/";

		$files = scandir($path);
		foreach($files as $dir){
			if($dir[0] == ".")continue;

			if(!file_exists($to.$dir))mkdir($to.$dir);
			if(!is_dir($path . $dir))continue;
			$templates = scandir($path . $dir);

			foreach($templates as $file){
				if($file[0] == ".")continue;
				if(preg_match('/\.ini$/',$file)){
					file_put_contents($to . $dir . "/" . $file,file_get_contents($path . $dir . "/" . $file));
				}else{
					file_put_contents($to . $dir . "/" . $file,$this->replaceTemplate(file_get_contents($path . $dir . "/" . $file)));
				}
			}
		}
	}

	function initDefaultTheme($to){
		if(!defined("SOYSHOP_TEMPLATE_ID"))define("SOYSHOP_TEMPLATE_ID","bryon");
		$path = dirname(__FILE__) . "/theme/".SOYSHOP_TEMPLATE_ID."/";

		$this->copyDirectory($path,$to);
	}

	function initDefaultIcon($to){
		if(!defined("SOYSHOP_TEMPLATE_ID"))define("SOYSHOP_TEMPLATE_ID","bryon");
		$path = dirname(__FILE__) . "/files/".SOYSHOP_TEMPLATE_ID."/";

		$this->copyDirectory($path,$to);
	}

	function initDefaultVersion(){
		SOY2Logic::createInstance("logic.upgrade.UpdateDBLogic")->registerVersion(SOY2Logic::createInstance("logic.upgrade.CheckVersionLogic")->getUpdateVersion());
		return true;
	}

	function replaceTemplate($html){
		if(!defined("SOYSHOP_SITE_NAME"))define("SOYSHOP_SITE_NAME","インテリアショップLBD");
		$url = parse_url(SOYSHOP_SITE_URL);
		$path = $url["path"];
		if($path[strlen($path)-1] == "/")$path = substr($path,0,strlen($path) - 1);
		$html = str_replace("@@SOYSHOP_URI@@",$path,$html);
		$html = str_replace("@@SOYSHOP_NAME@@",SOYSHOP_SITE_NAME,$html);

		return $html;
	}

	function copyDirectory($from,$to){

		$files = scandir($from);

		if($from[strlen($from)-1] != "/")$from .= "/";
		if($to[strlen($to)-1] != "/")$to .= "/";

		foreach($files as $file){
			if($file[0] == ".")continue;

			if(is_dir($from . $file)){
				if(!file_exists($to.$file))mkdir($to.$file);
				$this->copyDirectory($from . $file, $to . $file);
				continue;
			}else{

				file_put_contents(
					$to . $file
					,file_get_contents($from . $file)
				);
			}

		}
	}

	function initModules($isOnlyAdmin=false){
		if(!defined("SOYSHOP_TEMPLATE_ID")) define("SOYSHOP_TEMPLATE_ID","bryon");
		$logic = SOY2Logic::createInstance("logic.plugin.SOYShopPluginLogic");
	    $logic->prepare();
	    $logic->searchModules();

	    //初期化時にインストールするモジュールの管理は/soyshop/src/init/plugin/plugin.default.iniで管理
	    $list = $logic->readModuleFile($isOnlyAdmin);
	    foreach($list as $moduleId){
	    	$logic->installModule(trim($moduleId));
	    }

		//プラグインの並べ替え
		if(!$isOnlyAdmin){
			foreach(array(27, 47) as $n){
				include_once(SOY2::RootDir() . "logic/upgrade/extend/extendUpdate-" . $n . ".php");
			}
		}

		return true;
	}

	/**
	 * register as SOY CMS Site
	 * @param Integer siteId
	 * @param Array option DSN
	 * @param String siteName
	 *
	 */
	function registSite($siteId = null,$option = array(), $siteName=null){

		//SOY2 config
		//SOY2::RootDir()の書き換え
		$oldRooDir = SOY2::RootDir();
		$oldDaoDir = SOY2DAOConfig::DaoDir();
		$oldEntityDir = SOY2DAOConfig::EntityDir();
		$oldDsn = SOY2DAOConfig::Dsn();
		$oldUser = SOY2DAOConfig::user();
		$oldPass = SOY2DAOConfig::pass();

		SOY2::RootDir(CMS_COMMON);
		SOY2DAOConfig::DaoDir(CMS_COMMON."domain/");
		SOY2DAOConfig::EntityDir(CMS_COMMON."domain/");
		SOY2DAOConfig::Dsn(ADMIN_DB_DSN);
		SOY2DAOConfig::user(ADMIN_DB_USER);
		SOY2DAOConfig::pass(ADMIN_DB_PASS);

		$dao = SOY2DAOFactory::create("admin.SiteDAO");

		$url = SOYCMS_TARGET_URL;
		if($url[strlen($url)-1] != "/")$url .= "/";
		$url .= $siteId;

		$path = soy2_realpath(SOYCMS_TARGET_DIRECTORY);
		$path .= $siteId."/";

		$dsn = "";
		if($option["dbtype"] == "mysql"){
			$dsn = $option["dsn"];
		}else{
			$dsn = "sqlite:" . $path.".db/sqlite.db";
		}

		$site = new Site();
		$site->setSiteId($siteId);
		$site->setSiteName($siteName);
		$site->setPath($path);
		$site->setUrl($url);
		$site->setSiteType(Site::TYPE_SOY_SHOP);
		$site->setDataSourceName($dsn);
		try{
			$dao->insert($site);
			$res = true;
		}catch(Exception $e){
			$res = false;
		}

		SOY2::RootDir($oldRooDir);
		SOY2DAOConfig::DaoDir($oldDaoDir);
		SOY2DAOConfig::EntityDir($oldEntityDir);
		SOY2DAOConfig::Dsn($oldDsn);
		SOY2DAOConfig::user($oldUser);
		SOY2DAOConfig::pass($oldPass);


		return $res;
	}
}
