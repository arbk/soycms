<?php

class CustomAliasPlugin
{
    const PLUGIN_ID = "CustomAlias";

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public $useId;
    public $prefix;
    public $postfix;
    public $labelId;

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name"=>"カスタムエイリアス",
        "description"=>"ブログの記事ページのURLの記事毎に変わる部分（エイリアス）を指定できるようにします.",
            "author"=>"株式会社Brassica",
            "url"=>"https://brassica.jp/",
        "mail"=>"soycms@soycms.net",
        "version"=>"1.3-trv0",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this,"config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            CMSPlugin::setEvent("onEntryCreate", self::PLUGIN_ID, array($this,"onEntryUpdate"));
            CMSPlugin::setEvent("onEntryUpdate", self::PLUGIN_ID, array($this,"onEntryUpdate"));
            CMSPlugin::setEvent("onEntryCopy", self::PLUGIN_ID, array($this,"onEntryCopy"));
            CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Entry.Detail", array($this,"onCallCustomField"));
            CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Blog.Entry", array($this,"onCallCustomField_inBlog"));
        }
    }

    public static function register()
    {
      //このプラグインは管理モードでのみ動作する
        if (!CMSPlugin::adminModeCheck()) {
            return;
        }

        require_once(__DIR__ . "/config_form.php");
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new CustomAliasPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj,"init"));
    }

    public function config_page($message)
    {
        $form = SOY2HTMLFactory::createInstance("CustomAliasPluginFormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }

    public function onEntryCopy($ids)
    {
        $oldId = $ids[0];
        $newId = $ids[1];

        if ($this->useId) {
            $entry = $this->getEntry($newId);
            if ($entry) {
                if ($entry->isEmptyAlias() || $entry->getId() != $entry->getAlias()) {
                    $entry->setAlias($entry->getId());
                    $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
                    $logic->update($entry);
                }
            }
        }
    }

    public function onEntryUpdate($arg)
    {
        $entry = $arg["entry"];
        if ($this->useId) {
            if ($entry->isEmptyAlias() || $entry->getId() != $entry->getAlias()) {
                $entry->setAlias($entry->getId());
                $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
                $logic->update($entry);
            }
        }
    }

    private function buildForm($inBlog = false)
    {
        if ($this->useId) {
            return "";
        }

        $arg = SOY2PageController::getArguments();
        if (!isset($arg[0])) {
            return "";
        }

        $tval = array("alias"=>"", "pageUri"=>"", "entryUri"=>"");

        if ($inBlog) {
          // pageId = arg[0], entryId = arg[1]
            $page = $this->getBlogPage($arg[0]);
            if ((null===$page) || !isset($arg[1])) {
                return "";
            }
            $tval["alias"] = $this->getAlias($arg[1]);
            $tval["pageUri"] = CMSUtil::getSiteUrl() . $page->getEntryPageURL();
            $tval["entryUri"] = $tval["pageUri"] . rawurlencode($tval["alias"]);
        } else {
          // entryId = arg[0]
            $tval["alias"] = $this->getAlias($arg[0]);
            $tval["pageUri"] = CMSUtil::getSiteUrl() . "{blog}/{entry}/";
        }

        $html  = "<div class=\"section custom_alias" . ($this->labelId ? " toggled_by_label_" . soy2_h($this->labelId) . "\" style=\"display:none;" : "") . "\">";
        $html .= "<p class=\"sub\"><label for=\"custom_alias_input\">カスタムエイリアス（ブログのエントリーページのURL）</label></p>";
        $html .= soy2_h($tval["pageUri"]);
        $html .= "<input value=\"" . soy2_h($tval["alias"]) . "\" id=\"custom_alias_input\" name=\"alias\" type=\"text\" style=\"min-width:300px; ";
        if ($inBlog) {
            $html .= "width:57%; \"> <a href=\"" . soy2_h($tval["entryUri"]) . "\" target=\"_blank\">確認</a>";
        } else {
            $html .= "width:60%; \">";
        }
        $html .= "</div>";

        return $html;
    }

    public function onCallCustomField()
    {
        return $this->buildForm();
//      if($this->useId){
//          $html = "";
//      }else{
//          $arg = SOY2PageController::getArguments();
//          $entryId = (isset($arg[0]) && is_numeric($arg[0])) ? (int)$arg[0] : null;
//          $alias = $this->getAlias($entryId);
//
//          $html = "<div style=\"margin:-0.5ex 0px 0.5ex 1em;\">";
//          $html .= "<p class=\"sub\"><label for=\"custom_alias_input\">カスタムエイリアス（ブログのエントリーページのURL）</label></p>";
//          $html .= "<input value=\"".soy2_h($alias)."\" id=\"custom_alias_input\" name=\"alias\" type=\"text\" style=\"width:400px\" />";
//          $html .= "</div>";
//      }
//      return $html;
    }

    public function onCallCustomField_inBlog()
    {
        return $this->buildForm(true);

//      if($this->useId){
//          $html = "";
//      }else{
//          $arg = SOY2PageController::getArguments();
//          $pageId = (isset($arg[0]) && is_numeric($arg[0])) ? (int)$arg[0] : null;
//          $entryId = (isset($arg[1]) && is_numeric($arg[1])) ? (int)$arg[1] : null;
//
//          $page = $this->getBlogPage($pageId);
//          $alias = $this->getAlias($entryId);
//
//          $html = "";
//          if($page){
//              $entryPageUri = CMSUtil::getSiteUrl().$page->getEntryPageURL();
//              $entryUri = $entryPageUri.rawurlencode($alias);
//
//              $html = "<div style=\"margin:-0.5ex 0px 0.5ex 1em;\">";
//              $html .= "<p class=\"sub\"><label for=\"custom_alias_input\">カスタムエイリアス（ブログのエントリーページのURL）</label></p>";
//              $html .= $entryPageUri;
//              $html .= "<input value=\"".soy2_h($alias)."\" id=\"custom_alias_input\" name=\"alias\" type=\"text\" style=\"width:300px\" />";
//              $html .= "<a href=\"".soy2_h($entryUri)."\" target=\"_blank\">確認</a>";
//              $html .= "</div>";
//          }
//      }
//      return $html;
    }

    public function getEntry($entryId)
    {
        try {
            $dao = SOY2DAOFactory::create("cms.EntryDAO");
            $entry = $dao->getById($entryId);
        } catch (Exception $e) {
            return null;
        }
        return $entry;
    }

    public function getAlias($entryId)
    {
        $entry = $this->getEntry($entryId);
        if ($entry) {
            return $entry->getAlias();
        } else {
            return $entryId;
        }
    }

    public function getBlogPage($pageId)
    {
        $dao = SOY2DAOFactory::create("cms.BlogPageDAO");
        try {
            $page = $dao->getById($pageId);
        } catch (Exception $e) {
            return null;
        }
        return $page;
    }

    public function setUseId($useId)
    {
        $this->useId = $useId;
    }
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
    public function setPostfix($postfix)
    {
        $this->postfix = $postfix;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = is_numeric($labelId) ?  $labelId : "";
    }
}

CustomAliasPlugin::register();
