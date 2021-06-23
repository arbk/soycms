<?php
class Plugin
{
    private $id;
    private $name;
    private $description;
    private $isActive;
    private $config;
    private $custom;
    private $category;
    private $author;
    private $version;
    private $url;
    private $mail;
    private $icon;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

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

    public function isActive()
    {
        if ((null===$this->isActive)) {
            $this->isActive = CMSPlugin::activeCheck($this->id);
        }
        return $this->isActive;
    }
    public function getIsActive()
    {
        return $this->isActive;
    }
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getConfig()
    {
        return $this->config;
    }
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getCustom()
    {
        return $this->custom;
    }
    public function setCustom($custom)
    {
        $this->custom = $custom;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
    public function getCategory()
    {
        if ((null===$this->category)) {
            $dao = SOY2DAOFactory::create("cms.PluginDAO");
            $list = $dao->getCategoryArray();
    //    $list = PluginDAO::getCategoryArray();
            foreach ($list as $category => $ids) {
                if (in_array($this->id, $ids)) {
                    $this->category = $category;
                }
            }
        } else {
        }
        return $this->category;
    }

    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getVersion()
    {
        return $this->version;
    }
    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getMail()
    {
        return $this->mail;
    }
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    public function getIcon()
    {
        return 0 < strlen($this->icon)
            ? soy2_path2url($this->icon)
            : SOY2PageController::createRelativeLink("./image/icon/default_plugin_icon.png");

//      //アイコン設定
//      $prefix =  SOY2PageController::createRelativeLink("../common/site_include/plugin/".$this->getId()."/icon");
//
//      $dir = SOY2::RootDir()."site_include/plugin/".$this->getId()."/icon";
//
//      if(file_exists($dir.".jpg")){
//          return $prefix.".jpg";
//      }else if(file_exists($dir.".png")){
//          return $prefix.".png";
//      }else if(file_exists($dir.".gif")){
//          return $prefix.".gif";
//      }
//
//      return SOY2PageController::createRelativeLink("./image/icon/default_plugin_icon.png");
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
