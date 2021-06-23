<?php
class CMSPageController extends SOY2PageController
{
    public $args;
    public $siteConfig;
    public $pageType;
    public $webPage;

    public function execute()
    {
        $start = microtime(true);

        // デフォルトページ
        $siteConfig = SOY2DAOFactory::create("cms.SiteConfigDAO")->get();

        // パスからURIと引数に変換
        SOY2::import("site_include.CMSPathInfoBuilder");
        $pathBuilder = new CMSPathInfoBuilder();
        $uri = $pathBuilder->getPath();
        $args = $pathBuilder->getArguments();
        unset($pathBuilder);

        // 保存
        $this->args = $args;
        $this->siteConfig = $siteConfig;

        // URLShortener
        //CMS:PLUGIN callEventFunction
        CMSPlugin::callEventFunc('onSiteAccess', array("controller"=>$this));

        // 文字コード変換
        $_GET = $this->convertEncoding($_GET);
        $_POST = $this->convertEncoding($_POST);

        // ヘッダー送信（先に送信しておかないと後で上書きできない）
        $contentType = "text/html";
        $contentExt = pathinfo($uri, PATHINFO_EXTENSION);
        if ("css" === $contentExt) {
            $contentType = "text/css";
        } elseif ("js" === $contentExt) {
            $contentType = "application/javascript";
        } elseif ("xml" === $contentExt) {
            $contentType = "application/xml";
        }
        header("Content-Type: " . $contentType . "; charset=" . $this->siteConfig->getCharsetText());
        // 多言語対応のために保留
        // header("Content-Language: ja");

        try {
            $page = $this->_getPage($uri);

            try {
//              //404ページでも下記の値を使用する
//              $_SERVER["SOYCMS_PAGE_URI"] = $page->getUri();
//              $_SERVER["SOYCMS_PAGE_ID"] = $page->getId();

                if ($page->isActive() < 0) {
                    throw new Exception("out of date.");
                }

                // 閲覧制限チェック
                if ($this->siteConfig && $this->siteConfig->isShowOnlyAdministrator()) {
                    // セッションからログインしているかどうか取得
                    SOY2::import("util.UserInfoUtil");
                    SOY2::import("domain.admin.Site");

                    if (!UserInfoUtil::isLoggined() || !UserInfoUtil::getSite()
                    or soy2_realpath(_SITE_ROOT_) != soy2_realpath(UserInfoUtil::getSite()->getPath())
                    ) {
                        throw new Exception("not logined");
                    }
                }

                $this->pageType = $page->getPageType();

                switch ($page->getPageType()) {
                    case Page::PAGE_TYPE_BLOG:
                        SOY2::import('site_include.CMSBlogPage');
                        $this->webPage = &SOY2HTMLFactory::createInstance("CMSBlogPage", array(
                        "arguments"=>array($page->getId(), $args, $siteConfig),
                        "siteRoot"=>SOY2PageController::createLink("")
                        ));
                        // TODO 存在しないページへのアクセスで例外を投げる
                        break;

//                  case Page::PAGE_TYPE_MOBILE:
//                      SOY2::import('site_include.CMSMobilePage');
//                      $this->webPage = &SOY2HTMLFactory::createInstance("CMSMobilePage", array(
//                          "arguments" => array($page->getId(), $args, $siteConfig),
//                          "siteRoot" => SOY2PageController::createLink("")
//                      ));
//                      break;

                    case Page::PAGE_TYPE_APPLICATION:
                        SOY2::import('site_include.CMSApplicationPage');
                        $this->webPage = &SOY2HTMLFactory::createInstance("CMSApplicationPage", array(
                        "arguments"=>array($page->getId(), $args, $siteConfig),
                        "siteRoot"=>SOY2PageController::createLink("")
                        ));
                        break;

                    case Page::PAGE_TYPE_NORMAL:
                    default:
                        /*
                         * URIが空のページに対して、/index.html以外（hoge.htmlなど）でアクセスした場合
                         */
                        if (empty($uri) && count($args) > 0 && strstr($args[0], "index.htm") === false) {
                            throw new Exception("存在しないページ");
                        }
                        SOY2::import('site_include.CMSPage');
                        $this->webPage = &SOY2HTMLFactory::createInstance("CMSPage", array(
                            "arguments"=>array($page->getId(), $args, $siteConfig),
                            "siteRoot"=>SOY2PageController::createLink("")
                        ));
                        break;
                }

                $this->webPage->main();

                // プラグインonLoadイベントの呼び出し
                $onLoads = CMSPlugin::getEvent('onPageLoad');
                if (is_array($onLoads) && count($onLoads)) {
                    foreach ($onLoads as $plugin) {
                        $func = $plugin[0];
                        $filter = $plugin[1]['filter'];
                        switch ($filter) {
                            case 'all':
                                call_user_func($func, array('page'=>&$page, 'webPage'=>&$this->webPage));
                                break;
                            case 'blog':
                                if ($page->getPageType() == Page::PAGE_TYPE_BLOG) {
                                    call_user_func($func, array('page'=>&$page, 'webPage'=>&$this->webPage));
                                }
                                break;
                            case 'page':
                                if ($page->getPageType() == Page::PAGE_TYPE_NORMAL) {
                                    call_user_func($func, array('page'=>&$page, 'webPage'=>&$this->webPage));
                                }
                                break;
                        }
                    }
                }

                $this->webPage->parseTime = microtime(true) - $start;

                // 出力
                $html = $this->getOutput($page);
                // プラグイン
                $html = $this->onOutput($html, $page);
                // 文字コード変換
                $html = $this->convertCharset($html);

                // 改行コードを統一しておく
                $html = strtr($html, array("\r\n"=>"\n", "\r"=>"\n"));

                header("Content-Length: " . strlen($html));
                echo $html;
            } catch (Exception $e) {
//              error_log($e->getMessage());
                $this->onNotFound();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $html = '<!DOCTYPE html><title>500 Internal Server Error</title><p>500 Internal Server Error.';
            header("HTTP/1.1 500 Internal Server Error");
            header("Content-Type: text/html; charset=" . SOY2::CHARSET);
            header("Content-Length: " . strlen($html));
//          header("X-Error: 500 Internal Server Error");
            echo $html;
        }
    }

    private function _getPage($uri)
    {
        try {
            return SOY2DAOFactory::create("cms.PageDAO")->getActivePageByUri($uri);
        } catch (Exception $e) {
            return new Page();
        }
    }

    /**
     * 出力内容を取得
     */
    public function getOutput($page)
    {
        ob_start();
        CMSPlugin::callEventFunc("beforeOutput");
        $this->webPage->display();
        $html = ob_get_contents();
        CMSPlugin::callEventFunc('afterOutput', array('html'=>$html, 'page'=>$page, 'webPage'=>$this->webPage));
        ob_end_clean();

        return $html;
    }

    /**
     * onOutputのプラグインを呼び出す。
     */
    public function onOutput($html, $page)
    {
        $onLoads = CMSPlugin::getEvent('onOutput');
        if (!is_array($onLoads) || !count($onLoads)) {
            return $html;
        }
        foreach ($onLoads as $plugin) {
            $func = $plugin[0];
            $res = call_user_func($func, array('html'=>$html, 'page'=>&$page, 'webPage'=>&$this->webPage));
            if (null!==$res && is_string($res)) {
                $html = $res;
            }
        }
        return $html;
    }

    /**
     * 文字コード変換
     */
    public function convertCharset($html)
    {
        $html = $this->webPage->beforeConvert($html);
        $html = $this->siteConfig->convertToSiteCharset($html);
        $html = $this->webPage->afterConvert($html);
        return $html;
    }

    public function onNotFound($path = null, $args = null, $classPath = null)
    {

        $page = SOY2DAOFactory::create("cms.PageDAO")->getErrorPage();
        $this->pageType = $page->getPageType();

        SOY2::import('site_include.CMSPage');
        $this->webPage = &SOY2HTMLFactory::createInstance("CMSPage", array(
        "arguments"=>array($page->getId(), $this->args, $this->siteConfig),
        "siteRoot"=>SOY2PageController::createLink("")
        ));

        $this->webPage->main();

        // 出力
        $html = $this->getOutput($page);
        // プラグイン
        try {
            $html = $this->onOutput($html, $page);
        } catch (Exception $e) {
          // プラグインでの例外は無視
        }
        // 文字コード変換
        $html = $this->convertCharset($html);

        // 404NotFoundが表示される直前で読み込まれる
        CMSPlugin::callEventFunc('onSite404NotFound');

        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/html; charset=" . $this->siteConfig->getCharsetText());
        header("Content-Length: " . strlen($html));
        echo $html;
    }

    /**
     * POSTデータの文字コード変換
     */
    public function convertEncoding($obj = null)
    {
        if (!$obj) {
            $obj = $_POST;
        }

        if (!is_array($obj)) {
            return;
        }

        foreach ($obj as $key => $value) {
            if (is_array($value)) {
                $obj[$key] = $this->convertEncoding($value);
            } else {
                $obj[$key] = $this->siteConfig->convertFromSiteCharset($value);
            }
        }

        return $obj;
    }

    /**
     * 現在の公開セット（記事＋ページ）の有効期限を返す
     * @return Number UnixTime
     */
    public function getCurrentContentsLifetime()
    {
        $minTime = CMSUtil::DATE_MAX;
        if (0 < SOYCMS_CACHE_LIFETIME) {
            $minTime = min($minTime, SOYCMS_NOW + (int)SOYCMS_CACHE_LIFETIME);
        }
        try {
            $entryDao = SOY2DAOFactory::create("cms.EntryDAO");
            $time = $entryDao->getNearestClosingEntry(SOYCMS_NOW);
            if (isset($time)) {
                $minTime = min($minTime, $time);
            }
            $time = $entryDao->getNearestOpeningEntry(SOYCMS_NOW);
            if (isset($time)) {
                $minTime = min($minTime, $time);
            }

            $pageDao = SOY2DAOFactory::create("cms.PageDAO");
            $time = $pageDao->getNearestClosingPage(SOYCMS_NOW);
            if (isset($time)) {
                $minTime = min($minTime, $time);
            }
            $time = $pageDao->getNearestOpeningPage(SOYCMS_NOW);
            if (isset($time)) {
                $minTime = min($minTime, $time);
            }
        } catch (Exception $e) {
            $minTime = 0;
        }

        return $minTime;
    }

    /**
     * ページタイプ（標準、ブログ、携帯、アプリ、…）を返す
     */
    public function getPageType()
    {
        return $this->pageType;
    }
}
