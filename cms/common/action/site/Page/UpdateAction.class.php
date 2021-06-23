<?php
SOY2::import("action.site.Page.PageActionForm");
/**
 * ページの更新を行います
 */
class UpdateAction extends SOY2Action
{
    /**
     * ページID
     */
    private $id;
    private $dao;
    private $updateConfig = false;

    public function setId($id)
    {
        $this->id = $id;
    }

    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if ($form->hasError()) {
            foreach ($form as $key => $value) {
                $this->setErrorMessage($key, $form->getErrorString($key));
            }
            return SOY2Action::FAILED;
        }

        $this->dao = SOY2DAOFactory::create("cms.PageDAO");
        $entity = $this->dao->getById($this->id);
        $old = $entity;

        SOY2::cast($entity, $form);
        $entity->setId($this->id);


        //ループしていないかチェック
        if ($this->checkParentPageId($entity, $entity->getId()) != true) {
            $this->setErrorMessage("failed", "ページがループしています");
            return SOY2Action::FAILED;
        }

        //CMS:PLUGIN callEventFunction
        CMSPlugin::callEventFunc('onPageUpdate', array("new_page"=>$entity,"old_page"=>$old));

        $logic = SOY2Logic::createInstance("logic.site.Page.PageLogic");

        try {
            $logic->update($entity);
            if ($this->updateConfig) {
                $logic->updatePageConfig($entity);
            }

            CMSUtil::notifyUpdate();
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        return SOY2Action::SUCCESS;
    }

    public function getActionFormName()
    {
        return "PageActionForm";
    }

    public function checkParentPageId($entity, $checkId)
    {
        //OK
        if ($entity->getParentPageId() == null) {
            return true;
        }

        //NG
        if ($entity->getParentPageId() == $checkId) {
            return false;
        }

        return $this->checkParentPageId($this->dao->getById($entity->getParentPageId()), $checkId);
    }

    public function getUpdateConfig()
    {
        return $this->updateConfig;
    }
    public function setUpdateConfig($updateConfig)
    {
        $this->updateConfig = $updateConfig;
    }
}
