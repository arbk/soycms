<?php
class Template
{
    const TEMP_DEFAULT_CODE = '<?xml version="1.0" encoding="' . SOY2::CHARSET . '"?><template></template>';

    private $id;
    private $name;
    private $description;
    private $active;
    private $archieveFileName;
    private $templatesDirectory;

    private $fileList = array();

    /**
     * array
     * key : id
     * ex: blog_template
     *   id = ("entry","top","popup","archive")
     */
    private $template;

    private $pageType;


    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getTemplate()
    {
        return $this->template;
    }
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function addTemplate($template)
    {
        if (is_array($this->template)) {
            $this->template = array_merge($this->template, $template);
        } else {
            $this->template = $template;
        }
    }

    public function getTemplateById($id)
    {
        return @$this->template[$id];
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPageType()
    {
        return $this->pageType;
    }
    public function setPageType($pageType)
    {
        $this->pageType = $pageType;
    }

    public function getActive()
    {
        return $this->active;
    }


    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isActive()
    {
        return (boolean)$this->active;
    }

    public function getFileList()
    {
        return $this->fileList;
    }
    public function setFileList($fileList)
    {
        $this->fileList = $fileList;
    }

    public function getArchieveFileName()
    {
        return $this->archieveFileName;
    }
    public function setArchieveFileName($archieveFileName)
    {
        $this->archieveFileName = $archieveFileName;
    }

    public function getTemplateContent($id = null)
    {

        if ($id) {
            $template = $this->template[$id];
            return @file_get_contents($this->getTemplatesDirectory() . $id);
        }

        $array = array();
        foreach ($this->template as $key => $template) {
            $array[$template["id"]] = @file_get_contents($this->getTemplatesDirectory() . $key);
        }

        return $array;
    }

    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }
    public function setTemplatesDirectory($templatesDirecotry)
    {
        $this->templatesDirectory = $templatesDirecotry;
    }
}
