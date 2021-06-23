<?php
//ini_set("display_errors", 1);

class MinifyResourcePlugin
{
    const PLUGIN_ID = "minify_resource";

    public $minCss  = false;
    public $genCss  = false;
    public $minJs   = false;
    public $genJs   = false;
    public $minXml  = false;
    public $genXml  = false;
    public $minHtml = false;

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name" => "リソース軽量化プラグイン",
        "description" => "CSS/JavaScript/XML/HTML から 空白 や 改行, コメント など余分な要素を削除して, コードを軽量化（圧縮）するプラグインです.",
        "author" => "arbk",
        "url" => "https://aruo.net/",
        "mail" => "",
        "version" => "3.1",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this, "config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            CMSPlugin::setEvent("onPageUpdate", self::PLUGIN_ID, array($this, "onPageUpdate"));
            CMSPlugin::setEvent("onOutput", self::PLUGIN_ID, array($this, "onOutput"));
        }
    }

    public static function register()
    {
        require_once(__DIR__ . "/config_form.php");
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new MinifyResourcePlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
    }

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public function config_page($message)
    {
        $form = SOY2HTMLFactory::createInstance("MinifyResourcePlugin_FormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }

    public function onPageUpdate($arg)
    {
        // $arg : array(new_page,old_page)
        $res_real_path = $this->getResRealPath($arg["new_page"]);
        if ((null!==$res_real_path) && is_file($res_real_path)) {
            unlink($res_real_path);
        }
    }

    public function onOutput($arg)
    {
        // $arg : array(html,page,webPage)
        $res_path = $arg["page"]->getUri();
        if ("CMSBlogPage" === get_class($arg["webPage"]) && CMSBlogPage::MODE_ENTRY === $arg["webPage"]->mode) {
//          $res_path = $res_path."/".$arg["page"]->getPageConfigObject()->entryPageUri."/".rawurlencode($arg["webPage"]->entry->getAlias());
            $res_path = $arg["webPage"]->entry->getAlias();
        }
        $html = $this->minify(pathinfo($res_path, PATHINFO_EXTENSION), $arg["html"]);

        $this->genRealFile($html, $arg["page"]);

        return $html;
    }

    private function genRealFile($html, $page)
    {
        $res_real_path = $this->getResRealPath($page);
        if ((null!==$res_real_path) && !is_file($res_real_path)) {
            file_put_contents($res_real_path, $html);
            @chmod($res_real_path, F_MODE_FILE);
        }
    }

    private function getSiteDir()
    {
        if (defined("_SITE_ROOT_")) {
            return _SITE_ROOT_."/";
        } elseif (class_exists("UserInfoUtil")) {
            return UserInfoUtil::getSiteDirectory();
        } else {
            return null;
        }
    }

    private function getResRealPath($page)
    {
        if (!empty($page->getPageType()) // 空の場合は標準ページ
        && Page::PAGE_TYPE_NORMAL !== $page->getPageType()) {
            return null; // 標準ページのみ生成する.
        }

        $res_path = $page->getUri();
        $res_type=pathinfo($res_path, PATHINFO_EXTENSION);
        if (("css"!==$res_type && "js"!==$res_type && "xml"!==$res_type) ||
        ("css"===$res_type && !$this->genCss) ||
        ("js" ===$res_type && !$this->genJs)  ||
        ("xml"===$res_type && !$this->genXml)) {
            return null; // 生成対象ではない.
        }

        $site_dir = $this->getSiteDir();
        if ((null===$site_dir)) {
            error_log("Site dir is null. : ".__METHOD__);
            return null;
        }
        $res_real_path = $site_dir.$res_path;

        if (!is_dir(dirname($res_real_path))               // 出力先DIRがない
        || is_dir($res_real_path)                          // 同名のDIRがある
        || 1===preg_match("#/\.[^/]+/.*#", $res_real_path) // パスに上位／隠しDIRが含まれる
        ) {
            error_log("Output file path is invalid. [Path: ".$res_real_path."] : ".__METHOD__);
            return null;
        }

        return $res_real_path;
    }

    private function minify($res_type, $conts, $false_ret = null)
    {
        require_once(__DIR__ . "/lib/xMin/xMin.php");
        if ("css"===$res_type) {
            return $this->minCss ? xMin::minifyCss($conts) : $false_ret;
        } elseif ("js"===$res_type) {
            return $this->minJs ? xMin::minifyJs($conts) : $false_ret;
        } elseif ("xml"===$res_type) {
            return $this->minXml ? xMin::minifyHtml($conts) : $false_ret;
        } else { // html or other
            return $this->minHtml ? xMin::minifyHtml($conts) : $false_ret;
        }
    }
}

MinifyResourcePlugin::register();
