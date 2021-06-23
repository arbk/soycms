<?php

class MultiEntryListComponent extends HTMLList
{
    private $url = array();
     private $blogId = array();
    private $blogTitle = array();
    private $blogUrl = array();
    private $blogCategoryUrl = array();
    private $outputItems;

    private $dsn;

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setBlogId($blogId)
    {
        $this->blogId = $blogId;
    }

    public function setBlogTitle($blogTitle)
    {
        $this->blogTitle = $blogTitle;
    }

    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
    }

    public function setBlogCategoryUrl($blogCategoryUrl)
    {
        $this->blogCategoryUrl = $blogCategoryUrl;
    }

    public function getStartTag()
    {

        return parent::getStartTag();
    }

    /**
     * 実行前後にDSNの書き換えを実行
     */
    public function execute()
    {
        $this->outputItems = $this->getAttribute("cms:items");

        if ($this->dsn) {
            $old = SOY2DAOConfig::Dsn($this->dsn);
        }

        parent::execute();

        if ($this->dsn) {
            SOY2DAOConfig::Dsn($old);
        }
    }

    protected function createAddIfOutputItem($id, $className, $array = array())
    {
        if (!CMSUtil::isEntryListItems($this->outputItems, $id)) {
            return;
        }
        $this->createAdd($id, $className, $array);
    }

    protected function populateItem($entity)
    {
        //entry title
        $id = (is_numeric($entity->getId())) ? (int)$entity->getId() : 0;
        $url = (isset($this->url[$id])) ? $this->url[$id] : "" ;

        $hTitle = soy2_h($entity->getTitle());
        $entryUrl = ( strlen($url) > 0 ) ? $url.rawurlencode($entity->getAlias()) : "" ;

        if (strlen($entryUrl) > 0) {
            $hTitle = "<a href=\"".soy2_h($entryUrl)."\">".$hTitle."</a>";
        }

        //blog title
        $blogId = (isset($this->blogId[$id])) ? (int)$this->blogId[$id] : 0;
        $blogUrl = (isset($this->blogUrl[$id])) ? $this->blogUrl[$id] : "";
//      $blogCategoryUrl = (isset($this->blogCategoryUrl[$id])) ? $this->blogCategoryUrl[$id] : ""; // TODO
        $blogTitle = (isset($this->blogTitle[$id])) ? $this->blogTitle[$id] : "";
        $hBlogTitle = soy2_h($blogTitle);

        if (strlen($blogUrl) > 0) {
            $hBlogTitle = "<a href=\"".soy2_h($blogUrl)."\">".$hBlogTitle."</a>";
        }



        $this->createAddIfOutputItem("entry_id", "CMSLabel", array(
            "text" => $id,
            "soy2prefix" => "cms"
        ));

        $this->createAddIfOutputItem("title", "CMSLabel", array(
            "html" => $hTitle,
            "soy2prefix" => "cms"
        ));
        $this->createAddIfOutputItem("content", "CMSLabel", array(
            "html" => $entity->getContent(),
            "soy2prefix" => "cms"
        ));

        $this->createAddIfOutputItem("more", "CMSLabel", array(
            "html" => $entity->getMore(),
            "soy2prefix" => "cms"
        ));

        $this->createAddIfOutputItem("create_date", "DateLabel", array(
            "text" => $entity->getCdate(),
            "soy2prefix" => "cms"
        ));

        $this->createAddIfOutputItem("create_time", "DateLabel", array(
            "text" => $entity->getCdate(),
            "soy2prefix" => "cms",
            "defaultFormat"=>"H:i"
        ));

        //entry_link追加
        $this->createAddIfOutputItem("entry_link", "HTMLLink", array(
            "link" => $entryUrl,
            "soy2prefix" => "cms"
        ));

        //リンクの付かないタイトル 1.2.6～
        $this->createAddIfOutputItem("title_plain", "CMSLabel", array(
            "text" =>  $entity->getTitle(),
            "soy2prefix" => "cms"
        ));

        //1.2.7～
        $this->createAddIfOutputItem("more_link", "HTMLLink", array(
            "soy2prefix" => "cms",
            "link" => $entryUrl ."#more",
            "visible"=>(strlen($entity->getMore()) != 0)
        ));
        $this->createAddIfOutputItem("more_link_no_anchor", "HTMLLink", array(
            "soy2prefix" => "cms",
            "link" => $entryUrl,
            "visible"=>(strlen($entity->getMore()) != 0)
        ));

        //Blog Title link
        $this->createAddIfOutputItem("blog_title", "CMSLabel", array(
            "html" => $hBlogTitle,
            "soy2prefix" => "cms"

        ));

        //Blog Title plain
        $this->createAddIfOutputItem("blog_title_plain", "CMSLabel", array(
            "text" => $blogTitle,
            "soy2prefix" => "cms"
        ));

        //Blog link
        $this->createAddIfOutputItem("blog_link", "HTMLLink", array(
            "link" => $blogUrl,
            "soy2prefix" => "cms"
        ));

        //1.7.5~
        $this->createAddIfOutputItem("update_date", "DateLabel", array(
            "text" => $entity->getUdate(),
            "soy2prefix" => "cms",
        ));

        $this->createAddIfOutputItem("update_time", "DateLabel", array(
            "text" => $entity->getUdate(),
            "soy2prefix" => "cms",
            "defaultFormat"=>"H:i"
        ));

        $this->createAddIfOutputItem("entry_url", "HTMLLabel", array(
            "text" => $entryUrl,
            "soy2prefix" => "cms",
        ));

//    //カテゴリ  // TODO
//      $this->createAdd("category_list","CategoryListComponent",array(
//          "list" => (is_numeric($blogId) && $blogId > 0 && strlen($blogCategoryUrl)) ? self::_labelLogic()->getLabelsByBlogPageIdAndEntryId($blogId, $id) : array(),
//          "categoryUrl" => $blogCategoryUrl,
//          "entryCount" => array(),
//          "soy2prefix" => "cms"
//      ));

        CMSPlugin::callEventFunc('onEntryOutput', array("entryId" => $id, "SOY2HTMLObject" => $this, "entry" => $entity));
    }

//  private function _labelLogic(){
//      static $logic;
//      if((null===$logic)) $logic = SOY2Logic::createInstance("logic.site.Label.LabelLogic");
//      return $logic;
//  }

    public function getDsn()
    {
        return $this->dsn;
    }
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }
}
