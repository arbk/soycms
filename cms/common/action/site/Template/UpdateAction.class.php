<?php
SOY2::import("domain.cms.Template");
class UpdateAction extends SOY2Action
{
    private $id;
    private $file;

    protected function execute(SOY2ActionRequest &$request, SOY2ActionForm &$form, SOY2ActionResponse &$response)
    {
        if ($form->hasError()) {
            return SOY2Action::FAILED;
        }

        $logic = SOY2Logic::createInstance("logic.site.Template.TemplateLogic");

        try {
            $template = $logic->getById($this->id);
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }

        $fname = $template->getTemplatesDirectory().$this->file;

        $result = file_put_contents($fname, $form->template);

        if ($result === false) {
            return SOY2Action::FAILED;
        } else {
            @chmod($fname, F_MODE_FILE);
            return SOY2Action::SUCCESS;
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
    public function getFile()
    {
        return $this->file;
    }
    public function setFile($file)
    {
        $this->file = $file;
    }
}

class UpdateActionForm extends SOY2ActionForm
{
    public $template;

    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
