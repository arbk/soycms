<?php

class TrackbackPage extends CMSWebPageBase
{
    private $pageId;

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function doPost()
    {
        if (soy2_check_token()) {
            switch ($_POST['op_code']) {
                case "toggleApproved":
                    $result = SOY2ActionFactory::createInstance("EntryTrackback.ToggleApprovedAction")->run();
                    if ($result->success()) {
                        $newState = $result->getAttribute("new_stat");
                        if ($newState) {
                            $this->addMessage("BLOG_TRACKBACK_CERTIFICATION_SUCCESS");
                        } else {
                            $this->addMessage("BLOG_TRACKBACK_INCERTIFICATION_SUCCESS");
                        }
                    } else {
                        $this->addErrorMessage("BLOG_TRACKBACK_CERTIFICATION_FAILD");
                    }
                    break;
                case "delete":
                    $result = SOY2ActionFactory::createInstance("EntryTrackback.DeleteAction")->run();
                    if ($result->success()) {
                        $this->addMessage("BLOG_TRACKBACK_DELETE_SUCCESS");
                    } else {
                        $this->addErrorMessage("BLOG_TRACKBACK_DELETE_FAILED");
                    }
                    break;
                case "change_defaults":
                    $result = $this->run("EntryTrackback.ChangeDefaultsAction", array("pageId"=>$this->pageId));
                    if ($result->success()) {
                        $this->addMessage("BLOG_TRACKBACK_DEFAULT_CHANGE_SUCCESS");
                    } else {
                        $this->addErrorMessage("BLOG_TRACKBACK_DEFAULT_CHANGE_FAILED");
                    }
                    break;
            }
        }
        $this->jump('Blog.Trackback.'.$this->pageId);
    }

    public function __construct($arg)
    {
        if (!is_array($arg) || !count($arg) || !strlen($arg[0])) {
            $this->jump('Blog');
        }
        $this->pageId = $arg[0];

        //記事公開管理者権限が必要
        if (!UserInfoUtil::hasEntryPublisherRole()) {
            $this->jump('Blog.'.$this->pageId);
        }

        parent::__construct();

        $page = $this->run("Blog.DetailAction", array("id"=>$this->pageId))->getAttribute("Page");

        /**
         * ブログ共通メニュー
         */
        $this->createAdd("BlogMenu", "Blog.BlogMenuPage", array(
            "arguments" => array($this->pageId)
        ));

        /**
         * トラックバック受付の標準設定フォーム
         */
        $this->createAdd("accept_form", "HTMLForm");

        $this->createAdd("default_accept", "HTMLSelect", array(
            "indexOrder"=>true,
            "options"=>array(
                "0"=>CMSMessageManager::get("SOYCMS_WORD_DENY"),
                "1"=>CMSMessageManager::get("SOYCMS_WORD_ALLOW")
            ),
            "name"=>"default_accept",
            "selected"=>((null===$page->getDefaultAcceptTrackback()) || $page->getDefaultAcceptTrackback() == 0)? 0 : 1
        ));

        /**
         * 一括変更フォーム
         */
        $this->createAdd("index_form", "HTMLForm");

        /**
         * トラックバックリスト
         */
        $offset = @$_GET['offset'];
        $limit = @$_GET['limit'];

        if ((null===$offset)) {
            $offset = 0;
        }

        if ((null===$limit)) {
            $limit = SOYCMS_INI_NUMOF_TRACKBACK;
        }

        $result = $this->run('EntryTrackback.TrackbackListAction', array(
            'pageId'=>$this->pageId,
            'limit'=>$limit,
            'offset'=>$offset
        ));

        if (!$result->success()) {
            $this->addMessage("PAGE_DETAIL_GET_FAILED");
            $this->jump("Page");
            exit;
        }

        $list = $result->getAttribute("list");
        $count = $result->getAttribute("count");

        if (count($list)>0) {
            DisplayPlugin::hide("no_trackback_message");
        } else {
            DisplayPlugin::visible("no_trackback_message");
            DisplayPlugin::hide("must_exists_trackback");
        }

        $pageUrl = CMSUtil::getSiteUrl() . ( (strlen($page->getUri()) >0) ? $page->getUri() ."/" : "" ) ;
        $this->createAdd("trackback_list", "TrackbackList", array(
            "list" => $list,
            "url"  => $pageUrl.$page->getEntryPageUri()
        ));

        /**
         * ページャー
         */
        $this->createAdd("self_form", "HTMLForm");
        $currentLink = SOY2PageController::createLink("Blog.Trackback.".$this->pageId);
        $this->createAdd("topPager", "EntryPagerComponent", array(
            "arguments"=> array($offset, $limit, $count, $currentLink)
        ));

        $this->createAdd("limit_INI", "HTMLLink", array(
            "text" =>"[".SOYCMS_INI_NUMOF_TRACKBACK."]",
            "link"=> $currentLink ."?limit=".SOYCMS_INI_NUMOF_TRACKBACK
        ));
        $this->createAdd("limit_50", "HTMLLink", array(
            "text" =>"[50]",
            "link"=> $currentLink ."?limit=50"
        ));
        $this->createAdd("limit_100", "HTMLLink", array(
            "text" =>"[100]",
            "link"=> $currentLink ."?limit=100"
        ));

        /**
         * ツールボックス
         */
        CMSToolBox::addPageJumpBox();

        /**
         * CSS
         */
        HTMLHead::addLink("entrytree", array(
            "rel" => "stylesheet",
            "type" => "text/css",
            "href" => SOY2PageController::createRelativeLink("./css/entry/entry.css")
        ));
        HTMLHead::addLink("comment", array(
            "rel" => "stylesheet",
            "type" => "text/css",
            "href" => SOY2PageController::createRelativeLink("./css/blog/comment_trackback.css")
        ));
    }
}

class TrackbackList extends HTMLlist
{
    private $url = "";

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function populateItem($entity)
    {
        $this->createAdd("trackback_id", "HTMLLabel", array("text"=>$entity->getId()));

        $this->createAdd("submitdate", "HTMLLink", array(
            "text" => date("Y-m-d", $entity->getSubmitDate()),
            "title" => date("Y-m-d H:i:s", $entity->getSubmitDate()),
            "link"=>SOY2PageController::createLink("Blog.TrackbackDetail.".$entity->getId()),
            "onclick"=>"return common_click_to_layer(this,{height:500});"
        ));

        $this->createAdd("entry_title", "HTMLLink", array(
            "html"=>$this->mb_cut_length_html($entity->getEntryTitle(), 18),
            "link"=>$this->url."/".((strlen($entity->getAlias())) ? rawurlencode($entity->getAlias()) : $entity->getId())."#trackback_list"
        ));

        $this->createAdd("approved", "HTMLLabel", array(
            "text"=>($entity->getIsCertification() == 0)? CMSMessageManager::get("SOYCMS_WORD_DENY") : ""
        ));

        if (strlen($entity->getTitle()) == 0) {
            $title = CMSMessageManager::get("SOYCMS_NO_TITLE");
        } else {
            $title = $entity->getTitle();
        }

        $this->createAdd("sender", "HTMLLabel", array(
            "html"=>$this->mb_cut_length_html($entity->getBlogName(), 18),
        ));

//      $this->createAdd("title","HTMLLink",array(
//          "html"=>$this->mb_cut_length_html($title,18),
//          "link"=>$entity->getUrl()
//      ));

//      $this->createAdd("excerpt","HTMLLabel",array(
//          "html"=>$this->mb_cut_length_html($entity->getExcerpt(),40)
//      ));

        $this->createAdd("title", "HTMLLabel", array(
          "html"=>$this->mb_cut_length_html($title, 50),
        ));

        $this->createAdd("trackback_id", "HTMLInput", array(
            "name"=>"trackback_id[]",
            "value"=>$entity->getId()
        ));
    }

    private function mb_cut_length_html($text, $length)
    {
        $hText = soy2_h($text);

        if (mb_strwidth($text) > $length) {
            $sText = mb_strimwidth($text, 0, $length);
            $sText .= "...";

            $hText = "<span title=\"{$hText}\">".soy2_h($sText)."</span>";
        }

        return $hText;
    }
}
