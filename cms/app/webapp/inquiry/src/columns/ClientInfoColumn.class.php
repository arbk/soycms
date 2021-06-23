<?php

/**
 * クライアント情報を記録します.
 */
class ClientInfoColumn extends SOYInquiry_ColumnBase
{
    private $client_info;

    /**
    * ユーザに表示するようのフォーム
    */
    public function getForm($attr = array())
    {
        $this->client_info = array();
        $this->client_info['remote_addr']  = isset($_SERVER['REMOTE_ADDR'])          ? $_SERVER['REMOTE_ADDR']                   : null;
        $this->client_info['remote_addr'] .= isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? " (".$_SERVER['HTTP_X_FORWARDED_FOR'].")" : null;
        $this->client_info['remote_host']  = isset($_SERVER['REMOTE_HOST'])          ? $_SERVER['REMOTE_HOST']                   : null;
        $this->client_info['user_agent']   = isset($_SERVER['HTTP_USER_AGENT'])      ? $_SERVER['HTTP_USER_AGENT']               : null;

        $html = array();
        $html[] = "<ul style=\"list-style-type:none; padding-left:4px;\">";
        foreach ($this->client_info as $_val) {
            if (null===$_val) {
                continue;
            }
            $html[] = "<li>".$_val."<input type=\"hidden\" name=\"data[".$this->getColumnId()."][]\" value=\"".$_val."\"></li>";
        }
        $html[] = "</ul>";

        return implode("\n", $html);
    }

    /**
    * 確認画面で呼び出す
    */
    public function getView()
    {
        return nl2br($this->getContent());
    }

    /**
    * SOYMailへの連携先一覧を返す
    */
    public function getLinkagesSOYMailTo()
    {
        return array(
        SOYMailConverter::SOYMAIL_NONE    => "連携しない",
        SOYMailConverter::SOYMAIL_MEMO    => "備考"
        );
    }

    /**
    * データ投入用
    */
    public function getContent()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return "";
        }

        $value = implode("\n", $value);
        return soy2_h($value);
    }
}
