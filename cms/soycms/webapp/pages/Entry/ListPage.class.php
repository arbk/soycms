<?php
class ListPage extends CMSUpdatePageBase{
	
	protected $labelIds;
	protected $isShowDisplayOrder = true;
	
	private $offset;
	private $limit;
	
	function doPost(){
		
		$query_str = "?offset=".$this->offset."&limit=".$this->limit;
		
		switch($_POST['op_code']){
			case 'delete':
				//削除実行
		    	$result = $this->run("Entry.RemoveAction");
		    	if($result->success()){
		    		$this->addMessage("ENTRY_REMOVE_SUCCESS");
		    	}else{
		    		$this->addErrorMessage("ENTRY_REMOVE_FAILED");
		    	}
		    	$this->jump("Entry.List.".implode(".",$this->labelIds).$query_str);
				break;
			case 'copy':
				//複製実行
		    	$result = $this->run("Entry.CopyAction");
		    	if($result->success()){
		    		$this->addMessage("ENTRY_COPY_SUCCESS");
		    	}else{
		    		$this->addErrorMessage("ENTRY_COPY_FAILED");
		    	}
		    	$this->jump("Entry.List.".implode(".",$this->labelIds).$query_str);
				break;
			case 'setPublish':
				//公開状態にする
				$result =$this->run("Entry.PublishAction",array('publish'=>true));
				if($result->success()){
					$this->addMessage("ENTRY_PUBLISH_SUCCESS");	
				}else{
					$this->addErrorMessage("ENTRY_PUBLISH_FAILED");
				}
				$this->jump("Entry.List.".implode(".",$this->labelIds).$query_str);
		    	break;
			case 'setnonPublish':
				//非公開状態にする
				$result = $this->run("Entry.PublishAction",array('publish'=>false));
				if($result->success()){
					$this->addMessage("ENTRY_NONPUBLISH_SUCCESS");
				}else{
					$this->addErrorMessage("ENTRY_NONPUBLISH_FAILED");
				}
				$this->jump("Entry.List.".implode(".",$this->labelIds).$query_str);
		    	break;
			case 'update_display':
				//表示順が押された（と判断してるけど）
				$result = $this->run("EntryLabel.UpdateDisplayOrderAction");		
				
				if($result->success()){
					$this->addMessage("ENTRYLABEL_DISPLAYORDER_MODIFY_SUCCESS");
					$this->jump("Entry.List.".implode(".",$this->labelIds).$query_str);
				}else{
					$this->addErrorMessage("ENTRYLABEL_DISPLAYORDER_MODIFY_FAILED");
					$this->jump("Entry");
				}
				break;
		}
		exit;
		
	}
	
	/**
	 * クッキーに保存
	 */
	function updateCookie($labelIds){
		$timeout = 0;
		$path = "/";

		//Entry_List
		$cookieName = "Entry_List";
		$value = implode('.',$labelIds);
		setcookie($cookieName,$value,$timeout,$path);

		//Entry_List_Limit
		if(isset($_GET['limit'])){
			$cookieName = "Entry_List_Limit";
			$value = $_GET['limit'];
			setcookie("Entry_List_Limit",$value,$timeout,$path);
		}
	}
	
	function ListPage($arg){
		
		$offset = isset($_GET['offset'])? (int)$_GET['offset'] : 0 ;
		$limit  = isset($_GET['limit'])? (int)$_GET['limit'] : ( isset($_COOKIE['Entry_List_Limit'])? (int)$_COOKIE['Entry_List_Limit'] : 10 );
		
		$this->offset = $offset;
		$this->limit  = $limit;
		
		$labelIds = isset($arg) ? $arg : array();
		$this->labelIds = $labelIds;
		
		$this->updateCookie($labelIds);
		
		WebPage::WebPage();
		
		//IDが0だった場合はIndexへ
		if(count($this->labelIds)<1){
			$this->jump("Entry"."?offset=".$this->offset."&limit=".$this->limit);
		}
		
		//ラベル一覧を取得
		$labelList = $this->getLabelList();
		
		//自分自身へのリンク
		$currentLink = SOY2PageController::createLink("Entry.List") . "/". implode("/",$labelIds);
		
		//無効なラベルIDを除く
		foreach($this->labelIds as $key => $value){
			if(!is_numeric($value))unset($this->labelIds[$key]);
		}
		
		//記事を取得
		list($entries,$count,$offset) = $this->getEntries($offset,$limit,$this->labelIds);		
		
		//include_once(dirname(__FILE__).'/_EntryBlankPage.class.php');
		$this->createAdd("no_entry_message","Entry._EntryBlankPage",array(
			"visible"=>(count($entries) == 0),
			"labelIds" => $this->labelIds
		));
			
		if(count($entries) > 0){
			//do nothing
		}else{
			DisplayPlugin::hide("must_exist_entry");
		}		
		
		
		//記事一覧の表を作成
		$this->createAdd("list","LabeledEntryList",array(
				"labelIds"  => $this->labelIds,
				"labelList" => $labelList,
				"list"      => $entries,
		));
		
		$labelState = array();
		$url = SOY2PageController::createLink("Entry.List");		
		foreach($this->labelIds as $labelId){
			if(!isset($labelList[$labelId]))continue;
			$label = $labelList[$labelId];
			$url .= "/".$label->getId();
			$url = htmlspecialchars($url, ENT_QUOTES, "UTF-8");
			$caption = $label->getDisplayCaption();
			$labelState[] = "<a href=\"{$url}\">{$caption}</a>";
		}
		$this->createAdd("label_state","HTMLLabel",array(
			"html" => implode("&gt;",$labelState)
		));

		
		//子ラベルを取得
		$labels = $this->getNarrowLabels();

		//子ラベル表示領域のキャプション
		$this->createAdd("sublabel_list_caption","HTMLModel",array(
			"visible" => count($labels)
		));
		
		//子ラベルボタンを作成
		$this->createAdd("sublabel_list","SubLabelList",array(
			"list" => $labels,
			"labelList" => $labelList,
			"currentLink" => $currentLink
		));
		
		
		
		//戻るリンクを作成
		$this->createAdd("back_link","HTMLLink",array(
			"link" => SOY2PageController::createLink("Entry.List") . "/" .implode("/",array_slice($labelIds,0,count($labelIds)-1))
		));
		
		//新規作成リンクを作成（公開記事一覧などでは表示しない）
		$this->createAdd("create_link_box","HTMLModel",array(
			"visible" => isset($labelIds[0]) AND is_numeric($labelIds[0])
		));
		$this->createAdd("create_link","HTMLLink",array(
			"link" => SOY2PageController::createLink("Entry.Create") . "/" . implode("/",$labelIds),
		));
		
		
		//ページャーを作成
		$this->createAdd("topPager","EntryPagerComponent",array(
			"arguments"=> array($offset, $limit, $count, $currentLink)
		));
		
		//記事テーブルのCSS
		HTMLHead::addLink("entrytree",array(
			"rel" => "stylesheet",
			"type" => "text/css",
			"href" => SOY2PageController::createRelativeLink("./css/entry/entry.css")
		));
		
		$this->createAdd("showCount10" ,"HTMLLink",array("link"=> $currentLink ."?limit=10"));
		$this->createAdd("showCount20" ,"HTMLLink",array("link"=> $currentLink ."?limit=20"));
		$this->createAdd("showCount50" ,"HTMLLink",array("link"=> $currentLink ."?limit=50"));
		$this->createAdd("showCount100","HTMLLink",array("link"=> $currentLink ."?limit=100"));
		$this->createAdd("showCount500","HTMLLink",array("link"=> $currentLink ."?limit=500"));
		
		//フォーム
		$this->addForm("index_form",array(
			"action" => $currentLink."?limit=".$this->limit."&offset=".$this->offset
		));
		
		//表示順更新ボタンの追加
		$this->createAdd("display_order_submit","HTMLInput",array(
			"name" => "display_order_submit",
			"value"=>CMSMessageManager::get("SOYCMS_DISPLAYORDER"),
			"type" => "submit",
			"tabindex" => LabeledEntryList::$tabIndex++
		));
	
		//削除ボタンの追加
		$this->createAdd("remove_submit","HTMLInput",array(
			"name" => "remove_submit",
			"value"=>CMSMessageManager::get("SOYCMS_DELETE_ENTRY"),
			"type" => "submit"
		));
		
		if(count($this->labelIds) == 0 || !is_numeric(implode("",$this->labelIds))){
			HTMLHead::addScript("parameters",array(
				"lang"=>"text/JavaScript",
				"script"=>'var listPanelURI = "'.SOY2PageController::createLink("Entry.ListPanel").'"'
			));
			
			$this->createAdd("search_link","HTMLModel",array(
				"href"=>SOY2PageController::createLink("Entry.Search")
			));
		}else{
			HTMLHead::addScript("parameters",array(
				"lang"=>"text/JavaScript",
				"script"=>'var listPanelURI = "'.SOY2PageController::createLink("Entry.ListPanel.".implode('.',$this->labelIds)).'"'
			));
			$this->createAdd("search_link","HTMLModel",array(
				"href"=>SOY2PageController::createLink("Entry.Search.".implode('.',$this->labelIds))
			));
		}
			
		//操作用のJavaScript
		HTMLHead::addScript("entry_list",array(
			"type" => "text/javascript",
			"script"=> file_get_contents(dirname(__FILE__)."/script/entry_list.js")
		));
		
		//トップラベル以外は表示順更新を消す
		if(count($this->labelIds) >= 2){
			DisplayPlugin::hide("no_label");
		}
		
		if(!UserInfoUtil::hasEntryPublisherRole()){
			DisplayPlugin::hide("publish");
			DisplayPlugin::hide("no_label");
		}
	}
	
	/**
	 * 複数ラベルを指定して記事を取得
	 * ラベルがnullの時は、すべての記事を表示させる
	 * @param $offset,$limit,$labelId
	 * @return (entry_array,記事の数,大きすぎた場合最終オフセット)
	 */
	function getEntries($offset,$limit,$labelIds){
		//ラベルIDに数字以外が含まれていたらアウト
		foreach($labelIds as $labelId){
			if(!is_numeric($labelId))return array(array(),0,0);
		}
		
		$action = SOY2ActionFactory::createInstance("Entry.EntryListAction",array(
			"ids"=>$labelIds,
			"offset"=>$offset,
			"limit"=>$limit
		));
		$result = $action->run();
		$entities = $result->getAttribute("Entities");
		$totalCount = $result->getAttribute("total");
		
		return array($entities,$totalCount,min($offset,$totalCount));
	}
	
	
	/**
	 * ラベルをすべて取得
	 * 記事管理者は制限有り
	 */
	function getLabelList(){
		$action = SOY2ActionFactory::createInstance("Label.LabelListAction");
    	$result = $action->run();
    	
    	if($result->success()){
    		$list = $result->getAttribute("list");
    		return $list;
    	}else{
    		return array();
    	}
	}
	
	/**
	 * 子ラベルを取得
	 */
	function getNarrowLabels(){
		return $this->run("EntryLabel.NarrowLabelListAction",array(
			"labelIds" => $this->labelIds
		))->getAttribute("labels");
	}

}

class LabeledEntryList extends HTMLList{
	
	static $tabIndex = 0;
	
	private $labelIds;
	private $labelList;
	
	private $logic;
	
	function setLabelIds($labelIds){
		$this->labelIds = $labelIds;
	}
	
	function setLabelList($list){
		$this->labelList = $list;
	}
	
	function populateItem($entity){
		
		$this->createAdd("entry_check","HTMLInput",array(
			"type"=>"checkbox",
			"name"=>"entry[]",
			"value"=>$entity->getId()
		));
		
		$entity->setTitle(strip_tags($entity->getTitle()));
		$title_link = SOY2HTMLFactory::createInstance("HTMLLink",array(
			"text"=>((strlen($entity->getTitle())==0)?CMSMessageManager::get("SOYCMS_NO_TITLE"):$entity->getTitle()),
			"link"=>SOY2PageController::createLink("Entry.Detail.".$entity->getId()),
			"title"=>$entity->getTitle()
		));
		
		$this->add("title",$title_link);

		$status = SOY2HTMLFactory::createInstance("HTMLLabel", array(
			"text" => $entity->getStateMessage()
		));
		
		$this->add("status", $status);
		
		$this->createAdd("content","HTMLLabel",array(
			"text"  => mb_strimwidth(SOY2HTML::ToText($entity->getContent()),0,100,"..."),
			"title" => mb_strimwidth(SOY2HTML::ToText($entity->getContent()),0,1000,"..."),
		));
		
		
		
		$this->createAdd("create_date","HTMLLabel",array(
			"text"  => CMSUtil::getRecentDateTimeText($entity->getCdate()),
			"title" => date("Y-m-d H:i:s", $entity->getCdate())
		));
		
		if(!$this->logic) $this->logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
		$this->createAdd("order","HTMLInput",array(
			"type"     => "text",
			"name"     => ( ( count($this->labelIds) >0 ) ? "displayOrder[".$entity->getId()."][".$this->labelIds[0]."]" : "" ),
			"value"    => ( ( count($this->labelIds) >0 ) ? $this->logic->getDisplayOrder($entity->getId(),$this->labelIds[0]) : "" ),
			"size"     => "5",
			"tabindex" => self::$tabIndex++
		));	
		
		//ラベル表示部
		$this->createAdd("label","LabelList",array(
			"list" => $this->labelList,
			"entryLabelIds"=>$entity->getLabels(),
		));
		
	}
}

class LabelList extends HTMLList{

	var $entryLabelIds = array();
	
	function setEntryLabelIds($list){
		if(is_array($list)){
			$this->entryLabelIds = $list;
		}
	}
	
	protected function populateItem($label){
		$this->createAdd("entry_list_link","HTMLLink",array(
			"link" => SOY2PageController::createLink("Entry.List.".$label->getId()),
			"text" => "[".$label->getCaption()."]",
			"visible" => in_array($label->getId(), $this->entryLabelIds)
			
		));
	}
}


class SubLabelList extends HTMLList{
	var $labelList;
	var $currentLink;
	
	function setCurrentLink($link){
		$this->currentLink = $link;
	}
	
	function setLabelList($list){
		$this->labelList = $list;
	}
	
	protected function populateItem($labelId){
		
		$visible = array_key_exists($labelId, $this->labelList);
		
		if($visible){
			$label = $this->labelList[$labelId];
		}
		
		if(!$visible OR !$label instanceof Label){
			$label = new Label();
		}
		
		$this->createAdd("label_link","HTMLLink",array(
			"title" => $label->getCaption(),
			"link" => $this->currentLink ."/".$label->getId(),
		));
		
		$this->createAdd("label_icon","HTMLImage",array(
			"src" => $label->getIconUrl(),
			"visible" => $visible,
		));
		
		$this->createAdd("label_caption","HTMLLabel",array(
			"text" => $label->getCaption(),
			"visible" => $visible,
		));
	}	
}
?>