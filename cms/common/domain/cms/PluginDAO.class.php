<?php
/**
 * @entity cms.Plugin
 *
 */
class PluginDAO
{
    public function get()
    {
        $pluginsArray = CMSPlugin::getPluginMenu();
        $plugins = array();
        foreach ($pluginsArray as $key => $array) {
            $plugins[$key] = $this->getObject($key, $array);
        }

        return $plugins;
    }

    private static function getPluginDBName()
    {
        return SOY2::RootDir()."db/plugin.db";
    }
    public function getCategoryArray()
    {
        $content = @file_get_contents(self::getPluginDBName());
        if (strlen($content) == 0) {
            $content = array();
        } else {
            $content = unserialize($content);
            if (!is_array($content)) {
                $content = array();
            }
        }

        return $content;
    }

    public function saveCategoryArray($array)
    {
        $content = serialize($array);
        $ret = file_put_contents(self::getPluginDBName(), $content);
        if ($ret) {
            @chmod(self::getPluginDBName(), F_MODE_FILE);
        }
        return $ret;
    }

    public function update(Plugin $plugin)
    {
        $content = $this->getCategoryArray();

        foreach ($content as $key => $ids) {
            foreach ($ids as $index => $id) {
                if ($id == $plugin->getId()) {
                    unset($content[$key][$index]);
                }
            }
        }

        if (isset($content[$plugin->getCategory()])) {
            $content[$plugin->getCategory()][] = $plugin->getId();
        }

        $this->saveCategoryArray($content);

        return ;
    }

    public function addPluginCategory($label)
    {
        $category = $this->getCategoryArray();
        if (isset($category[$label])) {
            return false;
        } else {
            $category[$label] = array();
            $this->saveCategoryArray($category);
            return true;
        }
    }

    public function deletePluginCategory($label)
    {
        $category = $this->getCategoryArray();
        if (isset($category[$label])) {
            unset($category[$label]);
            $this->saveCategoryArray($category);
            return true;
        }
    }

    public function modifyPluginCategory($old, $new)
    {
        $category = $this->getCategoryArray();
        if (!isset($category[$old])) {
            return false;
        } else {
            $tmp = $category[$old];
            unset($category[$old]);
            $category[$new] = $tmp;
            return true;
        }
    }

    public function getActives()
    {
        $plugins = $this->get();
        $result = array();

        foreach ($plugins as $key => $plugin) {
            if ($plugin->isActive()) {
                $result[$plugin->getId()] = $plugin;
            }
        }
        return $result;
    }

    public function getNonActives()
    {
        $plugins = $this->get();
        $result = array();

        foreach ($plugins as $key => $plugin) {
            if (!$plugin->isActive()) {
                $result[$plugin->getId()] = $plugin;
            }
        }
        return $result;
    }

    public function getCategorizedPlugins()
    {
        $plugins = $this->get();
        $categories = $this->getCategoryArray();

        $non_categorized = array();
        $result = array();

        foreach ($categories as $category => $plugin_ids) {
            $result[$category] = array();

            foreach ($plugin_ids as $id) {
                if (isset($plugins[$id])) {
                    if ($plugins[$id]->isActive()) {
                        $result[$category][$id] = $plugins[$id];
                    }
                    unset($plugins[$id]);
                }
            }
        }

        foreach ($plugins as $key => $plugin) {
            if (!$plugin->isActive()) {
                unset($plugins[$key]);
            }
        }
        if (!empty($plugins)) {
            $result[CMSMessageManager::get("SOYCMS_NO_CATEGORY")] = $plugins;
        }
        return $result;
    }

    private function getObject($id, $array)
    {
        $plugin = new Plugin();
        @$plugin->setId($id);
        @$plugin->setAuthor($array["author"]);
        @$plugin->setName($array["name"]);
        @$plugin->setDescription($array["description"]);
        @$plugin->setUrl($array["url"]);
        @$plugin->setMail($array["mail"]);
        @$plugin->setVersion($array["version"]);
        @$plugin->setConfig($array["config"]);
        @$plugin->setCustom($array["custom"]);
        @$plugin->setIcon($array["icon"]);
        @$plugin->setIsActive((file_exists(CMSPlugin::getSiteDirectory().'/.plugin/'. $id .".active")? 1 :0));
        return $plugin;
    }

    public function getById($id)
    {
        $pluginArray = CMSPlugin::getPluginMenu($id);

        if (!$pluginArray) {
            return;
        }

        return $this->getObject($id, $pluginArray);
    }

    public function toggleActive($id)
    {
        $plugin = $this->getById($id);

        if (!$plugin) {
            return null;
        }

        if ($plugin->isActive()) {
            unlink(CMSPlugin::getSiteDirectory().'/.plugin/'. $plugin->getId() .".active");
        } else {
            file_put_contents(CMSPlugin::getSiteDirectory().'/.plugin/'.$plugin->getId().".active", "active");
            @chmod(CMSPlugin::getSiteDirectory().'/.plugin/'.$plugin->getId().".active", F_MODE_FILE);
        }

        //プラグインのonDisable onActive関数の実行
        if ($plugin->isActive()) {
            CMSPlugin::callLocalPluginEventFunc('onDisable', $id);
        } else {
            CMSPlugin::callLocalPluginEventFunc('onActive', $id);
        }
        if (isset($event[$id])) {
            call_user_func($event[$id][0]);
        }

        //新しく切り替えたものを返す
        return !$plugin->isActive();
    }
}
