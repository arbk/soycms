<?php

SOY2::import("module.plugins.yayoi_order_csv.component.CalendarBaseComponent");
class BuildCalendarLogic extends CalendarBaseComponent{
	
	private $outputDateList = array();
	private $y;
	private $m;
	
	function __construct(){}
	
	function build($y, $m, $dspOtherMD = true, $dspCaption = true, $dspRegHol = false){
		$this->y = $y;
		$this->m = $m;
		return parent::build($y, $m, $dspOtherMD, $dspCaption, $dspRegHol);
	}
	
	function handleFunc($i, $cd, $wc, $da, $isOtherMonth){
		$html = array();
		$html[] = $i;
		
		$ts = mktime(0, 0, 0, $this->m, $i, $this->y);
		
		/** 本日より後の日ではチェックボックスを表示しない **/
		
		if($ts < time()){
			$html[] = '<input type="checkbox" name="day[]" value="' . $i . '">';
		}
		
		if(in_array($ts, $this->outputDateList)){
			$html[] = '<br><span style="color:#FF0000;">出力済み</span>';
		}
		
		return implode("\n", $html);
	}
	
	function setOutputDateList($outputDateList){
		$this->outputDateList = $outputDateList;
	}
}
?>