<?php

class ReplaceStringPlugin
{
    const PLUGIN_ID = "replace_string";
    const ENTRY_FIELD_ID = self::PLUGIN_ID;
    const DELIMITER = ",";

    private $eLlogic = null;
    private $eaDao = null;
    private $comStringSet = null;
    private $entryFieldIds = null;
    private $labelId = null;

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name" => "文字列置換プラグイン",
        "description" => "記事更新時に文字列を置換します. 置換対象は 記事の 本文（content）, 追記（more）, 各フィールド（カスタムフィールドを含む） です.",
        "author" => "arbk",
        "url" => "https://aruo.net/",
        "mail" => "",
        "version" => "1.0.1",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this, "config_page"));

        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            $this->eLlogic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");
            $this->eaDao = SOY2DAOFactory::create("cms.EntryAttributeDAO");

            CMSPlugin::setEvent("onEntryCreate", self::PLUGIN_ID, array($this, "onEntryUpdate"));
            CMSPlugin::setEvent("onEntryUpdate", self::PLUGIN_ID, array($this, "onEntryUpdate"));
        }
    }

    public static function register()
    {
        //このプラグインは管理モードでのみ動作する
        if (!CMSPlugin::adminModeCheck()) {
            return;
        }

        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new ReplaceStringPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
    }

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    public function config_page($message)
    {
        require_once(__DIR__ . "/config_form.php");
        $form = SOY2HTMLFactory::createInstance("ReplaceStringPlugin_FormPage");
        $form->setPluginObj($this);
        $form->execute();
        return $form->getObject();
    }

    private function replaceString($strings, &$html)
    {
        if (empty($strings)) {
            return false;
        }
        $html = str_replace(array_keys($strings), $strings, $html, $count);
        return (0 < $count);
    }

    private function updateEntry($entry)
    {
        $repd = false;

        // #1 文字列置換
        if (isset($this->comStringSet["std"])) {
            // content
            $html = $entry->getContent();
            if ($this->replaceString($this->comStringSet["std"], $html)) {
                $entry->setContent($html);
                $repd = true;
            }

            // more
            $html = $entry->getMore();
            if ($this->replaceString($this->comStringSet["std"], $html)) {
                $entry->setMore($html);
                $repd = true;
            }
        }

        // #2 正規表現置換
//      if( isset($this->comStringSet["prg"]) ){}

        if ($repd) {  // 置換実行
            try {
                $this->eLlogic->update($entry);
            } catch (Exception $e) {
                error_log($e->getMessage() . " (entryId=" . $entry->getId() . ") : " . __METHOD__);
            }
        }
    }

    private function updateEntryAttribute($entryId)
    {
        if (empty($this->entryFieldIds)) {
            return; // 置換対象のエントリーフィールドが指定されていない.
        }

        $efIds = explode(self::DELIMITER, $this->entryFieldIds);
        foreach ($efIds as $efId) {
            $efId = trim($efId);
            if (0 >= strlen($efId)) {
                continue;
            }

            $attr = null;
            try {
                $attr = $this->eaDao->get($entryId, $efId);
                // エントリーフィールドが先に更新されているのが前提の処理.
            } catch (Exception $e) {
                $attr = null;
            }

            if ((null===$attr)) {
                continue;
            }

            $aval = $attr->getValue();
            if (0 < strlen($aval)) {
                $repd = false;

                // #1 文字列置換
                if (isset($this->comStringSet["std"])) {
                    if ($this->replaceString($this->comStringSet["std"], $aval)) {
                        $attr->setValue($aval);
                        $repd = true;
                    }
                }

                // #2 正規表現置換
//              if( isset($this->comStringSet["prg"]) ){}

                if ($repd) {  // 置換実行
                    try {
                        $this->eaDao->update($attr);
                    } catch (Exception $e) {
                        error_log($e->getMessage() . " (entryId=" . $entryId . ", entryFieldId=" . $efId . ") : " . __METHOD__);
                    }
                }
            }
        }
    }

    public function onEntryUpdate($arg) // $arg : array(entry)
    {
        if (empty($this->comStringSet)) {
            return; // 置換文字列が指定されていない.
        }

        $entry = $arg["entry"];

        if (null!==$this->labelId) {  // ラベルとの関連付け チェック
            $labels = $this->eLlogic->getLabelIdsByEntryId($entry->getId());
            // 懸案: 記事画面でラベルを更新した場合, 更新後のラベルIDがとれない (更新前のIDが入っている).
            if (!in_array($this->labelId, $labels)) {
                return;
            }
        }

        // 本文（content）, 追記（more）の更新
        $this->updateEntry($entry);

        // カスタムフィールド の更新
        $this->updateEntryAttribute($entry->getId());
    }

    private function parseString($stringStr)
    {
        // $stringStr: "search_str":"replace_str"\n"search_str":"replace_str"\n...\n"search_str":"replace_str"
        $stringSet = null;
        if (!empty($stringStr)) {
            $kset = explode("\n", trim(str_replace(array("\r\n","\r"), "\n", $stringStr), "\n"));
            foreach ($kset as $v) {
//              if( 1 === preg_match('/^"(.+)":"(.*)"$/', $v, $m) ){
//                $stringSet["prg"][$m[1]] = $m[2];  // "search_str*":"replace_str"
//              }
//              else
                if (1 === preg_match('/^"(.+)":"(.*)"$/', $v, $m)) {
                    $stringSet["std"][$m[1]] = $m[2];  // "search_str":"replace_str"
                }
            }
        }
        return $stringSet;
    }

    private function serializeString($stringSet)
    {
        $stringStr = "";
        if (!empty($stringSet) && is_array($stringSet)) {
            $inxs = array("std","prg");
            foreach ($inxs as $i) {
                if (empty($stringSet[$i]) || !is_array($stringSet[$i])) {
                    continue;
                }
                foreach ($stringSet[$i] as $k => $v) {
                    $stringStr .= '"'.$k.'":"'.$v.'"'."\n";
                }
            }
            $stringStr = rtrim($stringStr, ",\n");
        }
        return $stringStr;
    }

    public function getComStringSet()
    {
        return $this->serializeString($this->comStringSet);
    }
    public function setComStringSet($comStringSet)
    {
        return $this->comStringSet = $this->parseString($comStringSet);
    }

    public function getEntryFieldIds()
    {
        return $this->entryFieldIds;
    }
    public function setEntryFieldIds($entryFieldIds)
    {
        $this->entryFieldIds = $entryFieldIds;
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

ReplaceStringPlugin::register();
