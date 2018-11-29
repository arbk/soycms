<?php

class CommentListComponent extends HTMLList{

	function populateItem($entity){
		$blog = (isset($entity->info["blog"])) ? $entity->info["blog"] : null;
		$entry = (isset($entity->info["entry"])) ? $entity->info["entry"] : null;

		if(is_null($blog)) $blog = new BlogPage();
		if(is_null($entry)) $entry = new Entry();

		$title = ((strlen($entity->getTitle())==0) ? CMSMessageManager::get("SOYCMS_NO_TITLE") : $entity->getTitle());
		$title .= strlen($entity->getAuthor()) == 0  ? "" : " (" . $entity->getAuthor() . ")";

		$this->addLink("title", array(
			"link"=>SOY2PageController::createLink("Blog.Comment." . $blog->getId()),
			"text"=>$title
		));

		$this->addLabel("content", array(
			"text"=>$entry->getTitle() . " (" . $blog->getTitle() . ")"
		));
		$this->addLabel("udate", array(
			"text"=>CMSUtil::getRecentDateTimeText($entity->getSubmitDate()),
			"title" => date("Y-m-d H:i:s", $entity->getSubmitDate())
		));
	}
}