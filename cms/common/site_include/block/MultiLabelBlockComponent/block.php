<?php
/**
 * 複数ラベル、複数ブログ割り当てコンポーネント
 */
class MultiLabelBlockComponent implements BlockComponent
{
    private $siteId = null;
    private $oldSiteId = null;
    private $mapping = array();
    private $labelIds = array();

    private $displayCountFrom = null;
    private $displayCountTo = null;
    private $order = BlockComponent::ORDER_DESC;
    private $orderType = BlockComponent::ORDER_TYPE_CDT;
    private $entryOpdata = BlockComponent::ENTRY_OPDATA_ALL;

    /**
     * @return SOY2HTML
     * 設定画面用のHTMLPageComponent
     */
    public function getFormPage()
    {

        //DSNを切り替える
        if ((null===$this->siteId)) {
            $this->siteId = UserInfoUtil::getSite()->getId();
        } elseif ($this->oldSiteId != $this->siteId) {
            $this->mapping = array();
            $this->labelIds = array();
        }

        SOY2DAOConfig::Dsn(ADMIN_DB_DSN);
        $siteDAO = SOY2DAOFactory::create("admin.SiteDAO");

        $sites = $siteDAO->getBySiteType(Site::TYPE_SOY_CMS);
        try {
            $site = $siteDAO->getById($this->siteId);
        } catch (Exception $e) {
            $site = UserInfoUtil::getSite();
        }

        SOY2DAOConfig::Dsn($site->getDataSourceName());

        $logic = SOY2Logic::createInstance("logic.site.Page.BlogPageLogic");
        $blogPages = $logic->getBlogPageList();

        return SOY2HTMLFactory::createInstance("MultiLabelBlockComponent_FormPage", array(
        "entity" => $this,
        "blogPages" => $blogPages,
        "sites" => $sites,
        "siteId" => $this->siteId
        ));
    }

    /**
     * @return SOY2HTML
     * 表示用コンポーネント
     */
    public function getViewPage($page)
    {
        //$siteIdプロパティがnullの場合がある
        if ((null===$this->siteId) && defined("_SITE_ID_")) {
            $this->siteId = _SITE_ID_;
        }

        //古いDSNのバックアップ
        $oldDsn = null;

        //siteのDsn
        $dsn = null;

        $array = array();
        $urlMapping = array();
        $blogIdMapping = array();
        $blogTitleMapping = array();
        $blogUrlMapping = array();
//      $blogCategoryUrlMapping = array();  // TODO

        try {
            //DSNを切り替える、ついでにサイトのURLを取得
            //自サイトでもサイトのURL取得
            $oldDsn = SOY2DAOConfig::Dsn();
            SOY2DAOConfig::Dsn(ADMIN_DB_DSN);
            $siteDAO = SOY2DAOFactory::create("admin.SiteDAO");

            if (is_numeric($this->siteId)) {
                $site = $siteDAO->getById($this->siteId);
            } else {
                $site = $siteDAO->getBySiteId($this->siteId);
            }

            SOY2DAOConfig::Dsn($site->getDataSourceName());

            $dsn = $site->getDataSourceName();

            $siteUrl = $site->getUrl();

            if ($site->getIsDomainRoot()) {
                $siteUrl = "/";
            }

            //アクセスしているサイトと同じドメインなら / からの絶対パスにしておく（ケータイでURLに自動でセッションIDが付くように）
            if (strpos($siteUrl, "http://".$_SERVER["SERVER_NAME"]."/")===0) {
                $siteUrl = substr($siteUrl, strlen("http://".$_SERVER["SERVER_NAME"]));
            }
            if (strpos($siteUrl, "https://".$_SERVER["SERVER_NAME"]."/")===0) {
                $siteUrl = substr($siteUrl, strlen("https://".$_SERVER["SERVER_NAME"]));
            }

            $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
            $logic->setBlockClass(get_class($this));

            if (null!==$this->displayCountFrom && is_numeric($this->displayCountFrom)) {
                $this->displayCountFrom = max($this->displayCountFrom, 1);//0件目は認めない→１件目に変更

                if (null!==$this->displayCountTo && is_numeric($this->displayCountTo)
                && ($this->displayCountTo >= $this->displayCountFrom)) {
                    $logic->setLimit($this->displayCountTo - (int)$this->displayCountFrom + 1);//n件目～m件目はm-n+1個のエントリ
                }

                $logic->setOffset($this->displayCountFrom-1);//offsetは0スタートなので、n件目=offset:n-1
            }

            //表示順の設定
            if (BlockComponent::ORDER_TYPE_TTL === $this->orderType) {
                if (BlockComponent::ORDER_ASC === $this->order) {
                    $logic->setOrderColumns(array("title"=>"asc", "cdate"=>"asc", "id"=>"asc"));
                } else {
                    // BlockComponent::ORDER_DESC === $this->order
                    $logic->setOrderColumns(array("title"=>"desc", "cdate"=>"desc", "id"=>"desc"));
                }
            } elseif (BlockComponent::ORDER_ASC === $this->order) {
                // BlockComponent::ORDER_TYPE_CDT === $this->orderType
                $logic->setReverse(true);
            }

            //出力項目の設定
            if ($this->entryOpdata === BlockComponent::ENTRY_OPDATA_CNT) {
                $logic->setIgnoreColumns(array("more"));
            } elseif ($this->entryOpdata === BlockComponent::ENTRY_OPDATA_TTL) {
                $logic->setIgnoreColumns(array("content", "more"));
            }

            //エントリー取得
            $array = array();
            try {
//              if(defined("CMS_PREVIEW_ALL")){
//                  $array = $logic->getByLabelIds($this->getLabelIds());
//              }else{
                $array = $logic->getOpenEntryByLabelIds($this->getLabelIds(), false);
//              }
            } catch (Exception $e) {
//              error_log(var_export($e, true));
                error_log($e->getMessage());
            }

            //ブログページを作る
            $entryLabelDAO= SOY2DAOFactory::create("cms.EntryLabelDAO");

            $blogPageDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
            foreach ($array as $key => $entry) {
                foreach ($this->mapping as $labelId => $blogId) {
                    try {
                        $entryLabelDAO->getByParam($labelId, $entry->getId());
                        $blogPage = $blogPageDAO->getById($blogId);
                        $url = $siteUrl . $blogPage->getEntryPageURL();
                        $urlMapping[$entry->getId()] = $url;
                        $blogTitle = $blogPage->getTitle();
                        $blogIdMapping[$entry->getId()] = $blogId;
                        $blogTitleMapping[$entry->getId()] = $blogTitle;
                        $blogUrlMapping[$entry->getId()] = $siteUrl . $blogPage->getTopPageURL();
//                      $blogCategoryUrlMapping[$entry->getId()] = $siteUrl . $blogPage->getCategoryPageURL();  // TODO
                        continue;
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                    }
                }
            }

            $entryLabelDAO = null;
            $blogPageDAO = null;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        SOY2::import("site_include.block._common.MultiEntryListComponent");
//      SOY2::import("site_include.blog.component.CategoryListComponent");  // TODO
        $inst = SOY2HTMLFactory::createInstance("MultiEntryListComponent", array(
        "list" => $array,
        "url" => $urlMapping,
        "blogId" => $blogIdMapping,
        "blogTitle" => $blogTitleMapping,
        "blogUrl" => $blogUrlMapping,
//      "blogCategoryUrl" => $blogCategoryUrlMapping,
        "soy2prefix"=>"block",
        "dsn" => $dsn
        ));

        //Dsn戻す
        if ($oldDsn) {
            SOY2DAOConfig::Dsn($oldDsn);
        }

        return $inst;
    }

    /**
     * @return string
     * 一覧表示に出力する文字列
     */
    public function getInfoPage()
    {
        try {
            $res = count($this->mapping) . CMSMessageManager::get("SOYCMS_NUMBER_OF_SET_LABELS");
            $res .= (strlen($this->displayCountFrom) or strlen($this->displayCountTo)) ? " ". $this->displayCountFrom."-".$this->displayCountTo : "" ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $res = CMSMessageManager::get("SOYCMS_NO_SETTING");
        }
        return $res;
    }

    /**
     * @return string コンポーネント名
     */
    public function getComponentName()
    {
        return CMSMessageManager::get("SOYCMS_BLOG_LINK_BLOCK");
    }

    public function getComponentDescription()
    {
        return CMSMessageManager::get("SOYCMS_BLOG_LINK_BLOCK_DESCRIPTION");
    }


    public function getSiteId()
    {
        return $this->siteId;
    }
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }
    public function getMapping()
    {
        if (!empty($this->mapping) && strlen(implode("", array_values($this->mapping))) == 0) {
            $this->mapping = array();
        }
        return $this->mapping;
    }
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }
    public function getLabelIds()
    {
        if (empty($this->labelIds) || !empty($this->mapping)) {
            $this->labelIds = array_keys($this->mapping);
        }
        return $this->labelIds;
    }
    public function setLabelIds($labelIds)
    {
        $this->labelIds = $labelIds;
    }

    public function getDisplayCountFrom()
    {
        return $this->displayCountFrom;
    }
    public function setDisplayCountFrom($displayCountFrom)
    {
        $this->displayCountFrom = (strlen($displayCountFrom) && is_numeric($displayCountFrom)) ? (int)$displayCountFrom : null;
    }

    public function getDisplayCountTo()
    {
        return $this->displayCountTo;
    }
    public function setDisplayCountTo($displayCountTo)
    {
        $this->displayCountTo = (strlen($displayCountTo) && is_numeric($displayCountTo)) ? (int)$displayCountTo : null;
    }

    public function getOldSiteId()
    {
        return $this->oldSiteId;
    }
    public function setOldSiteId($oldSiteId)
    {
        $this->oldSiteId = $oldSiteId;
    }

    public function getOrder()
    {
        return $this->order;
    }
    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getOrderType()
    {
        return $this->orderType;
    }
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;
    }

    public function getEntryOpdata()
    {
        return $this->entryOpdata;
    }
    public function setEntryOpdata($entryOpdata)
    {
        $this->entryOpdata = $entryOpdata;
    }
}

class MultiLabelBlockComponent_FormPage extends HTMLPage
{
    private $siteId = "";
    private $sites = array();
    private $entity;
    private $blogPages = array();

    public function execute()
    {
        //サイト変更機能
        $this->addForm("sites_form");
        $this->addSelect("site", array(
        "options" => $this->sites,
        "property" => "siteName",
        "name" => "object[siteId]",
        "selected" => $this->siteId
        ));

        /* 以下、通常フォーム */

        $this->addSelect("label_select", array(
        "options"=>$this->getLabelList(),
        "property" => "displayCaption"
        ));

        $this->addSelect("blog_select", array(
        "options"=>$this->blogPages,
        "property" => "title"
        ));

        $this->addInput("display_number_start", array(
        "value"=>$this->entity->getDisplayCountFrom(),
        "name"=>"object[displayCountFrom]"
        ));
        $this->addInput("display_number_end", array(
        "value"=>$this->entity->getDisplayCountTo(),
        "name"=>"object[displayCountTo]"
        ));

        $this->addSelect("display_order_list", array(
        "name"=>"object[orderType]",
        "selected"=>$this->entity->getOrderType(),
        "options"=>array(
            BlockComponent::ORDER_TYPE_CDT=>"作成日",
            BlockComponent::ORDER_TYPE_TTL=>"タイトル"
        )
        ));
        $this->addCheckBox("display_order_asc", array(
        "type"      => "radio",
        "name"      => "object[order]",
        "value"     => BlockComponent::ORDER_ASC,
        "selected"  => $this->entity->getOrder() == BlockComponent::ORDER_ASC,
        "elementId" => "display_order_asc",
        ));
        $this->addCheckBox("display_order_desc", array(
        "type"      => "radio",
        "name"      => "object[order]",
        "value"     => BlockComponent::ORDER_DESC,
        "selected"  => $this->entity->getOrder() == BlockComponent::ORDER_DESC,
        "elementId" => "display_order_desc",
        ));

        $this->addSelect("entry_opdata_list", array(
        "name"=>"object[entryOpdata]",
        "selected"=>$this->entity->getEntryOpdata(),
        "options"=>array(
            BlockComponent::ENTRY_OPDATA_ALL=>"全て",
            BlockComponent::ENTRY_OPDATA_CNT=>"本文",
            BlockComponent::ENTRY_OPDATA_TTL=>"タイトル"
        )
        ));
        $this->addLabel("entry_opdata_list_label", array(
        "text"=>CMSMessageManager::get("SOYCMS_BLOCK_SELECT_ENTRY_OPDATA"),
        ));

        $labelList = $this->entity->getMapping();
        $this->createAdd("label_list", "MultiLabelList_LabelList", array(
        "labels"=>$this->getLabelList(),
        "blogs" => $this->blogPages,
        "list" =>$labelList,
        ));
        $this->addModel("has_label_list", array(
        "visible" => count($labelList),
        ));

        //現在保存されているサイトID
        $this->addInput("old_site_id", array(
        "name" => "object[oldSiteId]",
        "value" => $this->siteId
        ));

        $this->addForm("main_form", array());
    }

    /**
     * ラベル表示コンポーネントの実装を行う
     */
    public function setEntity(MultiLabelBlockComponent $block)
    {
        $this->entity = $block;
    }

    /**
     * ブログページを渡す
     *
     * array(ページID => )
     */
    public function setBlogPages($pages)
    {
        $this->blogPages = $pages;
    }

    /**
     *  ラベルオブジェクトのリストを返す
     *  NOTE:個数に考慮していない。ラベルの量が多くなるとpagerの実装が必要？
     */
    private function getLabelList()
    {
        $dao = SOY2DAOFactory::create("cms.LabelDAO");
        return $dao->get();
    }

    public function getTemplateFilePath()
    {
//    //ext-modeでbootstrap対応画面作成中
//    if(defined("EXT_MODE_BOOTSTRAP") && file_exists(CMS_BLOCK_DIRECTORY . basename(__DIR__). "/form_sbadmin2.html")){
//      return CMS_BLOCK_DIRECTORY . basename(__DIR__). "/form_sbadmin2.html";
//    }
//
//
//    if(!defined("SOYCMS_LANGUAGE")||SOYCMS_LANGUAGE=="ja"||!file_exists(CMS_BLOCK_DIRECTORY . basename(__DIR__). "/form_".SOYCMS_LANGUAGE.".html")){
        return CMS_BLOCK_DIRECTORY . basename(__DIR__). "/form.html";
//    }else{
//      return CMS_BLOCK_DIRECTORY . basename(__DIR__). "/form_".SOYCMS_LANGUAGE.".html";
//    }
    }

    public function setSites($sites)
    {
        $this->sites = $sites;
    }

    public function setSiteId($id)
    {
        $this->siteId = $id;
    }
}

class MultiLabelList_LabelList extends HTMLList
{
    private $labels = array();
    private $blogs = array();

    protected function populateItem($entity, $key)
    {

        $labelId = $key;
        $blogId = (is_numeric($entity)) ? (int)$entity : 0;

        $this->addLabel("label", array(
        "text"=> (isset($this->labels[$labelId])) ? $this->labels[$labelId]->getCaption() : ""
        ));

        $this->addLabel("title", array(
        "text"=> (isset($this->blogs[$blogId])) ? $this->blogs[$blogId] : ""
        ));

        $this->addInput("delete_button", array(
        "name" => "delete",
        "type" => "submit",
        "value" => CMSMessageManager::get("SOYCMS_DELETE"),
        "onclick" => 'add_reload_input(this);delete_mapping($(\'#mapping_'.$labelId.'\'));'
        ));

        $this->addInput("mapping", array(
        "id" => "mapping_".$labelId,
        "class" => "mapping_input",
        "name" => "object[mapping][".$labelId."]",
        "value" => $blogId,
        "type" => "hidden"
        ));
    }

    public function getLabels()
    {
        return $this->labels;
    }
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }
    public function getBlogs()
    {
        return $this->blogs;
    }
    public function setBlogs($blogs)
    {
        $this->blogs = $blogs;
    }
}
