<?php
class StartStage extends StageBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
    }

    public function checkNext()
    {
        return true;
    }

    public function checkBack()
    {
        return true;
    }

    public function getNextObject()
    {
        return "SelectTopStage";
    }

    public function getBackObject()
    {
        return null;
    }

    public function getBackString()
    {
        return "";
    }
}
