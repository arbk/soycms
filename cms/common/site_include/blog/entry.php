<?php
/*
このブロックは、記事毎ページでご利用いただけます。

記事毎ページの該当記事の内容を出力する際に用いることができます。

<!-- b_block:id="entry" -->
  <div>
    <h2 cms:id="title">ここにはタイトルが入ります。</h2>
      <span cms:id="create_date" cms:format="Y/m/d">2008/03/17</span>
    <div cms:id="content">ここには本文が入ります</div cms:id="content">
    <div cms:id="more">ここには追記が入ります。</div cms:id="more">
      <span cms:id="create_time" cms:format="H:i">17:00</span>
      <a cms:id="comment_link">コメント(<!-- cms:id="comment_count"--><!-- /cms:id="comment_count"-->)</a>
      <a cms:id="trackback_link">トラックバック(<!-- cms:id="trackback_count"--><!-- /cms:id="trackback_count"-->)</a>
    <p>
      <!-- cms:id="category_list" -->
        <a cms:id="category_link"><!-- cms:id="category_name" --><!-- /cms:id="category_name" --></a>
      <!-- /cms:id="category_list" -->
    </p>
  </div>
<!-- /b_block:id="entry" -->
 */

/**
 * 記事の詳細情報を出力します。
 *
 */
function soy_cms_blog_output_entry($page, $entry)
{
    if (!class_exists("BlogPage_Entry_CategoryList")) {
        class BlogPage_Entry_CategoryList extends HTMLList
        {
            public $categoryPageUri;
            public $entryCount;

            public function setCategoryPageUri($uri)
            {
                $this->categoryPageUri = $uri;
            }

            public function setEntryCount($entryCount)
            {
                $this->entryCount = $entryCount;
            }

            protected function populateItem($entry)
            {
                $this->createAdd("category_link", "HTMLLink", array(
                "link"=>$this->categoryPageUri . rawurlencode($entry->getAlias()),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("category_name", "CMSLabel", array(
                "text"=>$entry->getBranchName(),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("label_id", "CMSLabel", array(
                    "text"=>$entry->getId(),
                    "soy2prefix"=>"cms"
                ));

                $this->createAdd("category_alias", "CMSLabel", array(
                "text" => $entry->getAlias(),
                "soy2prefix" => "cms"
                ));

                $this->createAdd("category_description", "CMSLabel", array(
                "text" => $entry->getDescription(),
                "soy2prefix" => "cms"
                ));

                $reqUri = rtrim($_SERVER["REQUEST_URI"], "/");
                $arg = substr($reqUri, strrpos($reqUri, "/") + 1);
                $alias = rawurlencode($entry->getAlias());
                $this->createAdd("is_current_category", "HTMLModel", array(
                "visible" => ($arg === $alias),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("no_current_category", "HTMLModel", array(
                "visible" => ($arg !== $alias),
                "soy2prefix" => "cms"
                ));

                $this->addLabel("color", array(
                "text" => sprintf("%06X", $entry->getColor()),
                "soy2prefix" => "cms"
                ));

                $this->addLabel("background_color", array(
                "text" => sprintf("%06X", $entry->getBackGroundColor()),
                "soy2prefix" => "cms"
                ));

                $this->addLabel("entry_count", array(
                "text" => (isset($this->entryCount[$entry->getId()])) ? $this->entryCount[$entry->getId()] : 0,
                "soy2prefix" => "cms"
                ));
            }
        }
    }

    if (!class_exists("BlogPage_EntryComponent")) {
        /**
         * 記事を表示するコンポーネント
         */
        class BlogPage_EntryComponent extends SOYBodyComponentBase
        {
            public $entryPageUri;
            public $categoryPageUri;
            public $blogLabelId;
            public $categoryLabelList;
            public $labels;
            public $entryLogic;

            public function setCategoryPageUri($uri)
            {
                $this->categoryPageUri = $uri;
            }

            public function setEntryPageUri($uri)
            {
                $this->entryPageUri = $uri;
            }

            public function setBlogLabelId($blogLabelId)
            {
                $this->blogLabelId = $blogLabelId;
            }

            public function setCategoryLabelList($categoryLabelList)
            {
                $this->categoryLabelList = $categoryLabelList;
            }

            public function setLabels($labels)
            {
                $this->labels = $labels;
            }
            public function setEntryLogic($entryLogic)
            {
                $this->entryLogic = $entryLogic;
            }

            public function setEntry($entry)
            {
                $alias = rawurlencode($entry->getAlias());
                $link = $this->entryPageUri . $alias;

                $this->createAdd("entry_id", "CMSLabel", array(
                "text"=>$entry->getId(),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("title", "CMSLabel", array(
                "html"=> "<a href=\"$link\">".soy2_h($entry->getTitle())."</a>",
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("title_plain", "CMSLabel", array(
                "text"=> $entry->getTitle(),
                "soy2prefix"=>"cms"
                ));

                //本文
                $content = $entry->getContent();

                $this->createAdd("content", "CMSLabel", array(
                "html"=> $content,
                "soy2prefix"=>"cms"
                ));
                /** 一度目でcms:id="content" cms:length="*"を使用してしまうと、以後もcms:lengthに引き連れてしまうため、予備でcms:id="contents"を設ける **/
                $this->createAdd("content2", "CMSLabel", array(
                "html"=> $content,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("has_content", array(
                "visible" => (0 < strlen($content)),  // 空白文字もコンテンツであるため, trim($content)しない
                "soy2prefix"=>"cms",
                ));

                $more = $entry->getMore();

                $this->createAdd("more", "CMSLabel", array(
                "html"=> ('<a id="more"></a>' . $more),
                "soy2prefix"=>"cms",
                ));
                // 2015-07-09追加 1.8.13以降
                $this->addLabel("more_only", array(
                "html"=> $more,
                "soy2prefix"=>"cms",
                ));
                $this->addModel("has_more", array(
                "visible" => (0 < strlen($more)),  // 空白文字もコンテンツであるため, trim($more)しない
                "soy2prefix"=>"cms"
                ));

                //ページ分割 3.0.1-
                $currentPage = isset($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"] > 0 ? $_GET["p"] : 1 ;
                $numberOfPages = 1;

                $contentIsPaginated = ( strpos($content, '<!--nextpage-->') !== false );
                if ($contentIsPaginated) {
                      $paginatedContents = explode('<!--nextpage-->', $content);
                      $numberOfPages = count($paginatedContents);
                } else {
                    $paginatedContents = array($content);
                }

                $moreIsPaginated = ( strpos($more, '<!--nextpage-->') !== false );
                if ($moreIsPaginated) {
                    $paginatedMores = explode('<!--nextpage-->', $more);
                    $numberOfPages = max($numberOfPages, count($paginatedContents));
                } else {
                    $paginatedMores = array($more);
                }

                $this->addModel("content_is_paginated", array(
                "visible"=>$contentIsPaginated,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("content_is_not_paginated", array(
                "visible"=>!$contentIsPaginated,
                "soy2prefix"=>"cms"
                ));
                $this->addLabel("paginated_content", array(
                "html"=>isset($paginatedContents[$currentPage -1]) ? $paginatedContents[$currentPage -1] : "",
                "soy2prefix"=>"cms"
                ));

                $this->addModel("more_is_paginated", array(
                "visible"=>$moreIsPaginated,
                "soy2prefix"=>"cms",
                ));
                $this->addModel("more_is_not_paginated", array(
                "visible"=>!$moreIsPaginated,
                "soy2prefix"=>"cms",
                ));
                $this->addLabel("paginated_more", array(
                "html"=>isset($paginatedMores[$currentPage -1]) ? $paginatedMores[$currentPage -1] : "",
                "soy2prefix"=>"cms"
                ));

                $this->addModel("entry_is_paginated", array(
                "visible"=>$contentIsPaginated || $moreIsPaginated,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("entry_is_not_paginated", array(
                "visible"=>!( $contentIsPaginated || $moreIsPaginated ),
                "soy2prefix"=>"cms"
                ));
                $this->addLabel("current_page", array(
                "text"=> $currentPage,
                "soy2prefix"=>"cms"
                ));
                $this->addLabel("pages", array(
                "text"=> $numberOfPages,
                "soy2prefix"=>"cms"
                ));
                $this->addLabel("total_pages", array(
                "text"=> $numberOfPages,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("is_first_page", array(
                "visible"=> $currentPage == 1,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("is_middle_page", array(
                "visible"=> 1 < $currentPage && $currentPage < $numberOfPages,
                "soy2prefix"=>"cms"
                ));
                $this->addModel("is_last_page", array(
                "visible"=> $currentPage == $numberOfPages,
                "soy2prefix"=>"cms"
                ));

                $this->addLink("next_page_link", array(
                "link"=> ( $currentPage < $numberOfPages ? $link."?p=".($currentPage +1) : ""),
                "soy2prefix"=>"cms"
                ));
                $this->addModel("has_next_page", array(
                "visible"=> ($currentPage < $numberOfPages),
                "soy2prefix"=>"cms"
                ));

                $this->addLink("prev_page_link", array(
                "link"=> ( $currentPage > 1 ? $link."?p=".($currentPage -1) : ""),
                "soy2prefix"=>"cms"
                ));
                $this->addModel("has_prev_page", array(
                "visible"=> ($currentPage > 1),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("page_list", "BlogPage_PagerList", array(
                "list"=> range(1, $numberOfPages),
                "current" => $currentPage,
                "url" => $link,
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("create_date", "DateLabel", array(
                "text"=>$entry->getCdate(),
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("create_time", "DateLabel", array(
                "text"=>$entry->getCdate(),
                "defaultFormat"=>"H:i",
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("entry_alias", "CMSLabel", array(
                "text"=> $alias,
                "soy2prefix"=>"cms"
                ));

                $this->addLink("entry_link", array(
                "link" => $link,
                "soy2prefix"=>"cms"
                ));

                $this->addLink("entry_link_id_ed", array(
                "soy2prefix"=>"cms",
                "link" => $this->entryPageUri . $entry->getId()
                ));

                $this->addLabel("entry_link_text_id_ed", array(
                "soy2prefix"=>"cms",
                "text" => $this->entryPageUri . $entry->getId()
                ));

                $this->addLink("more_link", array(
                "link" => $link ."#more",
                "visible"=>(0 < strlen($more)),
                "soy2prefix"=>"cms"
                ));
                $this->addLink("more_link_no_anchor", array(
                "link" => $link,
                "visible"=>(0 < strlen($more)),
                "soy2prefix"=>"cms"
                ));

                $this->addLink("trackback_link", array(
                "soy2prefix"=>"cms",
                "link" => $link ."#trackback_list",
                "visible" => SOYCMS_ALLOW_BLOG_TRACKBACK
                ));
                $this->createAdd("trackback_count", "CMSLabel", array(
                "soy2prefix"=>"cms",
                "text" => $entry->getTrackbackCount(),
                "visible" => SOYCMS_ALLOW_BLOG_TRACKBACK
                ));
                $this->addModel("has_trackback", array(
                "visible" => (SOYCMS_ALLOW_BLOG_TRACKBACK && 0 < $entry->getTrackbackCount()),
                "soy2prefix"=>"cms"
                ));

                $this->addLink("comment_link", array(
                "soy2prefix"=>"cms",
                "link" => $link ."#comment_list",
                "visible" => SOYCMS_ALLOW_BLOG_COMMENT
                ));
                $this->createAdd("comment_count", "CMSLabel", array(
                "soy2prefix"=>"cms",
                "text" => $entry->getCommentCount(),
                "visible" => SOYCMS_ALLOW_BLOG_COMMENT
                ));
                $this->addModel("has_comment", array(
                "visible" => (SOYCMS_ALLOW_BLOG_COMMENT && 0 < $entry->getCommentCount()),
                "soy2prefix"=>"cms"
                ));

                $categoryLabel = array();
                $entryCount = array();
                foreach ($this->labels as $labelId => $label) {
                    if (in_array($labelId, $this->categoryLabelList)) {
                        $categoryLabel[] =  $label;
                        try {
                            //記事の数を数える。
                            $counts = $this->entryLogic->getOpenEntryCountByLabelIds(array_unique(array($this->blogLabelId,$labelId)));
                        } catch (Exception $e) {
//                          error_log(var_export($e, true));
                            error_log($e->getMessage());
                            $counts= 0;
                        }
                        $entryCount[$labelId] = $counts;
                    }
                }

                //カテゴリリンク
                $this->createAdd("category_list", "BlogPage_Entry_CategoryList", array(
                "list"=>$entry->getLabels(),
                "categoryPageUri"=>$this->categoryPageUri,
                "entryCount" => $entryCount,
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("visible_by_label", "DisplayCtrlModel", array(
                "entry"=>$entry,
                "ctrlType"=>"label",
                "soy2prefix"=>"cms"
                ));
                for ($lbln = 1; $lbln < SOYCMS_INI_NUMOF_BLOG_DCM_LBL; ++$lbln) {
                    $this->createAdd("visible_by_lab" . $lbln, "DisplayCtrlModel", array(
                      "entry"=>$entry,
                      "ctrlType"=>"label",
                      "soy2prefix"=>"cms"
                    ));
                }

                CMSPlugin::callEventFunc('onEntryOutput', array("entryId"=>$entry->getId(), "SOY2HTMLObject"=>$this, "entry"=>$entry));

            // Messageの追加
                $this->addMessageProperty("entry_id", '<?php echo $' . $this->_soy2_id . '["entry_id"]; ?>');
                $this->addMessageProperty("title", '<?php echo $' . $this->_soy2_id . '["title_plain"]; ?>');
                $this->addMessageProperty("content", '<?php echo $' . $this->_soy2_id . '["content"]; ?>');
                $this->addMessageProperty("more", '<?php echo $' . $this->_soy2_id . '["more"]; ?>');
                $this->addMessageProperty("create_date", '<?php echo $' . $this->_soy2_id . '["create_date"]; ?>');
                $this->addMessageProperty("entry_link", '<?php echo $' . $this->_soy2_id . '["entry_link_attribute"]["href"]; ?>');
                $this->addMessageProperty("more_link", '<?php echo $' . $this->_soy2_id . '["more_link_attribute"]["href"]; ?>');
                if (SOYCMS_ALLOW_BLOG_TRACKBACK) {
                    $this->addMessageProperty("trackback_link", '<?php echo $' . $this->_soy2_id . '["trackback_link_attribute"]["href"]; ?>');
                }
                if (SOYCMS_ALLOW_BLOG_COMMENT) {
                    $this->addMessageProperty("comment_link", '<?php echo $' . $this->_soy2_id . '["comment_link_attribute"]["href"]; ?>');
                }
            }

            public function getStartTag()
            {
                return parent::getStartTag();
              /*
              if(defined("CMS_PREVIEW_MODE")){
                return parent::getStartTag() . CMSUtil::getEntryHiddenInputHTML('<?php echo $'.$this->_soy2_pageParam.'["'.$this->_soy2_id.'"]["entry_id"]; ?>','<?php echo strip_tags($'.$this->_soy2_pageParam.'["'.$this->_soy2_id.'"]["title"]); ?>');
              }else{
                return parent::getStartTag();
              }
              */
            }
        }
    }

    $page->createAdd("entry", "BlogPage_EntryComponent", array(
    "soy2prefix" => "b_block",
    "entryPageUri"=> $page->getEntryPageURL(true),
    "categoryPageUri" => $page->getCategoryPageURL(true),
    "blogLabelId" => $page->page->getBlogLabelId(),
    "categoryLabelList" => $page->page->getCategoryLabelList(),
    "labels" => SOY2DAOFactory::create("cms.LabelDAO")->get(),
    "entryLogic" => SOY2Logic::createInstance("logic.site.Entry.EntryLogic"),
    "visible" => ($entry->getId()),
    "entry" => $entry
    ));
}

/**
 * 次の記事を出力
 * 次の記事が無い場合は表示されない
 *
 * <div b_block:id="next_entry">
 *   <a cms:id="entry_link"><!-- cms:id="title" --><!--/cms:id="title" --></a>
 * </div b_block:id="next_entry">
 *
 * <div b_block:id="prev_entry">
 *   <a cms:id="entry_link"><!-- cms:id="title" --><!--/cms:id="title" --></a>
 * </div b_block:id="prev_entry">
 */
function soy_cms_blog_output_entry_navi($page, $next, $prev)
{
    if (!class_exists("BlogPage_Entry_Navigation")) {
        class BlogPage_Entry_Navigation extends SOYBodyComponentBase
        {
            public $entryPageUri;

            public function setEntryPageUri($uri)
            {
                $this->entryPageUri = $uri;
            }

            public function setEntry($entry)
            {
                $this->createAdd("title", "CMSLabel", array(
                "text" => $entry->getTitle(),
                "soy2prefix" => "cms"
                ));

                //同じ意味だけど、他のブロックと合わせてtitle_plainを追加しておく
                $this->createAdd("title_plain", "CMSLabel", array(
                "text" => $entry->getTitle(),
                "soy2prefix" => "cms"
                ));

                $this->createAdd("entry_link", "HTMLLink", array(
                "link" => $this->entryPageUri . rawurlencode($entry->getAlias()),
                "soy2prefix" => "cms"
                ));
            }
        }
    }

    $page->createAdd("next_entry", "BlogPage_Entry_Navigation", array(
    "entryPageUri"=> $page->getEntryPageURL(true),
    "entry" => $next,
    "soy2prefix" => "b_block",
    "visible" => $next->getId()
    ));

    $page->createAdd("prev_entry", "BlogPage_Entry_Navigation", array(
    "entryPageUri"=> $page->getEntryPageURL(true),
    "entry" => $prev,
    "soy2prefix" => "b_block",
    "visible" => $prev->getId()
    ));
}


/*
このブロックは、記事毎ページでご利用になれます。

このブロックで、記事に対して、閲覧者がコメントを投稿できるようにフォームを設置することができます。

ここで投稿されたコメント、管理ページより確認することができます。

このブロックは必ずFORMタグに使用してください。

<form b_block:id="comment_form">
  <p>タイトル：<input cms:id="title" /></p>
  <p>お名前：<input cms:id="author" /></p>
  <p>mail：<input cms:id="mail_address" /></p>
  <p>URL：<input cms:id="url" /></p>
  <p><textarea cms:id="body"></textarea></p>
  <input type="submit" value="投稿">
</form b_block:id="comment_form">
 */
function soy_cms_blog_output_comment_form($page, $entry, $entryComment)
{
    if (!SOYCMS_ALLOW_BLOG_COMMENT) {
        return;
    }

    if (!class_exists("BlogPage_CommentForm")) {
        class BlogPage_CommentForm extends HTMLForm
        {
            const SOY_TYPE = SOY2HTML::HTML_BODY;

            private $entryComment = null;

            public function getStartTag()
            {
                // HTMLForm による トークンの自動生成をOFF (Sessionが無いと機能しないため.)
                $this->setGenerateToken(false);
                // コメント用の簡易トークンを付加 (tag内の $this は CMSBlogPage)
                $tokenTag = '<input type="hidden" name="c_token" value="<?php echo $this->getEasyCommentToken(); ?>">';
                return parent::getStartTag() . $tokenTag;
            }

            public function execute()
            {
//              //cookieから読みだす：高速化キャッシュ対応のため廃止
//              $array = array();
//              @parse_str($_COOKIE["comment_info"],$array);

                $hasComment = (null!==$this->entryComment);

                $this->createAdd("title", "HTMLInput", array(
                    "name" => "title",
                    "value" => $hasComment ? $this->entryComment->getTitle() : "",
                    "maxlength" => SOYCMS_BLOG_COMMENT_TITLE_MAXLEN,
                    "soy2prefix" => "cms"
                ));

                $this->createAdd("author", "HTMLInput", array(
                    "name" => "author",
                    "value" => $hasComment ? $this->entryComment->getAuthor() : "",
                    "maxlength" => SOYCMS_BLOG_COMMENT_AUTHOR_MAXLEN,
                    "soy2prefix" => "cms"
                ));

                $this->createAdd("body", "HTMLTextArea", array(
                    "name" => "body",
                    "value" => $hasComment ? $this->entryComment->getBody() : "",
                    "maxlength" => SOYCMS_BLOG_COMMENT_BODY_MAXLEN,
                    "soy2prefix" => "cms"
                ));

                $this->createAdd("mail_address", "HTMLInput", array(
                    "name" => "mail_address",
                    "value" => $hasComment ? $this->entryComment->getMailAddress() : "",
                    "soy2prefix" => "cms"
                ));

                $this->createAdd("url", "HTMLInput", array(
                    "name" => "url",
                    "value" => $hasComment ? $this->entryComment->getUrl() : "",
                    "soy2prefix" => "cms"
                ));

                parent::execute();
            }

            public function getEntryComment()
            {
                return $this->entryComment;
            }
            public function setEntryComment($entryComment)
            {
                $this->entryComment = $entryComment;
            }
        }
    }

    $page->createAdd("comment_form", "BlogPage_CommentForm", array(
      "action" => $page->getEntryPageURL(true) . $entry->getId() ."?comment#comment_list",
      "soy2prefix" => "b_block",
      "entryComment" => $entryComment,
      "visible" => $entry->getId()
    ));
}


/*
このブロックは、記事毎ページでご利用になれます。

このブロックで、記事に対して、投稿されたコメントの一覧を出力させることができます。

comment_list:
 未承認／拒否コメントは表示されません.

every_comment_list:
 未承認／拒否コメントは タイトル, オーサー, 送信日時 のみ表示されます.

<!-- b_block:id="comment_list" -->
<div>
 <h5 cms:id="title" cms:alt="無題">タイトル</h5>
 <a cms:id="mail_address"><!-- cms:id="author" cms:alt="名無しさん" -->名前<!-- /cms:id="author" --></a>
 |<!-- cms:id="submit_date" cms:format="Y-m-d"-->2008-03-17<!-- /cms:id="submit_date"-->
 |<a cms:id="url">URL</a>
 <div cms:id="body" cms:alt="本文無し">本文</div>
 <span cms:id="submit_time" cms:format="H:i">17:52</span>
</div>
<!--/b_block:id="comment_list" -->

<!-- b_block:id="every_comment_list" -->
  (※ comment_listと同じ)
<!--/b_block:id="every_comment_list" -->

*/
function soy_cms_blog_output_comment_list($page, $entry)
{
    if (!SOYCMS_ALLOW_BLOG_COMMENT) {
        return;
    }

    if (!class_exists("Blog_CommentList")) {
        class Blog_CommentList extends HTMLList
        {
            protected $genAnchor = true;

            public function execute()
            {
                if ("none" === $this->getAttribute("cms:anchor")) {
                    $this->genAnchor = false;
                }
                parent::execute();
            }

            public function getStartTag()
            {
                return ($this->genAnchor ? '<a id="comment_list"></a>' : "") . parent::getStartTag();
            }

            public function populateItem($comment)
            {
                $comment_title = "";
                $comment_author = "";
                $comment_body = "";
                $comment_mail_address = "";
                $comment_url = "";

                if (1 == $comment->getIsApproved()) {
                    // 承認済
                    $comment_title = $comment->getTitle();
                    $comment_author = $comment->getAuthor();
                    $comment_body = str_replace("\n", "@@@@__BR__MARKER__@@@@", $comment->getBody());
                    $comment_body = soy2_h($comment_body);
                    $comment_body = str_replace("@@@@__BR__MARKER__@@@@", "<br>", $comment_body);
                    $comment_mail_address = $comment->getMailAddress();
                    $comment_url = $comment->getUrl();
                } else {
                    // 未承認
                    $comment_title = CMSUtil::strimlength($comment->getTitle(), 5);
                    $comment_author = CMSUtil::strimlength($comment->getAuthor(), 5);
                }

                $this->createAdd("title", "CMSLabel", array(
                "text" => $comment_title,
                "visible" => strlen($comment_title),
                "soy2prefix" => "cms"
                ));

                $this->createAdd("author", "CMSLabel", array(
                  "text" => $comment_author,
                  "visible" => strlen($comment_author),
                  "soy2prefix" => "cms"
                ));

                $this->createAdd("body", "CMSLabel", array(
                  "html" => $comment_body,
                  "visible" => strlen($comment_body),
                  "soy2prefix" => "cms"
                ));
                $this->addModel("empty_body", array(
                  "visible" => (1 > strlen($comment_body)),
                  "soy2prefix" => "cms"
                ));

                $this->createAdd("mail_address", "HTMLLink", array(
                  "link" => "mailto:" . $comment_mail_address,
                  "visible" => strlen($comment_mail_address),
                  "soy2prefix" => "cms"
                ));

                $this->createAdd("url", "HTMLLink", array(
                  "link" => $comment_url,
                  "visible" => strlen($comment_url),
                  "soy2prefix" => "cms"
                ));

                $this->createAdd("submit_date", "DateLabel", array(
                  "text" => $comment->getSubmitDate(),
                  "soy2prefix" => "cms"
                ));
                $this->createAdd("submit_time", "DateLabel", array(
                  "text"=>$comment->getSubmitDate(),
                  "soy2prefix"=>"cms",
                  "defaultFormat"=>"H:i"
                ));
            }
        }
    }

    $dao = SOY2DAOFactory::create("cms.EntryCommentDAO");

    $commentList = $dao->getApprovedCommentByEntryId($entry->getId());
    $hasComment = (0 < count($commentList));

    $page->createAdd("comment_list", "Blog_CommentList", array(
    "list" => $commentList,
    "soy2prefix" => "b_block",
    "visible" => $entry->getId()
    ));
    $page->addModel("has_comment", array(
    "visible" => $hasComment,
    "soy2prefix" => "b_block",
    ));
    $page->addModel("no_comment", array(
    "visible" => !$hasComment,
    "soy2prefix" => "b_block",
    ));

    $everyCommentList = $dao->getByEntryId($entry->getId());
    $hasEveryComment = (0 < count($everyCommentList));

    $page->createAdd("every_comment_list", "Blog_CommentList", array(
      "list" => $everyCommentList,
      "soy2prefix" => "b_block",
      "visible" => $entry->getId()
    ));
    $page->addModel("has_every_comment", array(
      "visible" => $hasEveryComment,
      "soy2prefix" => "b_block",
    ));
    $page->addModel("no_every_comment", array(
      "visible" => !$hasEveryComment,
      "soy2prefix" => "b_block",
    ));
}


/*
このブロックは、記事毎ページでご利用になれます。

このブロックで、記事に対して、投稿されたトラックバックの一覧を出力させることができます。

管理ページより、拒否に設定されているトラックバックは表示されません。

投稿されたトラックバックは初期状態は拒否になっているため、許可に設定しなければ表示されないことにご注意ください。

<ul>
<!-- b_block:id="trackback_list" -->
  <li>
    <h5 cms:id="title">タイトル</h5>
    <a cms:id="url"><!-- cms:id="blog_name"-->ブログ名<!-- /cms:id="blog_name"--></a>
    <p cms:id="excerpt">要約</p>
    <span cms:id="submit_date" cms:format="Y/m/d H:i">2008/03/15 12:35</span>
  </li>
<!--/b_block:id="trackback_list" -->
</ul>
*/
function soy_cms_blog_output_trackback_list($page, $entry)
{
    if (!SOYCMS_ALLOW_BLOG_TRACKBACK) {
        return;
    }

    if (!class_exists("Blog_TrackbackList")) {
        class Blog_TrackbackList extends HTMLList
        {
            protected $genAnchor = true;

            public function execute()
            {
                if ("none" === $this->getAttribute("cms:anchor")) {
                    $this->genAnchor = false;
                }
                parent::execute();
            }

            public function getStartTag()
            {
                return ($this->genAnchor ? '<a id="trackback_list"></a>' : "") . parent::getStartTag();
            }

            public function populateItem($trackback)
            {
                $this->createAdd("title", "CMSLabel", array(
                "text"=>$trackback->getTitle(),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("url", "HTMLLink", array(
                "link"=>$trackback->getUrl(),
                "soy2prefix" => "cms"
                ));
                $this->addLink("link", array(
                "attr:rel"=>(strpos($trackback->getUrl(), CMSUtil::getSiteUrl())===0)?"":"nofollow noopener",
                "link"=>$trackback->getUrl(),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("blog_name", "CMSLabel", array(
                "text"=>$trackback->getBlogName(),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("excerpt", "CMSLabel", array(
                "text"=>$trackback->getExcerpt(),
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("excerpt_h", "CMSLabel", array(
                "html"=> str_replace("\n", "<br>", soy2_h($trackback->getExcerpt())),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("submit_date", "DateLabel", array(
                "text"=>$trackback->getSubmitdate(),
                "soy2prefix" => "cms"
                ));
                $this->createAdd("submit_time", "DateLabel", array(
                "text"=>$trackback->getSubmitdate(),
                "soy2prefix"=>"cms",
                "defaultFormat"=>"H:i"
                ));
            }
        }
    }

    $dao = SOY2DAOFactory::create("cms.EntryTrackbackDAO");
    $trackbackList = $dao->getCertificatedTrackbackByEntryId($entry->getId());

    $page->createAdd("trackback_list", "Blog_TrackbackList", array(
    "list" => $trackbackList,
    "soy2prefix" => "b_block",
    "visible" => $entry->getId()
    ));
    $page->addModel("has_trackback", array(
    "visible" => (0 < count($trackbackList)),
    "soy2prefix" => "b_block",
    ));
}


/*
このブロックは、記事毎ページでご利用になれます。

このブロックで、この記事に投稿するためのURLを出力します。

このブロックは必ずINPUTタグにご使用ください。

<input b_block:id="trackback_link">
 */
function soy_cms_blog_output_trackback_link($page, $entry)
{
    if (!SOYCMS_ALLOW_BLOG_TRACKBACK) {
        return;
    }

  /**
   * 絶対URL（http://～）
   */
    $trackbackUrl = $page->getEntryPageURL(true) . $entry->getId() . "?trackback";

    if (!class_exists("Blog_TrackbackURL")) {
        class Blog_TrackbackURL extends HTMLLabel
        {
            public function execute()
            {
                parent::execute();

                if ($this->tag == "input") {
                    $this->setInnerHTML("");
                } else {
                    $this->clearAttribute("value");
                }
            }
        }
    }

    $page->createAdd("trackback_link", "Blog_TrackbackURL", array(
      "value"=>$trackbackUrl,
      "text"=>$trackbackUrl,
      "soy2prefix"=>"b_block",
      "type"=>"text"
    ));
}


class BlogPage_PagerList extends HTMLList
{
    //今のページ番号
    public $current;
    //最大ページ数
    public $last;
    //ベースURL=最初のページのURL
    public $url;

    public function setCurrent($current)
    {
        $this->current = $current;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    protected function populateItem($page_num)
    {
        $this->last = count($this->list);

        $url = $this->url;
        if ($page_num >1) {
            $url = $url."?p=".$page_num;
        }
        if ($page_num == $this->current) {
            $url = "";
        }

        $class = array();
        if ($page_num == $this->current) {
            $class[] = "current_page_number";
        }
        if ($page_num == 1) {
            $class[] = "first_page_number";
        }
        if ($page_num == $this->last) {
            $class[] = "last_page_number";
        }

        $html = "";
        if (strlen($url)) {
            $html .= "<a href=\"".soy2_h($url)."\"";
            if (count($class)) {
                $html .= " class=\"".implode(" ", $class)."\"";
            }
            $html .= ">";
        }
        $html .= soy2_h($page_num);
        if (strlen($url)) {
            $html .= "</a>";
        }

        $this->createAdd("pager_item", "HTMLLabel", array(
        "html" => $html,
        "soy2prefix" => "cms"
        ));
        $this->createAdd("pager_item_link", "HTMLLink", array(
        "link" => $url,
        "soy2prefix" => "cms"
        ));
        $this->createAdd("pager_item_number", "HTMLLabel", array(
        "text" => $page_num,
        "soy2prefix" => "cms"
        ));

        $this->createAdd("is_first", "HTMLModel", array(
        "visible" => ($page_num == 1),
        "soy2prefix" => "cms"
        ));
        $this->createAdd("is_last", "HTMLModel", array(
        "visible" => ($page_num == $this->last),
        "soy2prefix" => "cms"
        ));
        $this->createAdd("is_middle", "HTMLModel", array(
        "visible" => ($page_num > 1 && $page_num < $this->last),
        "soy2prefix" => "cms"
        ));
        $this->createAdd("is_current", "HTMLModel", array(
        "visible" => ($page_num == $this->current),
        "soy2prefix" => "cms"
        ));
    }
}
