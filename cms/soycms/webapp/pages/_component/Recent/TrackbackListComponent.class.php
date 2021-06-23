<?php

class TrackbackListComponent extends HTMLList
{
    public function populateItem($entity)
    {
        $blog = (isset($entity->info["blog"])) ? $entity->info["blog"] : null;
        $entry = (isset($entity->info["entry"])) ? $entity->info["entry"] : null;

        if ((null===$blog)) {
            $blog = new BlogPage();
        }
        if ((null===$entry)) {
            $entry = new Entry();
        }

        $title = ((strlen($entity->getTitle()) == 0) ? CMSMessageManager::get("SOYCMS_NO_TITLE") : $entity->getTitle());
        $title .= (strlen($entity->getBlogName()) == 0)  ? "" : " (" . $entity->getBlogName() . ")";

        $this->addLink("title", array(
            "link"=>SOY2PageController::createLink("Blog.Trackback." . $blog->getId()),
            "text"=>$title
        ));
        $this->addLabel("content", array(
            "text"=>$entry->getTitle() . " (" . $blog->getTitle() . ")"
        ));
        $this->addLabel("udate", array(
            "text"  => CMSUtil::getRecentDateTimeText($entity->getSubmitDate()),
            "title" => date("Y-m-d H:i:s", $entity->getSubmitDate())
        ));
    }
}
