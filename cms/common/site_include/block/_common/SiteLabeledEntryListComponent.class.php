<?php

class SiteLabeledEntryListComponent extends HTMLList
{
    private $isStickUrl;
    private $articlePageUrl;
    private $categoryPageUrl;
    private $blogPageId;
    private $outputItems;
    private $dsn = false;

    public function setIsStickUrl($flag)
    {
        $this->isStickUrl = $flag;
    }

    public function setArticlePageUrl($articlePageUrl)
    {
        $this->articlePageUrl = $articlePageUrl;
    }

    public function setCategoryPageUrl($categoryPageUrl)
    {
        $this->categoryPageUrl = $categoryPageUrl;
    }

    public function setBlogPageId($id)
    {
        $this->blogPageId = $id;
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
        $id = (is_numeric($entity->getId())) ? (int)$entity->getId() : 0;
        $hTitle = soy2_h($entity->getTitle());
        $entryUrl = $this->articlePageUrl.rawurlencode($entity->getAlias());

        if ($this->isStickUrl) {
            $hTitle = "<a href=\"" . soy2_h($entryUrl) . "\">" . $hTitle . "</a>";
        }

        $this->createAddIfOutputItem("entry_id", "CMSLabel", array(
        "text"=> $id,
        "soy2prefix"=>"cms"
        ));

        $this->createAddIfOutputItem("title", "CMSLabel", array(
        "html"=> $hTitle,
        "soy2prefix"=>"cms"
        ));
        $this->createAddIfOutputItem("content", "CMSLabel", array(
        "html"=>$entity->getContent(),
        "soy2prefix"=>"cms"
        ));

        $this->createAddIfOutputItem("more", "CMSLabel", array(
        "html"=>$entity->getMore(),
        "soy2prefix"=>"cms"
        ));

        $this->createAddIfOutputItem("create_date", "DateLabel", array(
        "text"=>$entity->getCdate(),
        "soy2prefix"=>"cms"
        ));

        $this->createAddIfOutputItem("create_time", "DateLabel", array(
        "text"=>$entity->getCdate(),
        "soy2prefix"=>"cms",
        "defaultFormat"=>"H:i"
        ));

        //entry_link追加
        $this->createAddIfOutputItem("entry_link", "HTMLLink", array(
        "link" => $entryUrl,
        "soy2prefix"=>"cms"
        ));

        //リンクの付かないタイトル 1.2.6～
        $this->createAddIfOutputItem("title_plain", "CMSLabel", array(
        "text"=> $entity->getTitle(),
        "soy2prefix"=>"cms"
        ));

        //1.2.7～
        $this->createAddIfOutputItem("more_link", "HTMLLink", array(
        "soy2prefix"=>"cms",
        "link" => $entryUrl ."#more",
        "visible"=>(strlen($entity->getMore()) != 0)
        ));
        $this->createAddIfOutputItem("more_link_no_anchor", "HTMLLink", array(
        "soy2prefix"=>"cms",
        "link" => $entryUrl,
        "visible"=>(strlen($entity->getMore()) != 0)
        ));

        //1.7.5~
        $this->createAddIfOutputItem("update_date", "DateLabel", array(
        "text"=>$entity->getUdate(),
        "soy2prefix"=>"cms",
        ));

        $this->createAddIfOutputItem("update_time", "DateLabel", array(
        "text"=>$entity->getUdate(),
        "soy2prefix"=>"cms",
        "defaultFormat"=>"H:i"
        ));

        $this->createAddIfOutputItem("entry_url", "HTMLLabel", array(
        "text"=>$entryUrl,
        "soy2prefix"=>"cms",
        ));

//      //カテゴリ  // TODO
//      $this->createAdd("category_list","CategoryListComponent",array(
//          "list" => ($this->isStickUrl && $id > 0) ? self::_labelLogic()->getLabelsByBlogPageIdAndEntryId($this->blogPageId, $id) : array(),
//          "categoryUrl" => $this->categoryPageUrl,
//          "entryCount" => array(),
//          "soy2prefix" => "cms"
//      ));

        CMSPlugin::callEventFunc('onEntryOutput', array("entryId"=>$id,"SOY2HTMLObject"=>$this,"entry"=>$entity));
    }

//  private function _labelLogic(){  // TODO
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
