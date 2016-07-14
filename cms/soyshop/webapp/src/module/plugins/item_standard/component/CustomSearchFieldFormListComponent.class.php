<?php

class CustomSearchFieldFormListComponent extends HTMLList {
	
	private $conditions;
	
	protected function populateItem($entity, $key){
		
		$this->addLabel("label", array(
			"text" => (isset($entity["label"])) ? $entity["label"] : ""
		));
		
		$html = array();
		if(isset($entity["type"])){
			switch($entity["type"]){
				case CustomSearchFieldUtil::TYPE_CHECKBOX:
					$opts = explode("\n", $entity["option"]);
					foreach($opts as $opt){
						$o = trim($opt);
						if(in_array($o, $this->conditions[$key])){
							$html[] = "<label><input type=\"checkbox\" name=\"search_condition[csf][" . $key . "][]\" value=\"" . $o . "\" checked=\"checked\">". $o. "</label>";
						}else{
							$html[] = "<label><input type=\"checkbox\" name=\"search_condition[csf][" . $key . "][]\" value=\"" . $o . "\">". $o. "</label>";
						}
					}
					break;
			}
		}
		
		$this->addLabel("form", array(
			"html" => implode("\n", $html)
		));
		
		if(!count($html)) return false;
	}
	
	function setConditions($conditions){
		$this->conditions = $conditions;
	}
}