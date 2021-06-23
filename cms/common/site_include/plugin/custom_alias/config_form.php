<?php

class CustomAliasPluginFormPage extends WebPage
{

    private $pluginObj;

    public function __construct()
    {
    }

    public function doPost()
    {
        if (soy2_check_token()) {
            if (isset($_POST["custom_alias_use_id"])) {
                if (isset($_POST["custom_alias_use_id"])) {
                    $this->pluginObj->setUseId($_POST["custom_alias_use_id"]);
                }
                if (isset($_POST["custom_alias_prefix"])) {
                    $this->pluginObj->setPrefix($_POST["custom_alias_prefix"]);
                }
                if (isset($_POST["custom_alias_postfix"])) {
                    $this->pluginObj->setPostfix($_POST["custom_alias_postfix"]);
                }
                if (isset($_POST["custom_alias_labelid"])) {
                    $this->pluginObj->setLabelId($_POST["custom_alias_labelid"]);
                }
                CMSPlugin::savePluginConfig($this->pluginObj->getId(), $this->pluginObj);
            }

            if (isset($_POST["all_change"]) || isset($_POST["all_restore"])) {
                set_time_limit(EXEC_TIME_NO_LIMIT);

                $logic = SOY2Logic::createInstance("logic.site.Entry.EntryLogic");

                //50件ずつ記事を取得
                $offset = 0;
                $dao = SOY2DAOFactory::create("cms.EntryDAO");
                $dao->setLimit(50);

                for (;;) {
                    $dao->setOffset($offset++);
                    try {
                        $entries = $dao->get();
                    } catch (Exception $e) {
                        break;
                    }
                    if (!count($entries)) {
                        break;
                    }

                    foreach ($entries as $entry) {
                        if (isset($_POST["all_change"])) {
                            if ($entry->getId() == $entry->getAlias()) {
                                continue;
                            }
                            $entry->setAlias($entry->getId());
                        } elseif (isset($_POST["all_restore"])) {
                            $alias = $logic->getUniqueAlias($entry->getId(), $entry->getTitle());
                            $entry->setAlias($alias);
                        }

                        try {
                            $dao->update($entry);
                        } catch (Exception $e) {
                            //
                        }
                    }
                }
            }

            CMSPlugin::redirectConfigPage();
        }
    }

    public function execute()
    {
        parent::__construct();

        $this->addForm("custom_alias_form", array());

        $this->addInput("custom_alias_prefix", array(
        "name"=>"custom_alias_prefix",
        "value"=>$this->pluginObj->prefix,
        ));
        $this->addInput("custom_alias_postfix", array(
        "name"=>"custom_alias_postfix",
        "value"=>$this->pluginObj->postfix,
        ));

        $this->addInput("custom_alias_labelid", array(
        "name"=>"custom_alias_labelid",
        "value"=>$this->pluginObj->labelId
        ));

        $this->addCheckBox("use_id", array(
        "name"=>"custom_alias_use_id",
        "value"=>1,
        "selected"=>$this->pluginObj->useId,
        "label"=>"常にIDをエイリアスの値にする（エイリアス入力欄は表示されません）。"
        ));

          $this->addForm("all_change_form");

//      $this->createAdd("ignore","HTMLModel",array(
//          "visible" => false
//      ));
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }

    public function getTemplateFilePath()
    {
        return __DIR__ . "/config_form.html";
    }
}
