<?php

class TemplateDownloadAction extends SOY2Action
{
    private $id;

    public function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if (null===$this->id) {
            return SOY2Action::FAILED;
            exit;
        } else {
            $logic = SOY2Logic::createInstance("logic.site.Template.TemplateLogic");
            $template = $logic->getById($this->id);

            if (file_exists($template->getArchieveFileName())) {
                $fname = basename($template->getArchieveFileName());

                header('Content-Disposition: attachment;filename='.$fname.';');
                echo file_get_contents($template->getArchieveFileName());
            } else {
                //404
                header("HTTP/1.0 404 Not Found");
            }
            exit;
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
