<?php
/*
 * 簡易コメントスパム対策プラグイン
 */

class SOYCMS_SpamBlockerPlugin
{
    const PLUGIN_ID = "soycms_spam_blocker";

    private $useKeyword = false;
    private $prohibitionWords = array();
    private $name = "keyword";
    private $keyword = "確認";

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    /**
     * 初期化
     */
    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name"=>"コメントスパム対策プラグイン",
        "description"=>"ブログのコメントのスパムを対策します",
            "author"=>"株式会社Brassica",
            "url"=>"https://brassica.jp/",
        "mail"=>"soycms@soycms.net",
        "version"=>"1.0.1",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this,"config_page"));
        if (CMSPlugin::activeCheck(self::PLUGIN_ID)) {
            CMSPlugin::setEvent('onSubmitComment', self::PLUGIN_ID, array($this,"onSubmitComment"));
        }
    }

    /**
     * 設定画面
     */
    public function config_page()
    {
        if (isset($_POST["save"])) {
            $this->useKeyword = (boolean)$_POST["useKeyword"];
            $this->keyword = $_POST["keyword"];
            $this->name = $_POST["name"];
            $this->prohibitionWords = explode("\n", $_POST["prohibitionWords"]);
            $this->prohibitionWords = array_map("trim", $this->prohibitionWords);

            CMSPlugin::savePluginConfig(self::PLUGIN_ID, $this);
            CMSPlugin::redirectConfigPage();

            exit;
        }


        ob_start();
        include(__DIR__ . "/config.php");
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * コメント投稿
     */
    public function onSubmitComment($args)
    {
        $comment = @$args["entryComment"];

        if ($this->useKeyword) {
            if (!isset($_POST[$this->name])) {
                return false;
            }
            if ($_POST[$this->name] != $this->keyword) {
                return false;
            }
        }

        $before = $comment->getBody();
        $after = str_replace($this->prohibitionWords, "", $before);

        if ($before != $after) {
            return false;
        }
    }

  /**
   * プラグインの登録
   */
    public static function registerPlugin()
    {
        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new SOYCMS_SpamBlockerPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj,"init"));
    }
}

SOYCMS_SpamBlockerPlugin::registerPlugin();
