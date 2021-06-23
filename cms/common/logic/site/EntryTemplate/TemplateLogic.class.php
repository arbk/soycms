<?php
SOY2::import("domain.cms.EntryTemplate");
class TemplateLogic extends SOY2LogicBase
{
    public function get()
    {
        if (!$this->isSimpleXmlEnabled()) {
            return array();
        }
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->get();
    }

    public function getByFileName($filename)
    {
        if (!$this->isSimpleXmlEnabled()) {
            return null;
        }
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->getByFileName($filename);
    }

    public function getById($id)
    {
        if (!$this->isSimpleXmlEnabled()) {
            return null;
        }
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->getById($id);
    }

    public function insert(EntryTemplate $data)
    {
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->insert($data);
    }

    public function update(EntryTemplate $data)
    {
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->update($data);
    }

    public function delete($filename)
    {
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->delete($filename);
    }
    public function deleteById($id)
    {
        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        return $dao->deleteById($id);
    }

    public function uploadTemplate($file)
    {
        if ((null===$file)) {
            return false;
        }
        if (!preg_match('/\.xml$/i', $file['name'])) {
            return false;
        }
        if ($file['type'] != 'text/xml') {
            return false;
        }

        $dao = SOY2DAOFactory::create("cms.EntryTemplateDAO");
        if (!EntryTemplateDAO::test($file['tmp_name'])) {
            return false;
        }

        $max = 0;
        foreach ($dao->get() as $template) {
            if ($max < $template->getId()) {
                $max = $template->getId();
            }
        }
        $filename = ($max+1).'.xml';

        if (!@move_uploaded_file($file['tmp_name'], $dao->getTemplateDirectory().'/'.$filename)) {
            return false;
        }

        return true;
    }

    private function isSimpleXmlEnabled()
    {
        return function_exists("simplexml_load_file");
    }
}
