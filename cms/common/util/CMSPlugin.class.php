<?php
/*
 * definedスイッチ
 * CMS_PLUGIN_ADMIN_MODE true | false
 */
class CMSPlugin
{
    /**
     * イベントを整理
     * 引数はarray
     * 説明の頭に*がついているものはまだ動作しない
     */
    public $_event = array(
        //PathInfoBuilder
        "onPathInfoBuilder"=>array(),   // PathInfoBuilder内    array(uri, args) @return array("uri" => "", args => array())

        // ページ関連
        "onPageLoad"=>array(),      // ページが読み込まれる直前 array(page,webPage)
        "onPageCreate"=>array(),    // ページが作成される直前 array(page)
        "onPageUpdate"=>array(),    // ページが更新される直前 array(new_page,old_page)
        "onPageRemove"=>array(),    // ページが削除される直前 array(pageId)
        "onPutTrash"=>array(),      // ページがゴミ箱に移動したとき array(pageId)
        "onRecover"=>array(),       // ページがゴミ箱から復元されたとき array(pageId)
        "onPageOutput"=>array(),    // ページが出力される前 array($pageObject)
        "onPageEdit"=>array(),      // ページの編集画面が呼び出される直前 array(page)

        //管理画面の拡張
        "onAdminTop" => array(),    // 管理画面トップページの拡張 @params array()、@return array("title" => "", "content" => "")

        // エントリー関連
        "onEntryLoad"=>array(),         // *エントリーが読み込まれる直前 array(entry)
        "onEntryCreate"=>array(),       // エントリーが作成される直後 array(entry)
        "onEntryUpdate"=>array(),       // エントリーが更新される直後 array(entry)
        "onEntryRemove"=>array(),       // エントリーが削除された直後 array(entryIds)
        "onEntryStateChange"=>array(),  // *エントリー公開状態が変更された直後array(entryId,state)
        "onEntryOutput"=>array(),       // エントリーが呼び出された際に呼ばれる array(entryId,SOYHTMLObject,entry)
        "onEntryCopy"=>array(),         // エントリー複製時に呼び出される array(oldId,newId)
        "onSetupWYSIWYG"=>array(),      // WYSIWYGエディタをセットアップしている時 array(entryId, labelIds)

        //記事のCSV
        "onEntryCSVExImport"=>array(),  // インポート、エクスポート時に対象となるプラグインを探す array()
        "onEntryCSVExport"=>array(),    // 記事データのエクスポート array(entryId)
        "onEntryCSVImport"=>array(),    // 記事データのインポート array(entryId, value)

        // ラベル関連
        "onLabelCreate"=>array(),       // ラベルが作成される直前 array(label)
        "onLabelUpdate"=>array(),       // ラベルが更新される直前 array(new_label)
        "onLabelRemove"=>array(),       // ラベルが削除される直前 array(labelId)
        "onLabelOutput"=>array(),       // ラベルが呼び出された際に呼ばれる array(labelId,SOYHTMLObject,label)
        "onLabelSetupWYSIWYG"=>array(), // ラベル詳細でWYSIWYGエディタをセットアップしている時

        // エントリーラベル関連
        "onEntryLabelApply"=>array(),       // エントリーがラベル付けされる直前 array(entryId,labelId)
        "onEntryLabelRemove"=>array(),      // エントリーからラベルが削除される直前 array(entryId,labelId)
        "afterEntryLabelsApply"=>array(),   // エントリーのラベル付けが全部終わったされた直後 array(entryId)

        // サイト関連
        "onSiteCreate"=>array(),    // *サイトが作成される直前
        "onSiteRemove"=>array(),    // *サイトが削除される直前

        // ファイルマネージャ関連
        "onFileUpload"=>array(),  // *ファイルがアップロードされる直前(filename)
        "onFileRemoved"=>array(), // *ファイルが削除される直前(filename)

        // ブログ関連
        "onSubmitComment"=>array(),         // *コメントが投稿されたとき array(entryComment)
        "afterSubmitComment"=>array(),      // *コメントを挿入された後 array(entryComment)
        "onSubmitTrackback"=>array(),       // *トラックバックを受信したとき array(trackback)
        "afterSubmitTrackback"=>array(),    // *トラックバックを挿入した後 array(trackback)
        "onBlogSetupWYSIWYG"=>array(),      // ブログページ詳細でWYSIWYGエディタをセットアップしている時
        "onBlogPageUpdate"=>array(),        // ページが更新される直前    array(new_page,old_page)

        // ブロック関連
        "onBlockLoad"=>array(),     // *ブロックが呼び出される直前(blockId)
        "onBlockCreate"=>array(),   // *ブロックが作成される直前(blockId)
        "onBlockUpdate"=>array(),   // *ブロックが更新される直前(blockId)
        "onBlockRemove"=>array(),   // *ブロックが削除される直前(blockId)

        // プラグインブロック
        "onPluginBlockLoad"=>array(),                   // プラグインブロックが呼び出される直前 array($pageObject)
        "onPluginBlockAdminReturnPluginId"=>array(),    // プラグインブロックの管理画面が表示される直前 array()

        // その他
        "onActive"=>array(),            // プラグインが有効になる直前
        "onDisable"=>array(),           // プラグインが無効になる直前
        "onLoadPageTemplate"=>array(),  // ページのテンプレートが呼び出される直前 array()
        "beforeOutput"=>array(),        // 出力される直前
        "afterOutput"=>array(),         // 出力された直後
        "onOutput"=>array(),            // 出力されるHTMLに対して最後に呼ばれるイベント（最終的に出力されるHTML） array(html,page,webPage),

        //キャッシュの削除
        "onClearCache"=>array(),    //キャッシュの削除の際

        // アクセス関連
        "onSiteAccess"=>array(),        // サイトにアクセスがあった場合 array()
        "onSite404NotFound"=>array()    // 404NotFoundページを開いた時
    );

    public $_plugins = array();

    public $_blocks = array(
      "blog_entry"=>array(), // エントリー内部で使うことができる　引数として、エントリーのIDが渡される
      "page"=>array(),       // 通常のテンプレート内部で使うことが出来る。
    );

    public $_init = array(
      'all'=>array(),
      'blog'=>array(),
      'page'=>array()
    );

    public $_actionTypes = array();

    public $_activeHook = array();

    public $_customFieldFunctions = array();

    private function __construct()
    {
    }

    /* internal functions */


    /* public static functions */

    /**
     * @singleton
     */
    private static function &getInstance()
    {
        static $_static;
        if (!$_static) {
            $_static = new CMSPlugin();
            $_static->loadPlugins();
        }
        return $_static;
    }

    /**
     * プラグインの読み込み
     * プラグインディレクトリ内に配置されたプラグインを読み込みます.
     *
     * 格納形式:
     * プラグインはプラグインファイル（.php）と同名のディレクトリに格納します.
     * 例) example_plugin/example_plugin.php
     *
     * 読み込み順:
     * ディレクトリ名昇順に全てのプラグインを読み込みます.
     * plugin.list ファイルが存在する場合は, そこに記載されたプラグインを記載順に読み込みます.
     * (記載されたプラグインのみを読み込みます.)
     * -- ex: plugin.list --
     * CustomFieldAdvanced
     * admin_tools
     * minify_resource
     * replace_string
     * --
     */
    public function loadPlugins()
    {
        $files = array();
        $plist = CMS_PAGE_PLUGIN . "plugin.list";
        if (is_readable($plist) && is_file($plist)) {
            $files = file($plist, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } else {
            $files = scandir(CMS_PAGE_PLUGIN, SCANDIR_SORT_ASCENDING);
        }
        foreach ($files as $file) {
            $path = CMS_PAGE_PLUGIN . $file . "/";
            if ("." !== $file[0] && is_dir($path)) {
                include_once($path . $file . ".php");
            }
        }
    }

//  /**
//   * プラグインの読み込み
//   * プラグインディレクトリ内に配置されたプラグインを読み込みます.
//   *
//   * 格納形式:
//   * プラグインはプラグインファイル（.php）と同名のディレクトリに格納します.
//   * 例) example_plugin/example_plugin.php
//   *
//   * 読み込み順:
//   * サブディレクトリ 00/ -> 50/ -> 99/ の順に読み込みます.
//   * サブディレクトリ内の読み込み順はファイル名昇順です.
//   *
//   * ・他のプラグインより先に処理を行う必要があるプラグインは 00/ に配置します.
//   * ・通常のプラグインは 50/ に配置します.
//   * ・他のプラグインより後に処理を行う必要があるプラグインは 99/ に配置します.
//   *
//   * プラグインディレクトリ直下のプラグインは読み込みません.
//   * プラグインファイルの実体はプラグインディレクトリ直下に置き, 利用するプラグインへの
//   * リンクを 00/, 50/, 99/ に配置すると良いでしょう.
//   */
//  public function loadPlugins()
//  {
//      $dirs = array("00/", "50/", "99/");
//      foreach ($dirs as $dir) {
//          $dir = CMS_PAGE_PLUGIN . $dir;
//          $files = scandir($dir, SCANDIR_SORT_ASCENDING);
//          foreach ($files as $file) {
//              $path = $dir . $file . "/";
//              if ("." !== $file[0] && is_dir($path)) {
//                  include_once($path . $file . ".php");
//              }
//          }
//      }
//  }
//
//   /**
//    * プラグインの読み込み
//    * プラグイン設置ディレクトリにあるファイルを読み込みます。
//    * CMS_PLUGIN_DIR_/XXXX/XXXX.php
//    */
//   function loadPlugins(){
//     $dir = CMS_PAGE_PLUGIN;
//     $files = scandir(CMS_PAGE_PLUGIN);
//
//     foreach($files as $file){
//       if( $file[0] == "." ) continue;
//       if( !is_dir($dir . $file) ){
//         include_once ($dir . $file);
//       }
//       else{
//         include_once ($dir . $file . "/" . $file . ".php");
//       }
//     }
//   }

    /* 便利関数 */

    /**
     * 有効になっているかチェック
     */
    public static function activeCheck($id)
    {
        if (file_exists(self::getSiteDirectory() . '/.plugin/' . $id . ".active")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 管理モードかチェック
     */
    public static function adminModeCheck()
    {
        return defined("CMS_PAGE_PLUGIN_ADMIN_MODE") && true === CMS_PAGE_PLUGIN_ADMIN_MODE;
    }

    /**
     * サイトのディレクトリを取得
     */
    public static function getSiteDirectory()
    {
        if (defined("_SITE_ROOT_")) {
            return _SITE_ROOT_;
        }
        return UserInfoUtil::getSiteDirectory();
    }

    /**
     * プラグインの追加及び初期化関数の呼び出し
     */
    public static function addPlugin($id, $initFunc)
    {
        $instance = & CMSPlugin::getInstance();
        if (is_array($initFunc) && !method_exists($initFunc[0], $initFunc[1])) {
            return;
        }
        if (!is_array($initFunc) && !function_exists($initFunc)) {
            return;
        }
        $instance->_plugins[$id] = array();
        call_user_func($initFunc);
    }

    /**
     * プラグイン管理にメニューの追加
     */
    public static function addPluginMenu($id, $args)
    {
        if (!self::adminModeCheck()) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_plugins[$id])) {
            return;
        }
        $instance->_plugins[$id] = $args;
    }

    /**
     * 設定メニューの追加
     */
    public static function addPluginConfigPage($id, $func)
    {
        if (!self::adminModeCheck()) {
            return;
        }
        if (!CMSPlugin::activeCheck($id)) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_plugins[$id])) {
            return;
        }
        $instance->_plugins[$id]["config"] = $func;
    }

    /**
     * カスタムメニューの追加
     * @param html メニュー部分に表示するHTML
     * @param alt <a>のalt属性
     */
    public static function addWidget($id, $func, $html = null)
    {
        if (!self::adminModeCheck()) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_plugins[$id])) {
            return;
        }
        if (!CMSPlugin::activeCheck($id)) {
            return;
        }
        if (!isset($instance->_plugins[$id]["custom"])) {
            $instance->_plugins[$id]["custom"] = array();
        }
        $widget = array();
        $widget["func"] = $func;
        $widget["html"] = ($html) ? $html : $id;
        $instance->_plugins[$id]["custom"][] = $widget;
    }

    /**
     * カスタムフィールドの呼び出し
     */
    public static function addCustomFieldFunction($id, $rule, $func, $flag = false)
    {
        if (!CMSPlugin::activeCheck($id)) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_customFieldFunctions[$rule])) {
            $instance->_customFieldFunctions[$rule] = array();
        }
        if ($flag) {
            array_unshift($instance->_customFieldFunctions[$rule], $func);
        } else {
            $instance->_customFieldFunctions[$rule][] = $func;
        }
    }

//   /**
//    * カスタムフィールドの呼び出し(修正前)
//    */
//   static function addCustomFiledFunction($id, $rule, $func, $flag = false){
//     return self::addCustomFieldFunction($id, $rule, $func, $flag);
//   }

    public static function callCustomFieldFunctions($path)
    {
        $instance = & CMSPlugin::getInstance();
        $array = @$instance->_customFieldFunctions[$path];
        if (!is_array($array)) {
            $array = array();
        }
        $html = "";
        foreach ($array as $func) {
            $html .= call_user_func($func);
        }
        return $html;
    }

//   /**
//    * callCustomFieldFunctionsのエイリアス
//    */
//   static function callCustomFiledFunctions($path){
//     return self::callCustomFieldFunctions($path);
//   }

    public static function getPluginMenu($id = null)
    {
        $instance = & CMSPlugin::getInstance();
        if ($id && isset($instance->_plugins[$id])) {
            return $instance->_plugins[$id];
        }
        return $instance->_plugins;
    }

    /**
     * 以下、テンプレートに書くことで動作するプラグイン
     */
    public static function addBlock($id, $type, $func)
    {
        if (!CMSPlugin::activeCheck($id)) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_blocks[$type])) {
            return;
        }
        $instance->_blocks[$type][$id] = $func;
    }

    /**
     * プラグインブロックを複数追加する
     * @param string $pluginId プラグインID
     * @param string $blockId テンプレートに記述するcms:plugin
     * @param string $type
     * @param array $func
     */
    public static function addMultipleBlock($pluginId, $blockId, $type, $func)
    {
        if (!CMSPlugin::activeCheck($pluginId)) {
            return;
        }
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_blocks[$type])) {
            return;
        }
        $instance->_blocks[$type][$blockId] = $func;
    }

    public static function getBlocks($type)
    {
        $instance = & CMSPlugin::getInstance();
        return @$instance->_blocks[$type];
    }

    /**
     * イベントをセットします
     * @param event イベント名
     * @param id プラグインID
     * @param func コールバック
     * @param args イベント引数
     * @param byForce 強制セット アクティブチェックを無視する
     */
    public static function setEvent($event, $id, $func, $args = array(), $byForce = false)
    {
        $instance = & CMSPlugin::getInstance();

        //activeなプラグインだけ追加する
        // onActiveだけ限定的に使用可能
        if ($event !== "onActive" && !CMSPlugin::activeCheck($id) && !$byForce) {
            return;
        }

        if (!isset($instance->_event[$event])) {
            return;
        }
        $old = null;

        if (isset($instance->_event[$event][$id])) {
            $old = $instance->_event[$event][$id];
        }

        $instance->_event[$event][$id] = array(
            $func,
            $args
        );

        return $old;
    }

    /**
     * イベント関数の呼び出し
     * イベント引数で処理をしない場合これを呼び出せば早い
     */
    public static function callEventFunc($event, $arg = array(), $overloadReturn = false)
    {
        $instance = & CMSPlugin::getInstance();
        if (!isset($instance->_event[$event])) {
            throw new Exception("対応していないイベント" . $event . "が呼び出されました");
        }
        $events = $instance->_event[$event];
        $returns = array();
        foreach ($events as $id => $e) {
            $return = call_user_func($e[0], $arg);
            if ($overloadReturn) {
                if (null!==$return) {
                    $returns = $return;
                }
            } else {
                $returns[$id] = $return;
            }
        }
        return $returns;
    }

    /**
      * プラグインIDに限定してイベントを呼び出す
      */
    public static function callLocalPluginEventFunc($event, $pluginId, $arg = array())
    {
        $instance = & CMSPlugin::getInstance();

        if (!isset($instance->_event[$event])) {
            throw new Exception("対応していないイベント" . $event . "が呼び出されました");
        }
        $events = $instance->_event[$event];

        if (!isset($events[$pluginId])) {
            return array();
        }

        return array(
        $pluginId=>call_user_func($events[$pluginId][0], $arg)
        );
    }


    public static function getEvent($event)
    {
        $instance = & CMSPlugin::getInstance();
        return $instance->_event[$event];
    }

  /*
  static function addAction($key,$func){
    $instance =& CMSPlugin::getInstance();

    if(!isset($instance->_actions[$key])){
      $instance->_actions[$key] = array();
    }

    $instance->_actions[$key][] = $func;
  }

  static function fireAction($key,$args = array()){
    $instance =& CMSPlugin::getInstance();

    if(!isset($instance->_actions[$key])){
      return;
    }

    $functions = $instance->_actions[$key];

    foreach($functions as $_key => $function){
      call_user_func_array($function,$args);
    }
  }*/

    /**
     * プラグイン情報の保存
     */
    public static function savePluginConfig($id, $obj)
    {
        $fname = self::getSiteDirectory() . '/.plugin/' . $id . '.config';
        $ret = file_put_contents($fname, serialize($obj));
        if ($ret) {
            @chmod($fname, F_MODE_FILE);
        }
        return $ret;
    }

    /**
     * プラグイン情報の取得
     * @return object
     */
    public static function loadPluginConfig($id)
    {
        $fname = self::getSiteDirectory() . '/.plugin/' . $id . '.config';
        if (file_exists($fname)) {
            return unserialize(file_get_contents($fname));
        } else {
            return null;
        }
    }

    /**
     * 自分自身にリダイレクト
     */
    public static function redirectConfigPage($array = array())
    {
        // ie対策
        $flashSession = SOY2ActionSession::getFlashSession();
        $flashSession->clearAttributes();
        $flashSession->resetFlashCounter();
        $flashSession->setAttribute("config_redirect", $array);
        $url = SOY2PageController::createRelativeLink($_SERVER['REQUEST_URI'], true);
        header("Location: {$url}#config");
        exit();
    }
}
