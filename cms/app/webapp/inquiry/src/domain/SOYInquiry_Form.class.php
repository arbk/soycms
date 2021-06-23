<?php
/**
 * @table soyinquiry_form
 */
class SOYInquiry_Form
{
    /**
     * @id
     */
    private $id;

    /**
     * @column form_id
     */
    private $formId;

    private $name;

    private $config;

    /**
     * @no_persistent
     */
    private $configObject;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getFormId()
    {
        return $this->formId;
    }
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function getConfig()
    {
        return $this->config;
    }
    public function setConfig($config)
    {
        $this->config = $config;
    }
    public function getConfigObject()
    {
        if (!$this->configObject instanceof SOYInquiry_FormConfig) {
            if (strlen($this->config)) {
                $this->configObject = unserialize($this->config);
            } else {
                $this->configObject = new SOYInquiry_FormConfig();
            }
        }

        return $this->configObject;
    }
    public function setConfigObject($obj)
    {
        $this->configObject = $obj;
        $this->config = serialize($obj);
    }

    /**
     * Designに設定出来る値を表示する
     */
    public function getDesignList()
    {
        $dir = SOY2::RootDir() . "template/";
        $files = scandir($dir);

        $list = array();
        foreach ($files as $file) {
            if ($file[0] == "." || strpos($file[0], "_") === 0) {
                continue;
            }
            $list[] = $file;
        }

        return $list;
    }
}


/**
 * 設定オブジェクト
 */
class SOYInquiry_FormConfig
{

    private $isSendNotifyMail = true;
    private $isSendConfirmMail = true;
    private $isIncludeAdminURL = true;
    private $isUseCaptcha = false;
    private $isSmartPhone = false;
    private $isReplyToUser = false;

    //お問い合わせ詳細からの返答設定
    private $isCcOnReplyForm = false;

    private $administratorMailAddress = "";
    private $notifyMailSubject = "[SOYInquiry]問い合わせがあります";

    private $fromAddress = "";
    private $returnAddress = "";

    private $fromAddressName = "";
    private $returnAddressName = "";

    private $message = array(
    "information" => "下記の項目を入力してください。",
    "confirm" => "送信内容を確認して下さい。",
    "complete" => "送信いたしました。",
    "require_error" => "この項目は必須です。",
    );

    private $confirmMail = array(
    "title" => "お問い合わせありがとうございます。",
    "header" => "#NAME#様\r\n\r\n今回は○○にお問い合わせありがとうござます。\r\n近日中に返答いたします。\r\n",
    "isOutputContent" => false,
    "footer" => "\r\n\r\n株式会社○○\r\nTEL:XXX-XXX-XXX\r\n住所:東京都千代田区",
    "replaceTrackingNumber" => "#TRACKNUM#"
    );

    private $design = array(
    "theme" => "",
    "isOutputStylesheet" => true
    );

    //SOY Shop連携用
    private $connect = array(
    "siteId" => 0
    );

    public function getIsSendNotifyMail()
    {
        return $this->isSendNotifyMail;
    }
    public function setIsSendNotifyMail($isSendNotifyMail)
    {
        $this->isSendNotifyMail = $isSendNotifyMail;
    }
    public function getIsSendConfirmMail()
    {
        return $this->isSendConfirmMail;
    }
    public function setIsSendConfirmMail($isSendConfirmMail)
    {
        $this->isSendConfirmMail = $isSendConfirmMail;
    }
    public function getIsIncludeAdminURL()
    {
        return $this->isIncludeAdminURL;
    }
    public function setIsIncludeAdminURL($isIncludeAdminURL)
    {
        $this->isIncludeAdminURL = $isIncludeAdminURL;
    }
    public function getAdministratorMailAddress()
    {
        return $this->administratorMailAddress;
    }
    public function setAdministratorMailAddress($administratorMailAddress)
    {
        $this->administratorMailAddress = $administratorMailAddress;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function setMessage($message)
    {
        $this->message = $message;
    }
    public function getConfirmMail()
    {
        return $this->confirmMail;
    }
    public function setConfirmMail($confirmMail)
    {
        $this->confirmMail = $confirmMail;
    }
    public function getDesign()
    {
        return $this->design;
    }
    public function setDesign($design)
    {
        $this->design = $design;
    }
    public function getConnect()
    {
        return $this->connect;
    }
    public function setConnect($connect)
    {
        $this->connect = $connect;
    }

    public function getIsUseCaptcha()
    {
        if (!$this->enabledGD()) {
            $this->isUseCaptcha = false;
        }
        return $this->isUseCaptcha;
    }

    public function setIsUseCaptcha($isUseCaptcha)
    {
        $this->isUseCaptcha = $isUseCaptcha;
    }

    public function getIsSmartPhone()
    {
        return $this->isSmartPhone;
    }

    public function setIsSmartPhone($isSmartPhone)
    {
        $this->isSmartPhone = $isSmartPhone;
    }

    public function getNotifyMailSubject()
    {
        return $this->notifyMailSubject;
    }
    public function setNotifyMailSubject($notifyMailSubject)
    {
        $this->notifyMailSubject = $notifyMailSubject;
    }

    /**
     * theme
     */
    public function getTheme()
    {
        return (@$this->design["theme"]) ? @$this->design["theme"] : "default";
    }

    /**
     * styleを出力するかどうか
     */
    public function isOutputDesign()
    {
        return (boolean)$this->design["isOutputStylesheet"];
    }

    /**
     * GDが使えるかどうかチェック
     *
     * @return boolean
     */
    public function enabledGD()
    {
        if (function_exists("imagejpeg") && function_exists("imagettftext") && function_exists("imagettfbbox")) {
            return true;
        }

        return false;
    }

    public function getFromAddress()
    {
        return $this->fromAddress;
    }
    public function setFromAddress($fromAddress)
    {
        $this->fromAddress = $fromAddress;
    }
    public function getReturnAddress()
    {
        return $this->returnAddress;
    }
    public function setReturnAddress($returnAddress)
    {
        $this->returnAddress = $returnAddress;
    }

    public function getFromAddressName()
    {
        return $this->fromAddressName;
    }
    public function setFromAddressName($fromAddressName)
    {
        $this->fromAddressName = $fromAddressName;
    }
    public function getReturnAddressName()
    {
        return $this->returnAddressName;
    }
    public function setReturnAddressName($returnAddressName)
    {
        $this->returnAddressName = $returnAddressName;
    }

    public function getIsReplyToUser()
    {
        return $this->isReplyToUser;
    }
    public function setIsReplyToUser($isReplyToUser)
    {
        $this->isReplyToUser = $isReplyToUser;
    }

    public function getIsCcOnReplyForm()
    {
        return $this->isCcOnReplyForm;
    }
    public function setIsCcOnReplyForm($isCcOnReplyForm)
    {
        $this->isCcOnReplyForm = $isCcOnReplyForm;
    }
}
