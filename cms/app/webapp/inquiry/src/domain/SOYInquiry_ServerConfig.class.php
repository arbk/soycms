<?php

class SOYInquiry_ServerConfig
{
    const SERVER_TYPE_SMTP = 0;
    const SERVER_TYPE_SENDMAIL = 2;

    const RECEIVE_SERVER_TYPE_POP  = 0;
    const RECEIVE_SERVER_TYPE_IMAP = 1;

    //送信設定
    private $sendServerType = SOYInquiry_ServerConfig::SERVER_TYPE_SENDMAIL;
    private $isUseSMTPAuth = true;
    private $isUsePopBeforeSMTP = false;
    private $sendServerAddress = "localhost";
    private $sendServerPort = 25;
    private $sendServerUser = "";
    private $sendServerPassword = "";
    private $isUseSSLSendServer = false;

    //受信設定
    private $receiveServerType = SOYInquiry_ServerConfig::RECEIVE_SERVER_TYPE_POP;
    private $receiveServerAddress = "localhost";
    private $receiveServerPort = 110;
    private $receiveServerUser = "";
    private $receiveServerPassword = "";
    private $isUseSSLReceiveServer = false;

    //管理者設定
    private $administratorName = "";
    private $administratorMailAddress = "";
    private $returnMailAddress =  "";
    private $returnName = "";

    private $encoding = "ISO-2022-JP";

    private $signature = "";

    //ファイル設定
    private $uploadDir;

    private $adminUrl;

    public function getSendServerType()
    {
        return $this->sendServerType;
    }
    public function setSendServerType($sendServerType)
    {
        $this->sendServerType = $sendServerType;
    }
    public function getIsUseSMTPAuth()
    {
        return $this->isUseSMTPAuth;
    }
    public function setIsUseSMTPAuth($isUseSMTPAuth)
    {
        $this->isUseSMTPAuth = $isUseSMTPAuth;
    }
    public function getSendServerAddress()
    {
        return $this->sendServerAddress;
    }
    public function setSendServerAddress($sendServerAddress)
    {
        $this->sendServerAddress = $sendServerAddress;
    }
    public function getSendServerPort()
    {
        return $this->sendServerPort;
    }
    public function setSendServerPort($sendServerPort)
    {
        $this->sendServerPort = $sendServerPort;
    }
    public function getSendServerUser()
    {
        return $this->sendServerUser;
    }
    public function setSendServerUser($sendServerUser)
    {
        $this->sendServerUser = $sendServerUser;
    }
    public function getSendServerPassword()
    {
        return $this->sendServerPassword;
    }
    public function setSendServerPassword($sendServerPassword)
    {
        $this->sendServerPassword = $sendServerPassword;
    }
    public function getIsUseSSLSendServer()
    {
        return $this->isUseSSLSendServer;
    }
    public function setIsUseSSLSendServer($isUseSSLSendServer)
    {
        $this->isUseSSLSendServer = $isUseSSLSendServer;
    }
    public function getReceiveServerType()
    {
        return $this->receiveServerType;
    }
    public function setReceiveServerType($recieveServerType)
    {
        $this->receiveServerType = $recieveServerType;
    }
    public function getReceiveServerAddress()
    {
        return $this->receiveServerAddress;
    }
    public function setReceiveServerAddress($receiveServerAddress)
    {
        $this->receiveServerAddress = $receiveServerAddress;
    }
    public function getReceiveServerPort()
    {
        return $this->receiveServerPort;
    }
    public function setReceiveServerPort($receiveServerPort)
    {
        $this->receiveServerPort = $receiveServerPort;
    }
    public function getReceiveServerUser()
    {
        return $this->receiveServerUser;
    }
    public function setReceiveServerUser($receiveServerUser)
    {
        $this->receiveServerUser = $receiveServerUser;
    }
    public function getReceiveServerPassword()
    {
        return $this->receiveServerPassword;
    }
    public function setReceiveServerPassword($receiveServerPassword)
    {
        $this->receiveServerPassword = $receiveServerPassword;
    }
    public function getIsUsePopBeforeSMTP()
    {
        return $this->isUsePopBeforeSMTP;
    }
    public function setIsUsePopBeforeSMTP($isUsePopBeforeSMTP)
    {
        $this->isUsePopBeforeSMTP = $isUsePopBeforeSMTP;
    }
    public function getIsUseSSLReceiveServer()
    {
        return $this->isUseSSLReceiveServer;
    }
    public function setIsUseSSLReceiveServer($isUseSSLReceiveServer)
    {
        $this->isUseSSLReceiveServer = $isUseSSLReceiveServer;
    }
    public function getAdministratorName()
    {
        return $this->administratorName;
    }
    public function setAdministratorName($administratorName)
    {
        $this->administratorName = $administratorName;
    }
    public function getAdministratorMailAddress()
    {
        return $this->administratorMailAddress;
    }
    public function setAdministratorMailAddress($administratorMailAddress)
    {
        $this->administratorMailAddress = $administratorMailAddress;
    }
    public function getReturnMailAddress()
    {
        return $this->returnMailAddress;
    }
    public function setReturnMailAddress($returnMailAddress)
    {
        $this->returnMailAddress = $returnMailAddress;
    }
    public function getReturnName()
    {
        return $this->returnName;
    }
    public function setReturnName($returnName)
    {
        $this->returnName = $returnName;
    }
    public function getSignature()
    {
        return $this->signature;
    }
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }
    public function getEncoding()
    {
        return $this->encoding;
    }
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * 設定からSOY2Mailオブジェクトを生成する
     */
    public function createReceiveServerObject()
    {

        switch ($this->receiveServerType) {
            case SOYInquiry_ServerConfig::RECEIVE_SERVER_TYPE_IMAP:
                $flag = null;
                if ($this->getIsUseSSLReceiveServer()) {
                    $flag = "ssl";
                }

                return SOY2Mail::create("imap", array(
                    "imap.host" => $this->getReceiveServerAddress(),
                    "imap.port" => $this->getReceiveServerPort(),
                    "imap.user" => $this->getReceiveServerUser(),
                    "imap.pass" => $this->getReceiveServerPassword(),
                    "imap.flag" => $flag
                ));
                break;

            case SOYInquiry_ServerConfig::RECEIVE_SERVER_TYPE_POP:
            default:
                $host = $this->getReceiveServerAddress();
                if ($this->getIsUseSSLReceiveServer()) {
                    $host =  "ssl://" . $host;
                }

                return SOY2Mail::create("pop", array(
                    "pop.host" => $host,
                    "pop.port" => $this->getReceiveServerPort(),
                    "pop.user" => $this->getReceiveServerUser(),
                    "pop.pass" => $this->getReceiveServerPassword()
                ));
                break;
        }
    }

    /**
     * 設定からSOY2Mailオブジェクトを生成する
     */
    public function createSendServerObject()
    {

        switch ($this->sendServerType) {
            case SOYInquiry_ServerConfig::SERVER_TYPE_SMTP:
                $host = $this->getSendServerAddress();
                if ($this->getIsUseSSLSendServer()) {
                    $host =  "ssl://" . $host;
                }

                return SOY2Mail::create("smtp", array(
                    "smtp.host" => $host,
                    "smtp.port" => $this->getSendServerPort(),
                    "smtp.user" => $this->getSendServerUser(),
                    "smtp.pass" => $this->getSendServerPassword(),
                    "smtp.auth" => ($this->getIsUseSMTPAuth()) ? true : false
                ));
                break;
            case SOYInquiry_ServerConfig::SERVER_TYPE_SENDMAIL:
            default:
                return SOY2Mail::create("sendmail", array());
                break;
        }
    }

    public function getUploadDir()
    {

        if (strlen($this->uploadDir)<1) {
            $this->uploadDir = "/";
        }

        return $this->uploadDir;
    }
    public function setUploadDir($uploadDir)
    {
        if (strlen($uploadDir)>0) {
            //ルートと結合 ルートの末尾には/なし
            if ($uploadDir[0] != "/") {
                $uploadDir = "/" . $uploadDir;
            }
            $uploadDir = SOY_INQUIRY_UPLOAD_ROOT_DIR . $uploadDir;

            //相対パスを解釈:存在なければ×
            $uploadDir = realpath($uploadDir);
            $uploadDir = str_replace("\\", "/", $uploadDir);

            //末尾に/
            if (strlen($uploadDir)>1 && @$uploadDir[strlen($uploadDir)-1] != "/") {
                $uploadDir .= "/";
            }

            //ルートを削除：ルートより上位ディレクトリには保存できない
            $uploadDir = str_replace(SOY_INQUIRY_UPLOAD_ROOT_DIR, "", $uploadDir);
        }

        $this->uploadDir = $uploadDir;
    }

    public function getAdminUrl()
    {
        if (strlen($this->adminUrl)<1) {
            $this->adminUrl = SOY2PageController::createLink("", true);
        }
        return $this->adminUrl;
    }
    public function setAdminUrl($adminUrl)
    {
        $this->adminUrl = $adminUrl;
    }
}
