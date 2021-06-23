<?php

class SendTrackbackAction extends SOY2Action
{
    private $id;
    private $pageId;

    public function execute($response, $form, $request)
    {
//      @set_time_limit(0);

        try {
            $dao = SOY2DAOFactory::create("cms.EntryDAO");
            $dao->setIgnoreColumns(array("more"));
            $entry = $dao->getById($this->id);
            $pageDAO = SOY2DAOFactory::create("cms.BlogPageDAO");
            $page = $pageDAO->getById($this->pageId);

            // 記事のURLの作成 送信先のDBの長さ対策用に、確実に届くであろうIDで。
            $url = UserInfoUtil::getSiteURL();
            if (strlen($page->getUri())) {
                $url .= $page->getUri() . '/';
            }
            $url .= $page->getEntryPageUri() . '/' . $entry->getId(); // rawurlencode($entry->getAlias());

            foreach ($form->trackback as $trackbackURL) {
                if (trim($trackbackURL) == "") {
                    continue;
                }
                $this->SendTrackback(
                    $trackbackURL,
                    $page->getTitle(),
                    $entry->getTitle(),
                    mb_strcut(strip_tags($entry->getContent()), 0, 252),
                    $url
                );
            }
            return SOY2Action::SUCCESS;
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
    }

    /**
     * トラックバックを送る
     * @param String $server 送信先サーバのURL
     * @param String $name ブログ名／サイト名
     * @param String $title 記事タイトル
     * @param String $excerpt 記事概要
     * @param String $url 記事URL
     */
    public function SendTrackback($server, $name, $title, $excerpt, $url)
    {
        @set_time_limit(EXEC_TIME_LONG);

        // content
        $content = array(
        "title"     => $title,
        "url"       => $url,
        "blog_name" => $name,
        "excerpt"   => $excerpt,
        );
        $content = http_build_query($content, "", "&");

        // header
        $header = array(
        "Content-Type: application/x-www-form-urlencoded",
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
            $err_str = "Trackback failed. : ";
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


//      // 送信先サーバURLをホスト名とパス名に分解する
//      $arr = parse_url($server);
//
//      $host = $arr["host"]; // ホスト名
//      $path = $arr["path"]; // パス名
//      $port = @$arr["port"]; // ポート
//      $query = @$arr['query'];
//      if( strlen($port) < 1 ) $port = 80;
//      if( strlen($host) < 1 ) return (-1);
//
//      // 送信先サーバをオープンする
//      $sock = fsockopen($host, $port, $errno, $errstr, 60);
//
//      if( $sock == FALSE ) return (-1);
//
//      // トラックバック文字列をつくる
//      $str = "title=" . rawurlencode($title);
//      $str .= "&url=" . rawurlencode($url);
//      $str .= "&blog_name=" . rawurlencode($name);
//      $str .= "&excerpt=" . rawurlencode($excerpt);
//
//      // エンティティボディ
//      $request_path = (strlen($query) > 0) ? "$path?$query" : $path;
//      $request_host = ($port != "80") ? "$host:$port" : $host;
//
//      $body = "";
//      $body .= "POST $request_path HTTP/1.1\r\n";
//      $body .= "Host: $request_host\r\n";
//      $body .= "Content-type: application/x-www-form-urlencoded\r\n";
//      $body .= "Content-length: " . strlen($str) . "\r\n";
//      $body .= "\r\n";
//      $body .= $str;
//
//      fputs($sock, $body);
//
//      // fread
//      $buf = "";
//      while(!feof($sock)){
//        $buf = $buf . fgets($sock, 128);
//      }
//
//      // ソケットがタイムアウトしたかどうか調べる
//      $stat = socket_get_status($sock);
//      if( $stat["timed_out"] ){return -1;}
//
//      // 飛ばしっぱなしで終わり
//      fclose($sock);
//      return 1;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }
}

class SendTrackbackActionForm extends SOY2ActionForm
{
    public $trackback;

    public function setTrackback($trackback)
    {
        if (strlen(trim($trackback)) == 0) {
            $this->trackback = array();
        } else {
            $order = array("\r\n", "\n", "\r");
            $trackback = str_replace($order, '##LINE_BREAK##', $trackback);
            $this->trackback = explode("##LINE_BREAK##", $trackback);
            $this->trackback = array_diff($this->trackback, array(""));
        }
    }
}
