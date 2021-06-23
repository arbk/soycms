<?php

class LabelListComponent extends HTMLList
{
    public function populateItem($entity)
    {
        $this->addLabel("label_name", array(
        "text"  =>  $entity->getBranchName(),
//      "style"=> "color:#" . sprintf("%06X",$entity->getColor()).";" ."background-color:#" . sprintf("%06X",$entity->getBackgroundColor()).";",
        "title" => $entity->getBranchName(),
        ));

        $this->addLabel("label_entries_count", array(
        "text" => ( (int)$entity->getEntryCount())
        ));

        $this->addImage("label_icon", array(
        "src" => $entity->getIconUrl(),
        "title" => $entity->getBranchName(),
        "alt" => "",
        ));

        $this->addLabel("label_description", array(
        "html" => nl2br(soy2_h(self::trimDescription($entity->getDescription()))),
        "title" => $entity->getDescription()
        ));

        $this->addLink("detail_link_01", array(
        "title" => $entity->getCaption()." (".$entity->getEntryCount().")",
        "link"  => SOY2PageController::createLink("Entry.List")."/".$entity->getId()
        ));

        $this->addLink("create_link", array(
        "link" => SOY2PageController::createLink("Entry.Create") . "/" . $entity->getId()
        ));
    }

    private function trimDescription($str)
    {
        return mb_strimwidth($str, 0, 96);
    }
}
