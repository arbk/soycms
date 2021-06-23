<?php
/*
このブロックは全てのブログページでご利用になれます。

このブロックは、当該ブログのトップページへのリンクを出力します。

このブロックは必ずAタグに使用してください。
<a b_block:id="top_link">ブログのトップへ</a b_block:id="top_link">
 */
function soy_cms_blog_output_top_link($page)
{
    $page->createAdd("top_link", "HTMLLink", array(
      "soy2prefix"=>"b_block",
      "link"=>$page->getTopPageURL(true)
    ));
}

/*
このブロックは、全てのブログページでご利用になれます。

ブログに設定されている、カテゴリ分けに使用するラベルの情報を出力します。

このブロックは、繰り返しブロックであり、該当するカテゴリーの個数だけブロックの内容が繰り返し出力されます。

ラベルの表示順が反映されます。

<ul>
<!-- b_block:id="category" -->
  <li><a cms:id="category_link">
    <!-- cms:id="category_name" --><!-- /cms:id="category_name" -->(<!-- cms:id="entry_count" -->0<!-- /cms:id="entry_count" -->)
    </a>
  </li>
<!-- /b_block:id="category" -->
</ul>
 */
function soy_cms_blog_output_category_link($page, $objId = "category", $blog = null)
{
    if (!class_exists("BlogPage_CategoryList")) {
        /**
         * カテゴリーを表示
         */
        class BlogPage_CategoryList extends HTMLList
        {
            public $categoryUrl;
            private $entryCount = 0;

            public function setCategoryUrl($categoryUrl)
            {
                $this->categoryUrl = $categoryUrl;
            }

            protected function populateItem($entry)
            {
                $this->createAdd("category_link", "HTMLLink", array(
                "link"=>$this->categoryUrl . rawurlencode($entry->getAlias()),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("category_name", "CMSLabel", array(
                "text"=>$entry->getBranchName(),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("category_alias", "CMSLabel", array(
                "text"=>$entry->getAlias(),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("entry_count", "CMSLabel", array(
                "text"=>$this->entryCount[$entry->getId()],
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("label_id", "CMSLabel", array(
                "text"=>$entry->getId(),
                "soy2prefix"=>"cms"
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
            }

            public function getEntryCount()
            {
                return $this->entryCount;
            }
            public function setEntryCount($entryCount)
            {
                $this->entryCount = $entryCount;
            }
        }
    }

    // ラベル一覧を取得：ラベルの表示順を反映する
    $labelDao = SOY2DAOFactory::create("cms.LabelDAO");
    $labels = $labelDao->get(); // 表示順に並んでいる

    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");

    if ((null===$blog)) {
        $blogLabelId = $page->page->getBlogLabelId();
        $categories = $page->page->getCategoryLabelList();
        $categoryPageURL = $page->getCategoryPageURL(true);
    } else {
        $blogLabelId = $blog->getBlogLabelId();
        $categories = $blog->getCategoryLabelList();
        $categoryPageURL = $page->getSiteUrl() . $blog->getCategoryPageURL(true);
    }

    // カテゴリリンク
    $categoryLabel = array();
    $entryCount = array();
    foreach ($labels as $labelId => $label) {
        if (in_array($labelId, $categories)) {
            $categoryLabel[] = $label;
            try {
                // 記事の数を数える。
                $counts = $logic->getOpenEntryCountByLabelIds(array_unique(array($blogLabelId, $labelId)));
            } catch (Exception $e) {
//              error_log(var_export($e, true));
                error_log($e->getMessage());
                $counts = 0;
            }
            $entryCount[$labelId] = $counts;
        }
    }

    $page->createAdd($objId, "BlogPage_CategoryList", array(
      "list"=>$categoryLabel,
      "entryCount"=>$entryCount,
      "categoryUrl"=>$categoryPageURL,
      "soy2prefix"=>"b_block"
    ));
}

/*
このブロックは、全てのブログページでご利用になれます。

投稿されている記事を、月別に集計し出力します。

このブロックは、繰り返しブロックであり、該当する月の個数だけブロックの内容が繰り返し出力されます。

<ul>
<!-- b_block:id="archive" -->
  <li><a cms:id="archive_link">
      <!-- cms:id="archive_month" cms:format="Y年m月" -->2012年1月<!-- /cms:id="archive_month" --> (<!-- cms:id="entry_count" -->0<!-- /cms:id="entry_count" -->)
    </a>
  </li>
<!-- /b_block:id="archive" -->
</ul>

 */
//readOnlyはクラスの定義のみ読み込みたい場合はtrue
function soy_cms_blog_output_archive_link($page, $readOnly = false)
{
    $labels = array($page->page->getBlogLabelId());


    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
    // 取得までできているので、整形や表示を設定する
    $month_list = $logic->getCountMonth($labels);

    foreach ($month_list as $key => $month) {
        if ($month == 0) {
            unset($month_list[$key]);
        }
    }

    if (!class_exists("BlogPage_MonthArciveList")) {
        /**
         * 月別アーカイブを表示
         */
        class BlogPage_MonthArciveList extends HTMLList
        {
            public $monthPageUri;
            public $format;
//          private $prevYear;
//          private $secretMode = true;

            public function setMonthPageUri($uri)
            {
                $this->monthPageUri = $uri;
            }

            public function setFormat($format)
            {
                $this->format = $format;
            }

            public function setSecretMode($secretMode)
            {
                $this->secretMode = $secretMode;
            }

//          protected function populateItem($count, $key, $i){
            protected function populateItem($count, $key)
            {
                $this->addLink("archive_link", array(
                "link"=>$this->monthPageUri . date('Y/m', $key),
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("archive_month", "DateLabel", array(
                "text"=>$key,
                "soy2prefix"=>"cms",
                "defaultFormat"=>"Y年n月"
                ));
                $this->createAdd("entry_count", "CMSLabel", array(
                "text"=>$count,
                "soy2prefix"=>"cms"
                ));

//              /** 隠しモード 初回、もしくは前年度の最後の月の前に年数を表示する **/
//              if($this->secretMode){
//                $showFlag = ($i === 1);
//
//                //下記のコードで指定年の最初の月であるか？を調べる
//                $y = (int)date("Y", $key);
//                if(!$showFlag && $this->prevYear != $y) $showFlag = true;
//
//                //今月の年の記録
//                if($y > 1970) $this->prevYear = $y;
//
//
//                $this->addModel("show_year_label", array(
//                  "visible" => $showFlag,
//                  "soy2prefix" => "cms"
//                ));
//
//                // cms:id="no_first" or cms:id="not_first"
//                foreach(array("no", "not") as $t){
//                  $this->addModel($t . "_first", array(
//                    "visible" => ($i > 1),
//                    "soy2prefix" => "cms"
//                  ));
//                }
//
//                $this->addModel("no_year_label", array(
//                  "visible" => !$showFlag,
//                  "soy2prefix" => "cms"
//                ));
//
//                $this->addLabel("year", array(
//                  "text" => $y,
//                  "soy2prefix" => "cms"
//                ));
//              }
//              /** 隠しモードここまで **/
            }
        }
    }

    if (!$readOnly) {
        $page->createAdd("archive", "BlogPage_MonthArciveList", array(
        "list" => $month_list,
        "monthPageUri" => $page->getMonthPageURL(true),
//      "secretMode" => true,
        "soy2prefix" => "b_block"
        ));
    }
}

/*
このブロックは、全てのブログページでご利用になれます。

投稿されている記事を、年別に集計し出力します。

このブロックは、繰り返しブロックであり、該当する年の個数だけブロックの内容が繰り返し出力されます。

<ul>
<!-- b_block:id="archive_by_year" -->
  <li><a cms:id="archive_link">
      <!-- cms:id="archive_year" cms:format="Y年" --><!-- /cms:id="archive_year" --> (<!-- cms:id="entry_count" -->0<!-- /cms:id="entry_count" -->)
    </a>
  </li>
<!-- /b_block:id="archive_by_year" -->
</ul>

 */
function soy_cms_blog_output_archive_link_by_year($page)
{
    $labels = array($page->page->getBlogLabelId());

    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
    // 取得までできているので、整形や表示を設定する
    $year_list = $logic->getCountYear($labels);

    foreach ($year_list as $key => $count) {
        if ($count == 0) {
            unset($year_list[$key]);
        }
    }

    if (!class_exists("BlogPage_YearArciveList")) {
        /**
         * 月別アーカイブを表示
         */
        class BlogPage_YearArciveList extends HTMLList
        {
            public $yearPageUri;
            public $format;

            public function setYearPageUri($uri)
            {
                $this->yearPageUri = $uri;
            }

            public function setFormat($format)
            {
                $this->format = $format;
            }

            protected function populateItem($count, $key)
            {
                $this->createAdd("archive_link", "HTMLLink", array(
                "link"=>$this->yearPageUri . date('Y', $key),
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("archive_year", "DateLabel", array(
                "text"=>$key,
                "soy2prefix"=>"cms",
                "defaultFormat"=>"Y年"
                ));
                $this->createAdd("entry_count", "CMSLabel", array(
                "text"=>$count,
                "soy2prefix"=>"cms"
                ));
            }
        }
    }

    $page->createAdd("archive_by_year", "BlogPage_YearArciveList", array(
      "list"=>$year_list,
      "yearPageUri"=>$page->getMonthPageURL(true),
      "soy2prefix"=>"b_block"
    ));
}


function soy_cms_blog_output_archive_link_every_year($page)
{
    $labels = array($page->page->getBlogLabelId());

    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
    //取得までできているので、整形や表示を設定する
    $month_list = $logic->getCountMonth($labels);

    $month_every_year_list = array();
    foreach ($month_list as $key => $month) {
        if ($month > 0) {
            $month_every_year_list[date("Y", $key)][$key] = $month;
        }
    }

    if (!class_exists("BlogPage_MonthArciveList")) {
        soy_cms_blog_output_archive_link($page, true);
    }

    if (!class_exists("BlogPage_MonthArciveEveryYearList")) {

        class BlogPage_MonthArciveEveryYearList extends HTMLList
        {
            private $monthPageUri;
            private $format;

            public function setMonthPageUri($uri)
            {
                $this->monthPageUri = $uri;
            }

            public function setFormat($format)
            {
                $this->format = $format;
            }

            protected function populateItem($month_list, $year)
            {
                $this->addLabel("year", array(
                "text" => $year,
                "soy2prefix" => "cms"
                ));

                $this->createAdd("archive", "BlogPage_MonthArciveList", array(
                "list" => $month_list,
                "monthPageUri" => $this->monthPageUri,
      //        "secretMode" => false,
                "soy2prefix" => "cms"
                ));
            }
        }
    }

    $page->createAdd("archive_every_year", "BlogPage_MonthArciveEveryYearList", array(
    "list" => $month_every_year_list,
    "monthPageUri" => $page->getMonthPageURL(true),
    "soy2prefix" => "b_block"
    ));

//  if(!class_exists("BlogPage_MonthArciveList")){
//
//    /**
//     * 月別アーカイブを表示
//     */
//    class BlogPage_MonthArciveList extends HTMLList{
//
//      private $monthPageUri;
//      private $format;
//      private $prevYear;
//
//      function setMonthPageUri($uri){
//        $this->monthPageUri = $uri;
//      }
//
//      function setFormat($format){
//        $this->format = $format;
//      }
//
//      protected function populateItem($count, $key, $i){
//
//        $this->addLink("archive_link", array(
//          "link" => $this->monthPageUri . date('Y/m',$key),
//          "soy2prefix" => "cms"
//        ));
//
//        $this->createAdd("archive_month", "DateLabel", array(
//          "text" => $key,
//          "soy2prefix" => "cms",
//          "defaultFormat" => "Y年n月"
//        ));
//        $this->createAdd("entry_count", "CMSLabel", array(
//          "text" => $count,
//          "soy2prefix" => "cms"
//        ));
//
//        /** 隠しモード 初回、もしくは前年度の最後の月の前に年数を表示する **/
//        $showFlag = ($i === 1);
//
//        //下記のコードで指定年の最初の月であるか？を調べる
//        $y = (int)date("Y", $key);
//        if(!$showFlag && $this->prevYear != $y) $showFlag = true;
//
//        //今月の年の記録
//        if($y > 1970) $this->prevYear = $y;
//
//
//        $this->addModel("show_year_label", array(
//          "visible" => $showFlag,
//          "soy2prefix" => "cms"
//        ));
//
//        // cms:id="no_first" or cms:id="not_first"
//        foreach(array("no", "not") as $t){
//          $this->addModel($t . "_first", array(
//            "visible" => ($i > 1),
//            "soy2prefix" => "cms"
//          ));
//        }
//
//        $this->addModel("no_year_label", array(
//          "visible" => !$showFlag,
//          "soy2prefix" => "cms"
//        ));
//
//        $this->addLabel("year", array(
//          "text" => $y,
//          "soy2prefix" => "cms"
//        ));
//        /** 隠しモードここまで **/
//      }
//    }
//  }

//  $page->createAdd("archive", "BlogPage_MonthArciveList", array(
//      "list" => $month_list,
//      "monthPageUri" => $page->getMonthPageURL(true),
//      "soy2prefix" => "b_block"
//  ));
}

///*
//このブロックは、全てのブログページでご利用になれます。
//
//最近投稿された記事一覧を出力します。
//
//このブロックは、繰り返しブロックであり、該当する記事の個数だけブロックの内容が繰り返し出力されます。
//
//ここで表示される件数は、設定ページより設定できるRSSページの表示件数と同一です。
//
//<ul>
//<!-- b_block:id="recent_entry_list" -->
//  <li>
//    <a cms:id="entry_link">
//      <!-- cms:id="title" -->ここにタイトルが入ります<!-- /cms:id="title" -->(<!-- cms:id="create_date" cms:format="m/i"-->03/17<!-- /cms:id="create_date" -->)
//    </a>
//  </li>
//<!--/b_block:id="recent_entry_list" -->
//</ul>
// */
//function soy_cms_blog_output_recent_entry_list($page, $entries){
//
//  if( !class_exists("Blog_RecentEntryList") ){
//    class Blog_RecentEntryList extends HTMLList{
//
//      public $entryPageUri;
//
//      function setEntryPageUri($uri){
//        $this->entryPageUri = $uri;
//      }
//
//      function populateItem($entry){
//
//        $link = $this->entryPageUri . rawurlencode($entry->getAlias());
//
//        $this->createAdd("entry_id", "CMSLabel", array(
//            "text"=>$entry->getId(),
//            "soy2prefix"=>"cms"
//        ));
//
//        $this->createAdd("title", "CMSLabel", array(
//            "text"=>$entry->getTitle(),
//            "soy2prefix"=>"cms"
//        ));
//
//        //同じ意味だけど、他のブロックと合わせてtitle_plainを追加しておく
//        $this->createAdd("title_plain","CMSLabel",array(
//          "text" => $entry->getTitle(),
//          "soy2prefix" => "cms"
//        ));
//
//        $this->createAdd("entry_link", "HTMLLink", array(
//            "link"=>$link,
//            "soy2prefix"=>"cms"
//        ));
//
//        $this->createAdd("create_date", "DateLabel", array(
//            "text"=>$entry->getCdate(),
//            "soy2prefix"=>"cms"
//        ));
//
//        $this->createAdd("create_time", "DateLabel", array(
//            "text"=>$entry->getCdate(),
//            "soy2prefix"=>"cms",
//            "defaultFormat"=>"H:i"
//        ));
//
//        CMSPlugin::callEventFunc('onEntryOutput',array("entryId"=>$entry->getId(),"SOY2HTMLObject"=>$this,"entry"=>$entry));
//      }
//
//      function getStartTag(){
/*
//        if(defined("CMS_PREVIEW_MODE")){
//          return parent::getStartTag() . CMSUtil::getEntryHiddenInputHTML('<?php echo $'.$this->_soy2_id.'["entry_id"]; ?>','<?php echo strip_tags($'.$this->_soy2_id.'["title"]); ?>');
//        }else{
//        return parent::getStartTag();
//      }
*/
//    }
//
//
//    }
//  }
//
//  $page->createAdd("recent_entry_list", "Blog_RecentEntryList", array(
//      "list"=>$entries,
//      "entryPageUri"=>$page->getEntryPageURL(true),
//      "soy2prefix"=>"b_block"
//  ));
//
//}

/*
このブロックは、全てのブログページでご利用になれます。

最近投稿されたコメント一覧を出力します。

このブロックは、繰り返しブロックであり、該当するコメントの個数だけブロックの内容が繰り返し出力されます。

表示件数は SOYCMS_BLOG_COMMENT_NUMOF_RECENT で定義されています。

<ul>
<!-- b_block:id="recent_comment_list" -->
<li>
  <a cms:id="entry_link">
    <!-- cms:id="title" -->コメントのタイトル<!-- /cms:id="title" -->
    <br>
    <!-- cms:id="entry_title" -->記事のタイトル<!-- /cms:id="entry_title" -->
    [<!-- cms:id="submit_date" cms:format="m/d" -->03/17<!-- /cms:id="submit_date" -->]
  </a>
</li>
<!--/b_block:id="recent_comment_list" -->
</ul>
 */
function soy_cms_blog_output_recent_comment_list($page)
{
    if (!SOYCMS_ALLOW_BLOG_COMMENT) {
        return;
    }

    if (!class_exists("Blog_RecentCommentList")) {
        class Blog_RecentCommentList extends HTMLList
        {
            public $entryPageUri;

            public function setEntryPageUri($uri)
            {
                $this->entryPageUri = $uri;
            }

            public function populateItem($comment)
            {
                $this->createAdd("entry_title", "CMSLabel", array(
                "text"=>$comment->getEntryTitle(),
                "soy2prefix"=>"cms"
                ));

                $this->createAdd("title", "CMSLabel", array(
                  "text"=>$comment->getTitle(),
                  "soy2prefix"=>"cms"
                ));

                $this->createAdd("author", "CMSLabel", array(
                  "text"=>$comment->getAuthor(),
                  "soy2prefix"=>"cms"
                ));

                $this->createAdd("submit_date", "DateLabel", array(
                  "text"=>$comment->getSubmitDate(),
                  "soy2prefix"=>"cms"
                ));
                $this->createAdd("submit_time", "DateLabel", array(
                  "text"=>$comment->getSubmitDate(),
                  "soy2prefix"=>"cms",
                  "defaultFormat"=>"H:i"
                ));

                $this->createAdd("entry_link", "HTMLLink", array(
                  "link"=>$this->entryPageUri . rawurlencode($comment->getAlias()),
                  "soy2prefix"=>"cms"
                ));


                /* 以下1.2.8～ */
                $comment_body = str_replace("\n", "@@@@__BR__MARKER__@@@@", $comment->getBody());
                $comment_body = soy2_h($comment_body);
                $comment_body = str_replace("@@@@__BR__MARKER__@@@@", "<br>", $comment_body);

                $this->createAdd("body", "CMSLabel", array(
                  "html"=>$comment_body,
                  "soy2prefix"=>"cms"
                ));

                $this->createAdd("url", "HTMLLink", array(
                  "link"=>$comment->getUrl(),
                  "soy2prefix"=>"cms"
                ));

                $this->createAdd("mail_address", "HTMLLink", array(
                  "link"=>"mailto:" . $comment->getMailAddress(),
                  "soy2prefix"=>"cms"
                ));
            }
        }
    }

    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryCommentLogic");
    $recentCommentList = $logic->getRecentComments(array($page->page->getBlogLabelId()), SOYCMS_BLOG_COMMENT_NUMOF_RECENT);
    $hasRecentComment = (0 < count($recentCommentList));
    try {
        $page->createAdd("recent_comment_list", "Blog_RecentCommentList", array(
        "list"=>$recentCommentList,
        "entryPageUri"=>$page->getEntryPageURL(true),
        "soy2prefix"=>"b_block"
        ));
        $page->addModel("has_recent_comment", array(
          "visible" => $hasRecentComment,
          "soy2prefix" => "b_block",
        ));
        $page->addModel("no_recent_comment", array(
          "visible" => !$hasRecentComment,
          "soy2prefix" => "b_block",
        ));
    } catch (Exception $e) {
//      error_log(var_export($e, true));
        error_log($e->getMessage());
    }
}

/*
このブロックは、全てのブログページでご利用になれます。

最近投稿されたトラックバック一覧を出力します。

このブロックは、繰り返しブロックであり、該当するトラックバックの個数だけブロックの内容が繰り返し出力されます。

表示件数は SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT で定義されています。

<ul>
<!-- b_block:id="recent_trackback_list" -->
  <li>
    <a cms:id="entry_link">
      <!-- cms:id="title" -->タイトル<!-- /cms:id="title" -->
      <br>
      <!-- cms:id="entry_title" -->記事のタイトル<!-- /cms:id="entry_title" -->
      [<!-- cms:id="submit_date" cms:format="m/d" -->03/17<!-- /cms:id="submit_date" -->]
    </a>
  </li>
<!--/b_block:id="recent_trackback_list" -->
</ul>
 */
function soy_cms_blog_output_recent_trackback_list($page)
{
    if (!SOYCMS_ALLOW_BLOG_TRACKBACK) {
        return;
    }

    if (!class_exists("Blog_RecentTrackBackList")) {
        class Blog_RecentTrackBackList extends HTMLList
        {
            public $entryPageUri;

            public function setEntryPageUri($uri)
            {
                $this->entryPageUri = $uri;
            }

            public function populateItem($trackback)
            {
                $link = $this->entryPageUri . rawurlencode($trackback->getAlias());

                $this->createAdd("title", "CMSLabel", array(
                "text"=>$trackback->getTitle(),
                "soy2prefix"=>"cms"
                ));
                $this->createAdd("url", "HTMLLink", array(
                  "link"=>$trackback->getUrl(),
                  "soy2prefix"=>"cms"
                ));
                $this->addLink("link", array(
                  "attr:rel"=>(strpos($trackback->getUrl(), CMSUtil::getSiteUrl())===0)?"":"nofollow noopener",
                  "link"=>$trackback->getUrl(),
                  "soy2prefix" => "cms"
                ));
                $this->createAdd("blog_name", "CMSLabel", array(
                  "text"=>$trackback->getBlogName(),
                  "soy2prefix"=>"cms"
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
                  "soy2prefix"=>"cms"
                ));
                $this->createAdd("submit_time", "DateLabel", array(
                  "text"=>$trackback->getSubmitdate(),
                  "soy2prefix"=>"cms",
                  "defaultFormat"=>"H:i"
                ));
                $this->createAdd("entry_link", "HTMLLink", array(
                  "link"=>$link,
                  "soy2prefix"=>"cms"
                ));

                $this->createAdd("entry_title", "CMSLabel", array(
                  "text"=>$trackback->getEntryTitle(),
                  "soy2prefix"=>"cms"
                ));
            }
        }
    }

    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryTrackbackLogic");
    $trackbacks = $logic->getRecentTrackbacks(array($page->page->getBlogLabelId()), SOYCMS_BLOG_TRACKBACK_NUMOF_RECENT);
    try {
        $page->createAdd("recent_trackback_list", "Blog_RecentTrackBackList", array(
        "list"=>$trackbacks,
        "entryPageUri"=>$page->getEntryPageURL(true),
        "soy2prefix"=>"b_block"
        ));
    } catch (Exception $e) {
//      error_log(var_export($e, true));
        error_log($e->getMessage());
    }
}

/**
 * RSS2.0を出力
 */
function soy_cms_blog_output_rss($page, $entries, $title = null, $charset = SOY2::CHARSET, $feedUrl = null, $opUdate = false)
{
//  function soy_cms_blog_output_rss_h($string){
//      return soy2_h($string);
//  }

    function soy_cms_blog_output_rss_cdata($html)
    {
        // タグを除去してエンティティを戻す
        $text = SOY2HTML::ToText($html);
        // ]]> があったらそこで分割する
        $cdata = "<![CDATA[" . str_replace("]]>", "]]]]><![CDATA[>", $text) . "]]>";
        return $cdata;
    }

    $update = $page->page->getUdate();
    $pubDate = empty($entries) ? $update : current($entries)->getCdate();
    $entryPageUrl = $page->getEntryPageURL(true);
    if ((null===$title)) {
        $title = $page->page->getTitle();
    }
    if ((null===$feedUrl)) {
        $feedUrl = $page->getRssPageURL(true);
    }

    $xml = array();

    $xml[] = '<?xml version="1.0" encoding="' . $charset . '" ?>';
    $xml[] = '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
    $xml[] = '<channel>';
    $xml[] = '<title>' . soy2_h($title) . '</title>';
    $xml[] = '<link>' . soy2_h($page->getTopPageURL(true)) . '</link>';
    $xml[] = '<atom:link rel="self" type="application/rss+xml" href="' . soy2_h($feedUrl) . '" />';
    $xml[] = '<description>' . soy2_h($page->page->getDescription()) . '</description>';
    $xml['pubDate'] = '';
    $xml['lastBuildDate'] = '';
    $xml[] = '<docs>http://blogs.law.harvard.edu/tech/rss</docs>';
    $xml[] = '<language>' . SOYCMS_LANGUAGE . '</language>';

    foreach ($entries as $entry) {
        $buildDate = max($entry->getCdate(), $entry->getUdate());
        $update = max($buildDate, $update);

        $xml[] = '<item>';
        $xml[] = '<title>' . soy2_h($entry->getTitle()) . '</title>';
        $xml[] = '<link>' . soy2_h($entryPageUrl . rawurlencode($entry->getAlias())) . '</link>';
        $xml[] = '<guid isPermaLink="false">' . soy2_h($entryPageUrl . $entry->getId()) . '</guid>';
        $xml[] = '<pubDate>' . soy2_h(date('r', $opUdate ? $buildDate : $entry->getCdate())) . '</pubDate>';
//      $xml[] = '<lastBuildDate>'.soy2_h(date('r',$buildDate)).'</lastBuildDate>'; // [Validate ERR] Undefined item element: lastBuildDate
//      $xml[] = '<description>' . soy_cms_blog_output_rss_cdata((strlen($entry->getDescription()) > 0) ? $entry->getDescription() : $entry->getContent()) . '</description>';
        $xml[] = '<description>' . soy_cms_blog_output_rss_cdata($entry->getContent()) . '</description>';
        $xml[] = '</item>';
    }

    $xml['pubDate'] = '<pubDate>' . soy2_h(date('r', $opUdate ? $update : $pubDate)) . '</pubDate>';
    $xml['lastBuildDate'] = '<lastBuildDate>' . soy2_h(date('r', $opUdate ? $update : $pubDate)) . '</lastBuildDate>';

    $xml[] = '</channel>';
    $xml[] = '</rss>';

    echo implode("\n", $xml);
}

/*
 * ATOM出力
 */
function soy_cms_blog_output_atom($page, $entries, $title = null, $charset = SOY2::CHARSET, $feedUrl = null, $opUdate = false)
{
//  function soy_cms_blog_output_atom_h($string){
//      return soy2_h($string);
//  }

    function soy_cms_blog_output_atom_cdata($string)
    {
        // ]]> があったらそこで分割する
        $cdata = str_replace("]]>", "]]]]><![CDATA[>", $string);
        $cdata = "<![CDATA[" . $cdata . "]]>";
        return $cdata;
    }

    $update = $page->page->getUdate();
    $pubDate = empty($entries) ? $update : current($entries)->getCdate();
    $entryPageUrl = $page->getEntryPageURL(true);
    if ((null===$title)) {
        $title = $page->page->getTitle();
    }
    if ((null===$feedUrl)) {
        $feedUrl = $page->getRssPageURL(true);
    }

    $xml = array();

    $xml[] = '<?xml version="1.0" encoding="' . $charset . '" ?>';
    $xml[] = '<feed xml:lang="' . SOYCMS_LANGUAGE . '" xmlns="http://www.w3.org/2005/Atom">';
    $xml[] = '<title>' . soy2_h($title) . '</title>';
    $xml[] = '<subtitle type="html">' . soy2_h($page->page->getDescription()) . '</subtitle>';
    $xml[] = '<link rel="alternate" href="' . soy2_h($page->getTopPageURL(true)) . '" />';
    $xml[] = '<link rel="self" type="application/atom+xml" href="' . soy2_h($feedUrl) . '" />';
    $xml[] = '<author><name>' . soy2_h(strlen($page->page->getAuthor()) ? $page->page->getAuthor() : $page->siteConfig->getName()) . '</name></author>';
    $xml[] = '<id>' . soy2_h($page->getTopPageURL(true)) . '</id>';
    $xml['updated'] = '';

    foreach ($entries as $entry) {
        $buildDate = max($entry->getCdate(), $entry->getUdate());
        $update = max($buildDate, $update);

        $xml[] = '<entry>';
        $xml[] = '<title>' . soy2_h($entry->getTitle()) . '</title>';
        $xml[] = '<link rel="alternate" href="' . soy2_h($entryPageUrl . rawurlencode($entry->getAlias())) . '" type="application/xhtml+xml"/>';
        $xml[] = '<published>' . soy2_h(date('c', $entry->getCdate())) . '</published>';
        $xml[] = '<updated>' . soy2_h(date('c', $opUdate ? $buildDate : $entry->getCdate())) . '</updated>';
        $xml[] = '<id>' . soy2_h($entryPageUrl . $entry->getId()) . '</id>';
//      if( strlen($entry->getDescription()) > 0 ){
//          $xml[] = '<summary>' . soy2_h($entry->getDescription()) . '</summary>';
//      }
        $xml[] = '<content type="html">' . soy_cms_blog_output_atom_cdata($entry->getContent()) . '</content>';
        $xml[] = '</entry>';
    }

    $xml['updated'] = '<updated>' . soy2_h(date('c', $opUdate ? $update : $pubDate)) . '</updated>';

    $xml[] = '</feed>';

    echo implode("\n", $xml);
}

/*
 * フィードのメタ情報を出力
 * <!-- b_block:id="meta_feed_link" --><!--/b_block:id="meta_feed_link" -->
 */
function soy_cms_blog_output_meta_feed_info($page)
{
    $hUrls = $page->getRssHref();
    $hTitle = soy2_h($page->page->getTitle());

    $pageFormat = $page->page->getFeedTitleFormat();
    if (strlen($pageFormat) > 0) {
        $pageFormat = preg_replace('/%SITE%/', $page->siteConfig->getName(), $pageFormat);
        $pageFormat = preg_replace('/%BLOG%/', $page->page->getTitle(), $pageFormat);
        $hTitle = $pageFormat;
    }

    $text_rss = '<link rel="alternate" type="application/rss+xml" title="' . $hTitle . '" href="' . $hUrls["rss"] . '">';
    $page->createAdd("meta_rss_link", "HTMLLabel", array(
      "html"=>$text_rss,
      "visible"=>$page->page->getGenerateRssFlag(),
      "soy2prefix"=>"b_block"
    ));

    $text_atom = '<link rel="alternate" type="application/atom+xml" title="' . $hTitle . '" href="' . $hUrls["atom"] . '">';
    $page->createAdd("meta_atom_link", "HTMLLabel", array(
      "html"=>$text_atom,
      "visible"=>$page->page->getGenerateRssFlag(),
      "soy2prefix"=>"b_block"
    ));

    $page->createAdd("meta_feed_link", "HTMLLabel", array(
      "html"=>$text_rss . "\n" . $text_atom,
      "visible"=>$page->page->getGenerateRssFlag(),
      "soy2prefix"=>"b_block"
    ));
}

/*
 * feedのリンクを表示
 * <a b_block:id="rss_link">RSS</a>
 * <a b_block:id="atom_link">ATOM</a>
 */
function soy_cms_blog_output_feed_link($page)
{
    $hUrls = $page->getRssHref(true);

    $page->createAdd("rss_link", "HTMLLink", array(
      "link"=>$hUrls["rss"],
      "visible"=>$page->page->getGenerateRssFlag(),
      "soy2prefix"=>"b_block"
    ));

    $page->createAdd("atom_link", "HTMLLink", array(
      "link"=>$hUrls["atom"],
      "visible"=>$page->page->getGenerateRssFlag(),
      "soy2prefix"=>"b_block"
    ));
}
