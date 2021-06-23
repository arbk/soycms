<?php

class DetailAction extends SOY2Action
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.BlockDAO");
        $this->setAttribute("Block", $dao->getById($this->id));
        return SOY2Action::SUCCESS;
    }
}
