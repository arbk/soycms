<?php

class CategoryApplyAction extends SOY2Action
{
    public function execute($request, $form, $response)
    {
//    未分類というカテゴリは作れないのでそのまま渡すと、未分類に勝手になる
//      if($form->category == "未分類"){
//        return SOY2Action::FAILED;
//      }

        $dao = SOY2DAOFactory::create("cms.PluginDAO");

        $plugin = $dao->getById($form->plugin_id);

        if (!$plugin) {
            return SOY2Action::FAILED;
        } else {
            $plugin->setCategory($form->category);
            $dao->update($plugin);
            return SOY2Action::SUCCESS;
        }
    }
}

class CategoryApplyActionForm extends SOY2ActionForm
{
    public $category;
    public $plugin_id;

    public function getCategory()
    {
        return $this->category;
    }
    public function setCategory($category)
    {
        $this->category = $category;
    }
    public function getPlugin_id()
    {
        return $this->plugin_id;
    }
    public function setPlugin_id($plugin_id)
    {
        $this->plugin_id = $plugin_id;
    }
}
