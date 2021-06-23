<?php

class PluginListAction extends SOY2Action
{
    private $state;

    public function setState($state)
    {
        $this->state = $state;
    }

    public function execute()
    {
        $dao = SOY2DAOFactory::create("cms.PluginDAO");
        try {
            if (null===$this->state) {
                $plugins = $dao->get();
            } elseif ($this->state) {
                $plugins = $dao->getCategorizedPlugins();
            } else {
                $plugins = $dao->getNonActives();
            }
            $this->setAttribute("plugins", $plugins);
            return SOY2Action::SUCCESS;
        } catch (Exception $e) {
            return SOY2Action::FAILED;
        }
    }
}
