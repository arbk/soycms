<?php

class DetailAction extends SOY2Action
{
    private $adminId;

    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("admin.AdministratorDAO");
        try {
            $data = $dao->getById($this->adminId);
            $this->setAttribute("admin", $data);
            return SOY2Action::SUCCESS;
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
    }
}
