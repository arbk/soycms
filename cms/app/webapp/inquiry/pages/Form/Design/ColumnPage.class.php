<?php

class ColumnPage extends WebPage
{
    public $id;
    public $dao;
    public $errorMessage;
    public $formDao;

    public function doPost()
    {
    }

    public function prepare()
    {
        $this->dao = SOY2DAOFactory::create("SOYInquiry_ColumnDAO");
        $this->formDao = SOY2DAOFactory::create("SOYInquiry_FormDAO");

        parent::prepare();
    }

    public function __construct($args)
    {
        if (count($args) < 1) {
            CMSApplication::jump("Form");
        }
        $this->id = (int)$args[0];

        //レイヤーモードで
        CMSApplication::setMode("layer");

        parent::__construct();

        $this->createAdd("column_list", "_common.Form.Design.ColumnListComponent", array(
            "list" => $this->getColumns($this->id),
            "isLinkageSOYMail" => true,
//          "isLinkageSOYShop" => $this->checkSOYShopConnect($this->id),
            "formDesign" => $this->getFormDesignById($this->id) //何のテンプレートを使用しているか？
        ));
    }

    private function getColumns($formId)
    {
        try {
            return $this->dao->getOrderedColumnsByFormId($formId);
        } catch (Exception $e) {
            return array();
        }
    }

    // private function checkSOYShopConnect($formId)
    // {
    //     $connectConfig = $this->getFormConfigById($formId)->getConnect();
    //     return ($connectConfig["siteId"] > 0);
    // }

    private function getFormDesignById($formId)
    {
        $designConfig = $this->getFormConfigById($formId)->getDesign();
        return (isset($designConfig["theme"])) ? $designConfig["theme"] : "default";
    }

    private function getFormConfigById($formId)
    {
        static $config;
        if (null===$config) {
            try {
                $config = $this->formDao->getById($formId)->getConfigObject();
            } catch (Exception $e) {
                $config = array();
            }
        }
        return $config;
    }
}
