<?php

class CMSPageModulePlugin extends PluginBase
{
    const ERR_MARK_FUNC_NOT_FOUND = "_FNF_";

    protected $_soy2_prefix = "cms";

    private $_immediate = false;

    public function getImmediate()
    {
        return $this->_immediate;
    }
    public function setImmediate($immediate)
    {
        $this->_immediate = $immediate;
    }

    public function execute()
    {
        $soyValue = $this->soyValue;

        $array = explode(".", $soyValue);
        if (count($array) > 1) {
            unset($array[0]);
        }
        $func = "scms_mdl_" . implode("_", $array);

  //  //ダイナミック編集のためにここで定義を確認しておく
  //  if(!defined("_SITE_ROOT_")) define("_SITE_ROOT_", UserInfoUtil::getSiteDirectory());
        $moduleFile = str_replace(".", "/", $soyValue) . ".php";
        $modulePath = soy2_realpath(_SITE_ROOT_) . ".module/" . $moduleFile; // サイトモジュール
        $modulePathCom = soy2_realpath(_CMS_COMMON_DIR_) . "site_include/module/" . $moduleFile; // 共通モジュール

        if ($this->_immediate) {
            if (file_exists($modulePath)) {
                include_once($modulePath);
            } else {
                include_once($modulePathCom);
            }
            $cnt = self::ERR_MARK_FUNC_NOT_FOUND;
            if (function_exists($func)) {
                ob_start();
                call_user_func($func, $this->getInnerHTML(), $this->parent);
                $cnt = ob_get_contents();
                ob_end_clean();
                $cnt = CMSPage::deleteComment($cnt);  // cms:ignore
                $cnt = CMSPage::checkAndEscapePhpTag($cnt); // <?php
            }
            $this->setInnerHTML($cnt);
        } else {
            $this->setInnerHTML(
                '<?php if(file_exists("' . $modulePath . '")){include_once("' . $modulePath . '");}else{include_once("' . $modulePathCom . '");} if(function_exists("' . $func . '")){ob_start(); ?>' .
                $this->getInnerHTML() .
                '<?php $tmp_html=ob_get_contents();ob_end_clean(); echo call_user_func("' . $func . '",$tmp_html,$this);}else{ echo "' . self::ERR_MARK_FUNC_NOT_FOUND . '";} ?>'
            );
        }
    }
}
