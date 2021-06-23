<?php

/*
 * パン屑リスト出力プラグイン
 */
class BreadPlugin
{
    const PLUGIN_ID = "bread";

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    private $separator = "&gt;";

    public function setCms_separator($separator)
    {
        $this->separator = $separator;
    }

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name"=>"パン屑リスト出力プラグイン",
        "description"=>"パン屑リストを出力することが出来ます。",
            "author"=>"株式会社Brassica",
            "url"=>"https://brassica.jp/",
        "mail"=>"soycms@soycms.net",
        "version"=>"1.2",
        "icon"=>__DIR__ . "/icon.gif"
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this, "config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            CMSPlugin::addBlock(self::PLUGIN_ID, "page", array($this, "block"));
        }
    }

    public static function register()
    {
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new BreadPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
    }

    public function config_page($message)
    {
        return file_get_contents(__DIR__ . "/info.html");
    }

    public function block($html, $pageId)
    {
        $pageDao = SOY2DAOFactory::create("cms.PageDAO");

        $buff = array();

        try {
            while (true) {
                $page = $pageDao->getById($pageId);
        //      if(empty($buff)){
        //        $buff[] = $page->getTitle();
        //      }else{
        //        if(defined("CMS_PREVIEW_MODE")){
        //          $link = SOY2PageController::createLink("Page.Preview") ."/". $page->getId();
        //        }else{
                $link = CMSUtil::getSiteUrl() . $page->getUri();
        //        }

                $buff[] = '<a href="' . soy2_h($link) . '">' . soy2_h($page->getTitle()) . '</a>';
        //      }

                $pageId = $page->getParentPageId();

                if (!$pageId) {
                    break;
                }
            }
        } catch (Exception $e) {
        }

        $buff = array_reverse($buff);

        return implode($this->separator, $buff);
    }
}

BreadPlugin::register();
