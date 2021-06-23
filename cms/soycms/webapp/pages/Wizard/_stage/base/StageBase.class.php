<?php

class StageBase extends CMSWebPageBase
{
    public $wizardObj;

    public function __construct()
    {
        parent::__construct();
    }

    //表示部分はここに書く
    public function execute()
    {
    }

    //次へが押された際の動作
    public function checkNext()
    {
        return true;
    }

    //前へが押された際の動作
    public function checkBack()
    {
        return true;
    }

    //次のオブジェクト名、終了の際はEndStageを呼び出す
    public function getNextObject()
    {
        return "EndStage";
    }

    //前のオブジェクト名、nullの場合は表示しない
    public function getBackObject()
    {
        return null;
    }

    public function getWizardObj()
    {
        return $this->wizardObj;
    }
    public function setWizardObj($wizardObj)
    {
        $this->wizardObj = $wizardObj;
    }

    public function getNextString()
    {
        return "次へ";
    }

    public function getBackString()
    {
        return "前へ";
    }
}
