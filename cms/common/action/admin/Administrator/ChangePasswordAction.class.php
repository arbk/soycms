<?php
class ChangePasswordAction extends SOY2Action
{
    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        $this->setAttribute("form", $form);

        if ($form->hasError()) {
            foreach ($form as $key => $value) {
                if ($form->isError($key)) {
                    $this->setErrorMessage($key, $form->getErrorString($key));
                }
            }
            return SOY2Action::FAILED;
        }

        $userid = $this->getUserSession()->getAttribute('userid');
        $logic = SOY2Logic::createInstance("logic.admin.Administrator.AdministratorLogic");
        //パスワードのチェック
        if (!$logic->checkUserIdAndPassword($userid, $form->oldPassword)) {
            return SOY2Action::FAILED;
        }

        if ($form->newPassword == $form->newPasswordConfirm) {
            $result = $logic->updateAdministratorPassword($userid, $form->newPassword);
            if (!$result) {
                return SOY2Action::FAILED;
            }

            return SOY2Action::SUCCESS;
        } else {
            return SOY2Action::FAILED;
        }
    }
}

class ChangePasswordActionForm extends SOY2ActionForm
{
    public $oldPassword; //  元のパスワード
    public $newPassword; //新しいパスワード
    public $newPasswordConfirm;  //新しいパスワードの入力確認

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @validator string { "require" : true }
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @validator string {"max" : 30, "min" : 6, "require" : true }
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }
    public function getNewPasswordConfirm()
    {
        return $this->newPasswordConfirm;
    }

    /**
     * @validator string {"max" : 30, "min" : 6, "require" : true }
     */
    public function setNewPasswordConfirm($newPasswordConfirm)
    {
        $this->newPasswordConfirm = $newPasswordConfirm;
    }
}
