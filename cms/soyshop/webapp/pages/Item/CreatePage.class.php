<?php

class CreatePage extends WebPage{

	function doPost(){

		if(isset($_POST["Item"]) && soy2_check_token()){
			$item = (object)$_POST["Item"];

			$dao = SOY2DAOFactory::create("shop.SOYShop_ItemDAO");
			$logic = SOY2Logic::createInstance("logic.shop.item.ItemLogic");

			$item = SOY2::cast("SOYShop_Item",$item);
			$item->setType($_POST["ItemType"]);

			//
			if($item->getType() == SOYShop_Item::TYPE_CHILD){
				if(isset($_POST["group_item_id"]))$item->setType($_POST["group_item_id"]);
			}

			if($item->getType() == SOYShop_Item::TYPE_DOWNLOAD){
				$dir = SOYSHOP_SITE_DIRECTORY . "download/" . $item->getCode() . "/";
				if(!file_exists($dir)){
					mkdir($dir, 0777, true);

					//.htaccessを作成する
					file_put_contents($dir.".htaccess","deny from all");
					//index.html
					file_put_contents($dir."index.html","<!-- empty -->");
				}
			}

			if($item->getType() == SOYShop_Item::TYPE_DOWNLOAD_CHILD){
				if(isset($_POST["dlgroup_item_id"]))$item->setType($_POST["dlgroup_item_id"]);
			}

			if($logic->validate($item)){

				$id = $logic->create($item);
				$item->setId($id);

				SOYShopPlugin::load("soyshop.item.name");
				SOYShopPlugin::invoke("soyshop.item.name", array(
					"item" => $item
				));

				SOY2PageController::jump("Item.Detail.$id?updated=created");
				exit;
			}


			$this->obj = $item;
			$this->errors = $logic->getErrors();
		}
	}

	var $obj;
	var $errors = array();

    function __construct() {

    	$session = SOY2ActionSession::getUserSession();
		$appLimit = $session->getAttribute("app_shop_auth_limit");

    	//管理制限者で商品の追加を開こうとしたとき、商品一覧にリダイレクト
		if($appLimit == false){
			SOY2PageController::jump("Item");
		}

		MessageManager::addMessagePath("admin");

    	parent::__construct();

		$this->addForm("create_form");

    	self::buildForm();
    }

    private function buildForm(){

		$dao = SOY2DAOFactory::create("shop.SOYShop_ItemDAO");
		$obj = ($this->obj) ? $this->obj : new SOYShop_Item();

		if(isset($_GET["parent"])){
			$obj->setType($_GET["parent"]);
		}

		if(isset($_GET["dlparent"])){
			$obj->setType($_GET["dlparent"]);
		}

		$this->addInput("item_name", array(
    		"name" => "Item[name]",
    		"value" => $obj->getName()
    	));

    	SOYShopPlugin::load("soyshop.item.name");
		$nameForm = SOYShopPlugin::display("soyshop.item.name", array(
			"item" => $obj
		));

		$this->addLabel("extension_item_name_input", array(
			"html" => $nameForm
		));

    	$this->addInput("item_code", array(
    		"name" => "Item[code]",
    		"value" => $obj->getCode()
    	));

    	$this->addInput("item_stock", array(
    		"name" => "Item[stock]",
    		"value" => $obj->getStock()
    	));

    	$this->addInput("item_price", array(
    		"name" => "Item[price]",
    		"value" => $obj->getPrice()
    	));

		$config = $obj->getConfigObject();
    	$this->addTextArea("item_description", array(
    		"name" => "Item[config][description]",
    		"value" => (isset($config["description"])) ? $config["description"] : ""
    	));

    	$categoryDAO = SOY2DAOFactory::create("shop.SOYShop_CategoryDAO");
		$array = $categoryDAO->get();

		$this->createAdd("category_tree", "_base.MyTreeComponent", array(
			"list" => $array,
			"selected" => $obj->getCategory()
		));

		$this->addInput("item_category", array(
			"name" => "Item[category]",
			"value" =>$obj->getCategory(),
			"attr:id" => "item_category"
		));

		$this->addLabel("item_category_text", array(
			"text" => (isset($array[$obj->getCategory()])) ? $array[$obj->getCategory()]->getName() : "選択してください",
			"attr:id" => "item_category_text"
		));

		/*
		 * グループ周り
		 */
		if(is_numeric($obj->getType())){
			$itemType = (isset($_GET["parent"])) ? SOYShop_Item::TYPE_CHILD : SOYShop_Item::TYPE_DOWNLOAD_CHILD;
		}else{
			$itemType = $obj->getType();
		}
		$this->addInput("item_type_hidden", array(
			"name" => "ItemType",
			"value" => $itemType
		));
		$this->addCheckBox("radio_type_normal", array(
			"elementId" => "radio_type_normal",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_SINGLE,
			"selected" => ($obj->getType() == SOYShop_Item::TYPE_SINGLE),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_SINGLE . '");'
		));
		$this->addCheckBox("radio_type_group", array(
			"elementId" => "radio_type_group",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_GROUP,
			"selected" => ($obj->getType() == SOYShop_Item::TYPE_GROUP),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_GROUP . '");'
		));
		$this->addCheckBox("radio_type_child", array(
			"elementId" => "radio_type_child",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_CHILD,
			"selected" => ($itemType == SOYShop_Item::TYPE_CHILD),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_CHILD . '");$(\'#group_item_div\').show();'
		));
		$this->addCheckBox("radio_type_download", array(
			"elementId" => "radio_type_download",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_DOWNLOAD,
			"selected" => ($itemType == SOYShop_Item::TYPE_DOWNLOAD),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_DOWNLOAD . '");'
		));
		$this->addCheckBox("radio_type_dlgroup", array(
			"elementId" => "radio_type_dlgroup",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_DOWNLOAD_GROUP,
			"selected" => ($itemType == SOYShop_Item::TYPE_DOWNLOAD_GROUP),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_DOWNLOAD_GROUP . '");'
		));
		$this->addCheckBox("radio_type_dlgroup_child", array(
			"elementId" => "radio_type_dlgroup_child",
			"name" => "item_type",
			"value" => SOYShop_Item::TYPE_DOWNLOAD_CHILD,
			"selected" => ($itemType == SOYShop_Item::TYPE_DOWNLOAD_CHILD),
			"onclick" => '$(\'#item_type_hidden\').val("' . SOYShop_Item::TYPE_DOWNLOAD_CHILD . '");$(\'#dlgroup_item_div\').show();'
		));

		$groupItems = $dao->getByType(SOYShop_Item::TYPE_GROUP);

		$this->addSelect("group_item_select", array(
			"name" => "group_item_id",
			"options" => $groupItems,
			"property" => "name",
			"selected" => $obj->getType()
		));

		DisplayPlugin::toggle("group_item_exists", (count($groupItems) > 0));

		//ダウンロード販売プラグインがアクティブの時に表示
		DisplayPlugin::toggle("download_exists", SOYShopPluginUtil::checkIsActive("download_assistant"));

		$dlgroupItems = $dao->getByType(SOYShop_Item::TYPE_DOWNLOAD_GROUP);

		$this->addSelect("dlgroup_item_select", array(
			"name" => "dlgroup_item_id",
			"options" => $dlgroupItems,
			"property" => "name",
			"selected" => $obj->getType()
		));

		DisplayPlugin::toggle("dlgroup_item_exists", (count($dlgroupItems) > 0));

    	//error
		foreach(array("name","code") as $key){
			$this->addLabel("error_$key", array(
				"text" => (isset($this->errors[$key])) ? $this->errors[$key] : "",
				"visible" => (isset($this->errors[$key]) && strlen($this->errors[$key]))
			));
		}
    }

    function getScripts(){
		$root = SOY2PageController::createRelativeLink("./js/");
		return array(
			$root . "jquery/treeview/jquery.treeview.pack.js",
		);
	}

	function getCSS(){
		$root = SOY2PageController::createRelativeLink("./js/");
		return array(
			$root . "jquery/treeview/jquery.treeview.css",
			$root . "tree.css",
		);
	}
}
