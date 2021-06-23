<?php

class ChangeDefaultsAction extends SOY2Action
{
    private $pageId;

    public function execute($request, $form, $response)
    {
        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        try {
            $page = $dao->getById($this->pageId);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        $page->setDefaultAcceptTrackback($form->default_accept);

        try {
            $dao->updatePageConfig($page);
            return SOY2Action::SUCCESS;
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
    }

    public function getPageId()
    {
        return $this->pageId;
    }
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
}

class ChangeDefaultsActionForm extends SOY2ActionForm
{
    public $default_accept;

    public function getDefault_accept()
    {
        return $this->default_accept;
    }
    public function setDefault_accept($default_accept)
    {
        $this->default_accept = $default_accept;
    }
}
