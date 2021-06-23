<?php

SOY2::import("domain.SOYMailConverter");
//SOY2::import("domain.SOYShopConnector");

/**
 * @table soyinquiry_column
 */
class SOYInquiry_Column
{
    /**
     * @id
     */
    private $id;

    /**
     * @column form_id
     */
    private $formId;

    /**
     * @column column_id
     */
    private $columnId;

    private $label;

    /**
     * @column column_type
     */
    private $type;

    private $config;

    /**
     * @column display_order
     */
    private $order = 1;

    /**
     * @column is_require
     */
    private $require = 0;

    /**
     * @no_persistent
     */
    private $value;

    /**
     * @no_persistent
     */
    public static $columnTypes = array(
    "SingleText" => "1行テキスト",
    "MultiText" => "複数行テキスト",
    "Radio" => "ラジオボタン",
    "CheckBox" => "チェックボックス",
    "SelectBox" => "セレクトボックス",
    "Date" => "日付",
    "DateWithoutDay" => "日付(日なし)",
    "Prefecture" => "都道府県",
    "Address" => "住所",
    "AddressJs" => "住所(JS版)",
    "File" => "アップロード",
    "Files" => "アップロード(複数)",
    "Telephone" => "電話番号",
    "MultiSingleText" => "分割1行テキスト",
    "NameText" => "[名前]",
    "MailAddress" => "[メールアドレス]",
    "ConfirmMailAddress" => "[メールアドレス(確認用フォーム有り)]",
    "PrivacyPolicy" => "[個人情報保護方針]",
    "Question" => "[質問]",
    "PlainText" => "[見出し表示]",
    "SOYCMSBlogEntryPage" => "カスタムフィールド [SOY CMSブログ詳細ページ]",
    "SOYCMSBlogEntry" => "記事名 [SOY CMSブログ連携]",
    //"SOYShop" => "商品名 [SOY Shop連携]",
    "Enquete" => "アンケート項目",
    "EnqueteFree" => "アンケート自由記述",
    "SerialNumber" => "連番",
    "ClientInfo" => "送信元情報",
    //"CustomfieldAdvanced" => "カスタムフィールドアドバンスド連携"
    );

    /**
     * @no_persistent
     */
    private $inquiry;

    /**
     * #helper function
     */
    private static function _types()
    {
        static $types;
        if (null===$types) {
            $types = SOYInquiry_Column::$columnTypes;

            //拡張 /common/inquiry.config.phpがあれば読み込む
            if (file_exists(CMS_COMMON . "/config/inquiry.config.php")) {
                require_once(CMS_COMMON . "/config/inquiry.config.php");
                $types = array_merge($types, $advancedColumns);
            }
        }
        return $types;
    }

    /**
     * #factory
     */
    public function getColumn(SOYInquiry_Form $form = null)
    {
        if ($this->type) {
            SOY2::import("columns." . $this->type . "Column");
        }
        $className = $this->type . "Column";

        if (!class_exists($className)) {
            $column = new SOYInquiry_ColumnBase();
        } else {
            $column = new $className();
            $column->setConfigure($this->config);
        }

        $column->setId($this->id);
        $column->setFormId($this->formId);
        if (strlen($this->columnId)>0) {
            $column->setColumnId($this->columnId);
        } else {
            $column->setColumnId($this->id);
        }
        if ($form) {
            $column->setFormObject($form);
        }
        $column->setInquiry($this->getInquiry());
        $column->setLabel($this->label);
        $column->setValue($this->value);
        $column->setIsRequire($this->require);
        return $column;
    }
    public function setColumn($columnObject)
    {
        $configure = $columnObject->getConfigure();
        $this->config = $configure;
    }

    /* 以下 getter setter */

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
    public function getLabel()
    {
        return $this->label;
    }
    public function setLabel($label)
    {
        $this->label = $label;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function getConfig()
    {
        return serialize($this->config);
    }
    public function setConfig($config)
    {
        if (is_string($config)) {
            $this->config = unserialize($config);
        } else {
            $this->config = $config;
        }
    }
    public function getOrder()
    {
        return $this->order;
    }
    public function setOrder($order)
    {
        $this->order = $order;
    }
    public function getRequire()
    {
        return (int)$this->require;
    }
    public function setRequire($require)
    {
        $this->require = $require;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }

  /**
   * 保存用
   */
    public function getContent()
    {
        $obj = $this->getColumn();
        return $obj->getContent();
    }

    public static function getTypes()
    {
        return self::_types();
    }

    public function getTypeText()
    {
        $types = self::_types();
        return (isset($types[$this->type])) ? $types[$this->type] : "無効な種別(".$this->type.")";
    }

    public function getInquiry()
    {
        return $this->inquiry;
    }
    public function setInquiry($inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function getColumnId()
    {
        return $this->columnId;
    }
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId;
    }

    public function getNoPersistent()
    {
        return $this->getColumn()->getNoPersistent();
    }
}

interface ISOYInquiry_Column
{
    /**
     * ユーザに表示するようのフォーム
     */
    public function getForm($attribute = array());

    /**
     * 確認画面で呼び出す
     */
    public function getView();

    /**
     * 設定画面で表示する用のフォーム
     */
    public function getConfigForm();

    /**
     * データ投入用
     */
    public function getContent();

    /**
     * Inquiry#append時に呼び出し
     */
    public function onAppend();

    /**
     * 値が正常かどうかチェック
     * @return boolean
     */
    public function validate();

    /**
     * エラーメッセージを取得
     */
    public function getErrorMessage();

    /**
     * 保存された設定値を渡す
     */
    public function setConfigure($config);

    /**
     * 保存に必要な設定を取得する
     */
    public function getConfigure();
}

class SOYInquiry_ColumnBase implements ISOYInquiry_Column
{
    protected $id;
    protected $formId;
    protected $columnId;
    protected $label;
    protected $value;
    protected $isRequire;
    protected $errorMessage = "";
    protected $formObject = null;
    protected $inquiry;
    protected $SOYMailTo = SOYMailConverter::SOYMAIL_NONE;
//  protected $SOYShopFrom = SOYShopConnector::SOYSHOP_NONE;
    protected $replacement;
    protected $annotation;
    protected $trProperty;
    protected $noPersistent = false;

    /**
     * ユーザに表示するようのフォーム
     */
    public function getForm($attributes = array())
    {
    }

    /**
     * 確認画面で呼び出す
     */
    public function getView()
    {
        return soy2_h((string)$this->getValue());
    }

    /**
     * 設定画面で表示する用のフォーム
     */
    public function getConfigForm()
    {
    }

    /**
     * SOYMailへの連携先一覧を返す
     */
    public function getLinkagesSOYMailTo()
    {
        return array(
        SOYMailConverter::SOYMAIL_NONE => "連携しない"
        );
    }

    // /**
    //  * SOY Shopとの連携項目一覧を返す
    //  */
    // public function getLinkagesSOYShopFrom()
    // {
    //     return array(
    //     SOYShopConnector::SOYSHOP_NONE => "連携しない"
    //     );
    // }

    /**
     * SOYMailに情報を登録するときのConverterを返す
     */
    public function factoryConverter()
    {
        return new SOYMailConverter();
    }

    // public function factoryConnector()
    // {
    //     return new SOYShopConnector();
    // }

    /**
     * SOYMail連携用のデータ(convert後のデータを取得)
     *
     * @return array
     */
    public function convertToSOYMail()
    {
        $converter = $this->factoryConverter();

        $value = $this->getValue();
        $soyMailTo = $this->getSOYMailTo();

        //確認用メールアドレス対策、カラムファイルでgetValueを持ちたかったが、validateが動かなくなるのでこちらで対応
        if ($soyMailTo === "mail_address" && is_array($value)) {
            $value = $value[0];
        }

        return $converter->convert($value, $soyMailTo);
    }

    // /**
    //  * SOYMail連携用のデータ(convert後のデータを取得)
    //  *
    //  * @return array
    //  */
    // public function convertToSOYShop()
    // {
    //     $converter = $this->factoryConverter(); //ConverterはSOY Mailを流用

    //     $value = $this->getValue();
    //     $soyShopTo = $this->getSOYShopFrom();   //SOY Shopの場合はToとFromを一緒にする

    //     //確認用メールアドレス対策、カラムファイルでgetValueを持ちたかったが、validateが動かなくなるのでこちらで対応
    //     if ($soyShopTo === "mail_address" && is_array($value) === true) {
    //         $value = $value[0];
    //     }

    //     return $converter->convert($value, $soyShopTo);
    // }

    // /**
    //  * @return String
    //  */
    // public function insertFromSOYShop()
    // {
    //     $connector = $this->factoryConnector();
    //     return $connector->insert($this->SOYShopFrom);
    // }

    /**
     * データ投入用
     *
     * 標準はgetView()を呼びだす
     */
    public function getContent()
    {
        return $this->getView();
    }

    /**
     * メールの本文に使用する
     *
     * 標準はgetContent()を呼びだす
     */
    public function getMailText()
    {
        return $this->getContent();
    }

    /**
     * Inquiry#append時に呼び出し
     */
    public function onAppend()
    {
    }

    /**
     * onSend
     */
    public function onSend($obj)
    {
    }

    /**
     * 値が正常かどうかチェック
     */
    public function validate()
    {
        if ($this->getIsRequire() && strlen($this->getValue()) < 1) {
            $this->setErrorMessage($this->getLabel() . "を入力してください。");
            return false;
        }
        return true;
    }

    /**
     * 保存された設定値を渡す
     */
    public function setConfigure($config)
    {
        $this->isLinkageSOYMail = isset($config["isLinkageSOYMail"]) ? $config["isLinkageSOYMail"] : false;
//      $this->isLinkageSOYShop = isset($config["isLinkageSOYShop"]) ? $config["isLinkageSOYShop"] : false;
        $this->SOYMailTo = isset($config["SOYMailTo"]) ? $config["SOYMailTo"] : null;
//      $this->SOYShopFrom = isset($config["SOYShopFrom"]) ? $config["SOYShopFrom"] : null;
        $this->replacement = isset($config["replacement"])? $config["replacement"] : null;
        $this->annotation = isset($config["annotation"])? $config["annotation"] : null;
        $this->trProperty = (isset($config["trProperty"])) ? $config["trProperty"] : null;

        if (!defined("SOYINQUIRY_FORM_DESIGN_PAGE")) {
            define("SOYINQUIRY_FORM_DESIGN_PAGE", (isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PATH_INFO"], "/" . APPLICATION_ID . "/Form/Design/") !== false));
        }
    }

    /**
     * 保存に必要な設定を取得する
     */
    public function getConfigure()
    {
        $config = array(
        "isLinkageSOYMail"=>$this->isLinkageSOYMail,
        "SOYMailTo" => $this->SOYMailTo,
//      "SOYShopFrom" => $this->SOYShopFrom,
//      "SOYShopFrom" => $this->SOYShopFrom,
        "replacement" => $this->replacement,
        "annotation" => $this->annotation,
        "trProperty" => $this->trProperty
        );
        return $config;
    }


    /**
     * エラーメッセージを取得
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getLabel()
    {
        return $this->label;
    }
    public function setLabel($label)
    {
        $this->label = $label;
    }
    public function getValue()
    {
        // //SOYShop連携
        // if (null===$this->value &&
        // (defined("SOYINQUERY_SOYSHOP_CONNECT_SITE_ID") && SOYINQUERY_SOYSHOP_CONNECT_SITE_ID) &&
        // $this->SOYShopFrom != SOYShopConnector::SOYSHOP_NONE &&
        // SOYInquiryUtil::checkSOYShopInstall()
        // ) {
        //     $this->value = $this->insertFromSOYShop();
        // }

        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getIsRequire()
    {
        return $this->isRequire;
    }
    public function isRequire()
    {
        return $this->isRequire;
    }
    public function setIsRequire($isRequire)
    {
        $this->isRequire = $isRequire;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getFormId()
    {
        return $this->formId;
    }
    public function setFormId($formId)
    {
        $this->formId = $formId;
    }

    public function getFormObject()
    {
        return $this->formObject;
    }
    public function setFormObject($formObject)
    {
        $this->formObject = $formObject;
    }

    public function getSOYMailTo()
    {
        return $this->SOYMailTo;
    }

    public function setSOYMailTo($to)
    {
        $this->SOYMailTo = $to;
    }

    // public function getSOYShopFrom()
    // {
    //     return $this->SOYShopFrom;
    // }
    // public function setSOYShopFrom($from)
    // {
    //     $this->SOYShopFrom = $from;
    // }

    public function getInquiry()
    {
        return $this->inquiry;
    }
    public function setInquiry($inquiry)
    {
        $this->inquiry = $inquiry;
    }
    public function getReplacement()
    {
        return $this->replacement;
    }
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
    }

    public function getColumnId()
    {
        return $this->columnId;
    }
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId;
    }

    public function getAnnotation()
    {
        return $this->annotation;
    }

    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    public function getTrProperty()
    {
        return $this->trProperty;
    }
    public function setTrProperty($trProperty)
    {
        $this->trProperty = $trProperty;
    }

    public function getNoPersistent()
    {
        return $this->noPersistent;
    }
    public function setNoPersistent($noPersistent)
    {
        $this->noPersistent = $noPersistent;
    }
}
