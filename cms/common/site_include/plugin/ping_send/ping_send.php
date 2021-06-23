<?php

class PingSendPlugin
{
    const PLUGIN_ID = "ping_send";

    public function getId()
    {
        return self::PLUGIN_ID;
    }

    //Ping送信先
    public $pingServers = array(
        "http://blogsearch.google.com/ping/RPC2",
        "http://api.my.yahoo.co.jp/RPC2",
        "http://rpc.technorati.com/rpc/ping",
        "http://blog.goo.ne.jp/XMLRPC",
        "http://rpc.reader.livedoor.com/ping",
        "http://ping.myblog.jp",
        "http://www.blogpeople.net/servlet/weblogUpdates",
        "http://ping.bloggers.jp/rpc/",
        "http://rpc.weblogs.com/RPC2",
        "http://ping.fc2.com",
        "http://ping.namaan.net/rpc/",
        "http://ping.rss.drecom.jp/",
        "http://ping.ask.jp/xmlrpc.m",
    );

    //最終送信時刻
    public $lastSendDate = array();

    public function init()
    {
        CMSPlugin::addPluginMenu(self::PLUGIN_ID, array(
        "name"=>"更新Ping送信プラグイン",
        "description"=>"更新Pingを送信することが出来ます。",
        "author"=>"株式会社Brassica",
        "url"=>"https://brassica.jp/",
        "mail"=>"soycms@soycms.net",
        "version"=>"1.1-trv0",
        "icon"=>__DIR__ . "/icon.gif",
        ));
        CMSPlugin::addPluginConfigPage(self::PLUGIN_ID, array($this,"config_page"));
    }

    public static function register()
    {
      //このプラグインは管理モードでのみ動作する
        if (!CMSPlugin::adminModeCheck()) {
            return;
        }

        $obj = CMSPlugin::loadPluginConfig(self::PLUGIN_ID);
        if ((null===$obj)) {
            $obj = new PingSendPlugin();
        }
        CMSPlugin::addPlugin(self::PLUGIN_ID, array($obj, "init"));
    }


    public function config_page()
    {
      //Pingサーバの情報を取得
        if (isset($_POST["update_ping_server"])) {
            $this->pingServers = explode("\n", $_POST["ping_server"]);
            $this->pingServers = array_unique($this->pingServers);
            CMSPlugin::savePluginConfig(self::PLUGIN_ID, $this);
            CMSPlugin::redirectConfigPage();
            exit;
        }

      //Ping送信
        if (isset($_POST["send_ping"])) {
            $id = $_POST["blog_id"];

            $now = SOYCMS_NOW;
            if (strlen($id) && is_numeric($id)) {
                set_time_limit(EXEC_TIME_LONG);
                $id = (int)$id;
                $blogDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
                $sendTrace = $this->sendUpdatePings($id, $this->pingServers, $blogDAO);
                $this->lastSendDate[$id] = $now;
                CMSPlugin::savePluginConfig(self::PLUGIN_ID, $this);
            }

            $html = array();
            $html[] = "<!DOCTYPE html><html><head><title>-</title></head><body>";
            $html[] = "<script type=\"text/javascript\">";
            $html[] = "var ele = window.parent.document.getElementById('send_ping_button_$id');";
            $html[] = "if(ele){ ele.removeAttribute('disabled'); } ";
            $html[] = "var ele = window.parent.document.getElementById('loading_$id');";
            $html[] = "if(ele){ ele.style.visibility='hidden' } ";
            $html[] = "var ele = window.parent.document.getElementById('last_send_ping_$id');";
            $html[] = "if(ele){ ele.innerHTML = '".date("Y-m-d H:i:s", $now)."'; } ";
            $html[] = "var ele = window.parent.document.getElementById('send_result');";
            $html[] = "if(ele){ ele.style.display='block' } ";
            $html[] = "var ele = window.parent.document.getElementById('send_result_data');";
            $html[] = "if(ele){ ele.innerHTML = '".str_replace(array("\r\n","\r","\n"), '<br>', soy2_h(implode("\n", $sendTrace)))."'; } ";
            $html[] = "</script>";
            $html[] = "</body></html>";
            echo implode("\n", $html);

            exit;
        }

      //全ブログページを取得
        $blogDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
        $blogs = $blogDAO->get();

        ob_start();
        include(__DIR__ . "/config.php");
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

    public function sendUpdatePings($id, $servers, $blogDAO)
    {
        try {
            $blogPage = $blogDAO->getById($id);
        } catch (Exception $e) {
            return null;
        }

        $title = $blogPage->getTitle();
        if (strlen($blogPage->getUri()) > 0) {
            $url = UserInfoUtil::getSiteURL() . $blogPage->getUri() . "/";
        } else {
            $url = UserInfoUtil::getSiteURL();
        }

        $sendTrace = array();
        foreach ($servers as $value) {
            $value = trim($value);
            if (strlen($value) < 1) {
                continue;
            }

//          $urls = parse_url($value);
//          $host = $urls["host"];
//          $path = @$urls["path"];

//          $sendTrace[] = "[Send Data: " . $host . ", " . $path . ", " . $title . ", " . $url . "]";
//          list($req, $result) = $this->sendUpdatePing($host, $path, $title, $url);
//          $sendTrace[] = $req;

            $sendTrace[] = "[Send Data: " . $value . ", " . $title . ", " . $url . "]";
            $result = $this->sendUpdatePing($value, $title, $url);
            $sendTrace[] = $result;
            $sendTrace[] = "\n";
        }
        return $sendTrace;
    }

    public function sendUpdatePing($server, $title, $url)
    {
        @set_time_limit(EXEC_TIME_LONG);

        // content
        $content = '<?xml version="1.0" encoding="' . SOY2::CHARSET . '" ?><methodCall><methodName>weblogUpdates.ping</methodName><params><param><value>'
        . soy2_h($title) . '</value></param><param><value>' . $url . '</value></param></params></methodCall>';

      // header
        $header = array(
        "Content-Type: text/xml",
        "Content-Length: " . strlen($content),
        "Connection: close"
        );

        $options = array(
        "http" => array(
            "method" => "POST",
            "header" => implode("\r\n", $header),
            "content" => $content,
  //          "ignore_errors" => true  // 有効にすると 404 や 500 の場合も $res に false ではなく レスポンスボディ が入る.
        )
        );

        $http_response_header = array();
        $res = @file_get_contents($server, false, stream_context_create($options));

        if (false === $res) {
            $err_str = "weblogUpdates.ping failed. : ";
            if (0 < count($http_response_header)) {
              // エラーレスポンスあり
                $err_str .= $http_response_header[0]; // 0:Status-Line
            } else {
              // タイムアウト or 送信先サーバーなし
                $err_str .= "Timeout or Unknown server";
            }
            $err_str .= " (server: " . $server . ", url: " . $url . ") : ".__METHOD__;
            error_log($err_str);
        }

        return $res;
    }

/*
  function sendUpdatePing($host, $path, $title, $url){
    $sock = @fsockopen($host, 80, $errno, $errstr, 2.0);
    $req = "";
    $result = "";
    if($sock){
      $title_str = soy2_h($title);
      $content =
           "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n" .
           "<methodCall>\r\n" .
           "<methodName>weblogUpdates.ping</methodName>\r\n" .
           "<params>\r\n" .
           "<param>\r\n" .
           "<value>$title_str</value>\r\n" .
           "</param>\r\n" .
           "<param>\r\n" .
           "<value>$url</value>\r\n" .
           "</param>\r\n" .
           "</params>\r\n" .
           "</methodCall>\r\n";
      $length = strlen($content);
      $req = "POST $path HTTP/1.0\r\n" .
           "Host: $host\r\n" .
           "Content-Type: text/xml\r\n" .
           "Content-Length: $length\r\n" .
           "Connection: close\r\n" .
           "\r\n" . $content . "\r\n\r\n";
      fputs($sock, $req);
      while(!feof($sock)){
        $result .= fread($sock, 1024);
      }
      fclose($sock);
    }
    return array($req, $result);
  }
*/
}

PingSendPlugin::register();
