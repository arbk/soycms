<?php

class FailedStage extends StageBase
{
    public function getStageTitle()
    {
        return "失敗";
    }

    public function getNextString()
    {
        return "";
    }

    public function getBackString()
    {
        return "";
    }
}
