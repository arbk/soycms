<?php

class LabelDetailAction extends SOY2Action
{
    private $id;

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.LabelDAO");
        try {
            $this->setAttribute("label", $dao->getById($this->id));
            return SOY2Action::SUCCESS;
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
}
