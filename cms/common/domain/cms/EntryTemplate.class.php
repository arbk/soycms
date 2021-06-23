<?php

class EntryTemplate
{
    const TEMP_DEFAULT_CODE = '<?xml version="1.0" encoding="' . SOY2::CHARSET . '"?><entryTemplate></entryTemplate>';

    private $id;
    private $name;
    private $description;
    private $templates;
    private $labelRestrictionPositive = array();

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
    public function getTemplates()
    {
        return $this->templates;
    }
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }
    public function getStyle()
    {
        return $this->templates["style"];
    }
    public function getLabelId()
    {
        return @$this->templates["labelId"];
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLabelRestrictionPositive()
    {
        return (array)$this->labelRestrictionPositive;
    }
    public function setLabelRestrictionPositive($array)
    {
        $this->labelRestrictionPositive = $array;
    }
}
