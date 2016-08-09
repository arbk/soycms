<?php

class IndexPage extends WebPage{

    function __construct() {
	
		$logic = SOY2Logic::createInstance("logic.CalendarLogic");
    	
    	WebPage::WebPage();
    	
    	$this->createAdd("current_calendar","HTMLLabel",array(
    		"html" => $logic->getCurrentCalendar(true)
    	));
    	
    	$this->createAdd("next_calendar","HTMLLabel",array(
    		"html" => $logic->getNextCalendar(true)
    	));
    	
    }
}
?>