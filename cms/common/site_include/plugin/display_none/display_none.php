<?php

class DisplayNonePlugin
{
    const PLUGIN_ID = "display_none";
    const ENTRY_FIELD_ID = self::PLUGIN_ID;
    const ENTRY_FORM_NAME = "entry_attr_display_none";
    const DELIMITER = ",";

    private $eaDao = null;
    private $removeIds = null;
    private $labelId = null;

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name"=>"HTML要素の非表示プラグイン",
        "description"=>"ブログ記事において, 指定されたidのHTML要素を非表示にします.",
        "author"=>"arbk",
        "url"=>"https://aruo.net/",
        "mail"=>"",
        "version"=>"2.0",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this, "config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            $this->eaDao = SOY2DAOFactory::create("cms.EntryAttributeDAO");

            CMSPlugin::setEvent('onPageLoad', self::PLUGIN_ID, array($this, "onPageLoad"), array("filter"=>"blog"));
            CMSPlugin::setEvent('onOutput', self::PLUGIN_ID, array($this, "onOutput"));

            if (CMSPlugin::adminModeCheck()) {  // 管理用
                CMSPlugin::setEvent('onEntryUpdate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
                CMSPlugin::setEvent('onEntryCreate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
                CMSPlugin::setEvent('onEntryCopy', self::PLUGIN_ID, array($this, "onEntryCopy"));
                CMSPlugin::setEvent('onEntryRemove', self::PLUGIN_ID, array($this, "onEntryRemove"));

                CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Entry.Detail", array($this, "onCallCustomField"));
                CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Blog.Entry", array($this, "onCallCustomField_inBlog"));
            }
        }
    }

    public static function register()
    {
        $obj = CMSPlugin::loadPluginConfig(DisplayNonePlugin::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new DisplayNonePlugin();
        }
        CMSPlugin::addPlugin(DisplayNonePlugin::PLUGIN_ID, array($obj, "init"));
    }

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public function config_page($message)
    {
        require_once(__DIR__ . "/config_form.php");
        $form = SOY2HTMLFactory::createInstance("DisplayNonePlugin_FormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }

    private function getEntryAttribute($entryId, $doParse = false)
    {
        $aval = null;
        try {
            $attr = $this->eaDao->get($entryId, self::ENTRY_FIELD_ID);
            $aval = $attr->getValue();
            if ($doParse && 0 < strlen($aval)) {
                $aval = explode(self::DELIMITER, $aval);
            }
        } catch (Exception $e) {
            return null;
        }
        return $aval;
    }

    public function onPageLoad($arg)
    {
  // $arg : array(page,webPage)
        if (!isset($arg["webPage"]->mode) || $arg["webPage"]->mode !== CMSBlogPage::MODE_ENTRY) {
            return;
        } // 記事ページでなければ処理を行わない.
        $this->removeIds = $this->getEntryAttribute($arg["webPage"]->entry->getId(), true);
    }

    public function onOutput($arg)
    {
  // $arg : array(html,page,webPage)
        if (!isset($arg["webPage"]->mode) || $arg["webPage"]->mode !== CMSBlogPage::MODE_ENTRY) {
            return null;
        } // 記事ページでなければ処理を行わない.
        if (empty($this->removeIds)) {
            return null;
        } // 非表示idが指定されていない.

      // DOM取得 : "WARNING" & "<meta http-equiv=...> がない場合の文字化け" に対処
        $doc = new DOMDocument();
        $prv_use = libxml_use_internal_errors(true);
        $ret = $doc->loadHTML('<?xml encoding="' . SOY2::CHARSET . '">' . $arg["html"]);
        libxml_clear_errors();
        libxml_use_internal_errors($prv_use);
        if (!$ret) {
            return null;
        }

      // 要素削除
        $exd = false;
        try {
            foreach ($this->removeIds as $val) {
                $val = trim($val);
                if (0 >= strlen($val)) {
                    continue;
                }
                $em = $doc->getElementById($val);
                if ((null===$em) || (null===$em->parentNode)) {
                    continue;
                }
                $em->parentNode->removeChild($em);
                $exd = true;
            }
        } catch (Exception $e) {
            $exd = false;
            error_log("Removing element is failed. : " . __METHOD__ . "\n" . $e->getMessage());
        }
        if (!$exd) {
            return null;
        }  // 削除された要素がない.

        return $doc->saveXML($doc->doctype) . $doc->saveHTML($doc->documentElement);
    }

    public function onEntryUpdate($arg)
    {
 // $arg : array(entry)
        $entryId = $arg["entry"]->getId();

        try {
            $this->eaDao->delete($entryId, self::ENTRY_FIELD_ID);
        } catch (Exception $e) {
        }

        if (isset($_POST[self::ENTRY_FORM_NAME]) && 0 < strlen($_POST[self::ENTRY_FORM_NAME])) {
            try {
                $obj = new EntryAttribute();
                $obj->setEntryId($entryId);
                $obj->setFieldId(self::ENTRY_FIELD_ID);
                $obj->setValue($_POST[self::ENTRY_FORM_NAME]);
                $this->eaDao->insert($obj);
            } catch (Exception $e) {
                error_log($e->getMessage() . " (entryId=". $entryId .") : " . __METHOD__);
            }
        }
        return true;
    }

    public function onEntryCopy($arg)
    {
 // $arg : array(oldId,newId)
        list($oldId, $newId) = $arg;
        $aval = $this->getEntryAttribute($oldId);
        if (0 < strlen($aval)) {
            try {
                $obj = new EntryAttribute();
                $obj->setEntryId($newId);
                $obj->setFieldId(self::ENTRY_FIELD_ID);
                $obj->setValue($aval);
                $this->eaDao->insert($obj);
            } catch (Exception $e) {
                error_log($e->getMessage() . " (oldId=" . $oldId . ", newId=" . $newId . ") : " . __METHOD__);
            }
        }
        return true;
    }

    public function onEntryRemove($arg)
    {
  // $arg : array(entryIds)
        foreach ($arg as $entryId) {
            try {
                $this->eaDao->delete($entryId, self::ENTRY_FIELD_ID);
            } catch (Exception $e) {
                error_log($e->getMessage() . " (entryId=". $entryId .") : " . __METHOD__);
            }
        }
        return true;
    }

    private function buildForm($inBlog = false)
    {
        $arg = SOY2PageController::getArguments();

        $entryId = null;
        if ($inBlog) {
          // pageId = arg[0], entryId = arg[1]
            if (!isset($arg[1])) {
                return "";
            }
            $entryId = $arg[1];
        } else {
          // entryId = arg[0]
            if (!isset($arg[0])) {
                return "";
            }
            $entryId = $arg[0];
        }
        $aval = $this->getEntryAttribute($entryId);

        $html = '<div class="section' . ($this->labelId ? ' toggled_by_label_' . soy2_h($this->labelId) . '" style="display:none;' : '') . '">
 <p class="sub"><label for="' . self::ENTRY_FORM_NAME . '">非表示にするHTML要素のid (","カンマ区切り) (' . self::ENTRY_FIELD_ID . ')</label></p>
 <div>
  <input id="' . self::ENTRY_FORM_NAME . '" name="' . self::ENTRY_FORM_NAME . '" value="' . soy2_h($aval) . '" type="text" style="width:99%;">
 </div>
</div>';

        return $html;
    }

    public function onCallCustomField()
    {
        return $this->buildForm();
    }
    public function onCallCustomField_inBlog()
    {
        return $this->buildForm(true);
    }

    public function getLabelId()
    {
        return $this->labelId;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = is_numeric($labelId) ? $labelId : null;
    }
}

DisplayNonePlugin::register();
