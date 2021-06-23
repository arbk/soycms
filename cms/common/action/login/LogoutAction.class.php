<?php

class LogoutAction extends SOY2Action
{
    public function execute()
    {
        //
        if (isset($_COOKIE["sc_auto_login"])) {
            $dao = SOY2DAOFactory::create("admin.AutoLoginDAO");
            try {
                $login = $dao->getByToken($_COOKIE["sc_auto_login"]);
                soy2_setcookie("sc_auto_login");
                $dao->deleteByUserId($login->getUserId());
            } catch (Exception $e) {
                //
            }
        }

        UserInfoUtil::logout();

        return SOY2Action::SUCCESS;
    }
}
