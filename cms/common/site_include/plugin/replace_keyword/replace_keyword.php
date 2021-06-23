<?php

class ReplaceKeywordPlugin
{
    const PLUGIN_ID = "replace_keyword";
    const ENTRY_FIELD_ID = self::PLUGIN_ID;
    const ENTRY_FORM_NAME = "entry_attr_replace_keyword";

    private $eaDao = null;
    private $entryKeywordSet = null;
    private $comKeywordSet = null;
    private $useEntryKeyword = false;
    private $labelId = null;

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name" => "キーワード置換プラグイン",
        "description" => "キーワードを指定した文言に置換し, ページを表示します.<br>サイト共通のキーワードと文言, ブログ記事別のキーワードと文言を それぞれ指定できます.",
        "author" => "arbk",
        "url" => "https://aruo.net/",
        "mail" => "",
        "version" => "4.0.1",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this,"config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            // キーワード置換処理
            CMSPlugin::setEvent('onOutput', self::PLUGIN_ID, array($this,"onOutput"));

            if (CMSPlugin::adminModeCheck()) {  // 管理用
                CMSPlugin::setEvent('onEntryRemove', self::PLUGIN_ID, array($this, "onEntryRemove"));
            }

            // ブログ記事別キーワード
            if ($this->useEntryKeyword) {
                $this->eaDao = SOY2DAOFactory::create("cms.EntryAttributeDAO");

                CMSPlugin::setEvent('onPageLoad', self::PLUGIN_ID, array($this,"onPageLoad"), array("filter"=>"blog"));

                if (CMSPlugin::adminModeCheck()) {  // 管理用
                    CMSPlugin::setEvent('onEntryUpdate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
                    CMSPlugin::setEvent('onEntryCreate', self::PLUGIN_ID, array($this, "onEntryUpdate"));
                    CMSPlugin::setEvent('onEntryCopy', self::PLUGIN_ID, array($this, "onEntryCopy"));
                    CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Entry.Detail", array($this, "onCallCustomField"));
                    CMSPlugin::addCustomFieldFunction(self::PLUGIN_ID, "Blog.Entry", array($this, "onCallCustomField_inBlog"));
                }
            }
        }
    }

    public static function register()
    {
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new ReplaceKeywordPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj,"init"));
    }

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public function config_page($message)
    {
        require_once(__DIR__ . "/config_form.php");
        $form = SOY2HTMLFactory::createInstance("ReplaceKeywordPlugin_FormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }

    private function getEntryAttribute($entryId)
    {
        try {
            $attr = $this->eaDao->get($entryId, self::ENTRY_FIELD_ID);
            return $attr->getValue();
        } catch (Exception $e) {
            return null;
        }
    }

    private function parseKeyword($keywordStr)
    {
        // $keywordStr: "##key##":"str"\n"##key##[]":"str"\n...\n"##key##":"str"
        $keywordSet = null;
        if (!empty($keywordStr)) {
            $kset = explode("\n", trim(str_replace(array("\r\n","\r"), "\n", $keywordStr), "\n"));
            foreach ($kset as $v) {
                if (1 === preg_match('/^"(##[a-zA-Z0-9_-]+##\[\])":"(.*)"$/', $v, $m)) {
                    $keywordSet["prm"][$m[1]] = $m[2];  // "##key##[]":"str"
                } elseif (1 === preg_match('/^"(##[a-zA-Z0-9_-]+##)":"(.*)"$/', $v, $m)) {
                    $keywordSet["std"][$m[1]] = $m[2];  // "##key##":"str"
                }
            }
        }
        return $keywordSet;
    }

    private function serializeKeyword($keywordSet)
    {
        $keywordStr = "";
        if (!empty($keywordSet) && is_array($keywordSet)) {
            $inxs = array("std","prm");
            foreach ($inxs as $i) {
                if (empty($keywordSet[$i]) || !is_array($keywordSet[$i])) {
                    continue;
                }
                foreach ($keywordSet[$i] as $k => $v) {
                    $keywordStr .= '"'.$k.'":"'.$v.'"'."\n";
                }
            }
            $keywordStr = rtrim($keywordStr, ",\n");
        }
        return $keywordStr;
    }

    public function onPageLoad($arg)    // $arg : array(page,webPage)
    {
        if ($arg["webPage"]->mode !== CMSBlogPage::MODE_ENTRY) {
            return; // 記事ページでなければ処理を行わない.
        }

        $this->entryKeywordSet = $this->parseKeyword($this->getEntryAttribute($arg["webPage"]->entry->getId()));
    }

    private function replaceKeyword($keywords, &$html)
    {
        if (empty($keywords)) {
            return 0;
        }
        $html = str_replace(array_keys($keywords), $keywords, $html, $count);
        return $count;
    }

    private function replaceKeywordParam($keywords, &$html)
    {
        if (empty($keywords)) {
            return 0;
        }
        $allCount = 0;
        foreach ($keywords as $k => $v) {
            $k = str_replace("[]", "\[(.*?)\]", $k);
            if (0 < preg_match_all('/'.$k.'/', $html, $mchs, PREG_SET_ORDER)) { // $k : ##key##\[(.*?)\]
                foreach ($mchs as $m) {  // m[0] : キーワード全体, m[1] : キーワードの引数部分
                    $rStr = $v;
                    $prms = explode(",", $m[1]);  // 引数（","カンマ区切り）
                    $i = 0;
                    foreach ($prms as $p) {
                        $rStr = str_replace("##".$i."##", $p, $rStr);  // 置換文言に引数を適用
                        $i++;
                    }
                    $html = str_replace($m[0], $rStr, $html, $count);  // キーワードを文言に置換
                    $allCount += $count;
                }
            }
        }
        return $allCount;
    }

    public function onOutput($arg)  // $arg : array(html,page,webPage)
    {
        if (empty($this->comKeywordSet) && empty($this->entryKeywordSet)) {
            return null;  // キーワードが指定されていない.
        }
        if (1 !== preg_match('/##[a-zA-Z0-9_-]+?##/', $arg["html"])) {
            return null;  // ページ・テンプレート または 記事内容 に キーワードが指定されていない.
        }

        $count = 0;
        $html = $arg["html"];

        // #1 サイト共通 キーワード置換（引数付き）
        if (isset($this->comKeywordSet["prm"])) {
            $count += $this->replaceKeywordParam($this->comKeywordSet["prm"], $html);
        }

        // #2 サイト共通 キーワード置換
        if (isset($this->comKeywordSet["std"])) {
            $count += $this->replaceKeyword($this->comKeywordSet["std"], $html);
        }

        // #3 記事別 キーワード置換（引数付き）
        if (isset($this->entryKeywordSet["prm"])) {
            $count += $this->replaceKeywordParam($this->entryKeywordSet["prm"], $html);
        }

        // #4 記事別 キーワード置換
        if (isset($this->entryKeywordSet["std"])) {
            $count += $this->replaceKeyword($this->entryKeywordSet["std"], $html);
        }

        // 置換された要素がない.
        if (0 === $count) {
            return null;
        }

        return $html;
    }

    public function onEntryUpdate($arg) // $arg : array(entry)
    {
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

    public function onEntryCopy($arg)   // $arg : array(oldId,newId)
    {
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

    public function onEntryRemove($arg) // $arg : array(entryIds)
    {
        if ((null===$this->eaDao)) {
            $this->eaDao = SOY2DAOFactory::create("cms.EntryAttributeDAO");
        }
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
 <p class="sub"><label for="' . self::ENTRY_FORM_NAME . '">記事別のキーワードと置換文言 （【書式】 "##KEYWORD##":"文言" KEYWORD:[a-zA-Z0-9_-], 1行1組） (' . self::ENTRY_FIELD_ID . ')</label></p>
 <div>
  <textarea id="' . self::ENTRY_FORM_NAME . '" name="' . self::ENTRY_FORM_NAME . '" style="width:99%; font-size:90%; height:3em;">' . soy2_h($aval) . '</textarea>
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

    public function getComKeywordSet()
    {
        return $this->serializeKeyword($this->comKeywordSet);
    }
    public function setComKeywordSet($comKeywordSet)
    {
        $this->comKeywordSet = $this->parseKeyword($comKeywordSet);
    }

    public function getUseEntryKeyword()
    {
        return $this->useEntryKeyword;
    }
    public function setUseEntryKeyword($useEntryKeyword)
    {
        $this->useEntryKeyword = $useEntryKeyword;
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

ReplaceKeywordPlugin::register();
