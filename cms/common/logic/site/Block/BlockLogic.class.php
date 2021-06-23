<?php

class BlockLogic extends SOY2LogicBase
{
    /**
     * PageIdよりTemplateのsoy:idをすべて取得し、返す
     */
    public function getSoyIds($id)
    {
        $dao = SOY2DAOFactory::create("cms.PageDAO");
        $page = $dao->getById($id);
        $template = $page->getTemplate();

        $regex = '/<[^>]*\sblock:id=\"([a-zA-Z][a-zA-Z0-9_]+)\"\s?[^>]*>/i';
        $ids = array();
        $match = array();
        $offset = 0;
        while (preg_match($regex, $template, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $offset = $match[1][1];
            $ids[$match[1][0]] = $match[1][0];
        }
        return $ids;
    }

    /**
     * PageIdに割り当てられているBlockをすべて返す
     */
    public function getByPageId($id)
    {
        $dao = SOY2DAOFactory::create("cms.BlockDAO");
        return $dao->getByPageId($id);
    }

    public function create(Block $block)
    {
        return $this->getDAO()->insert($block);
    }

    public function getDAO()
    {
        return SOY2DAOFactory::create("cms.BlockDAO");
    }

    public function getById($id)
    {
        return $this->getDAO()->getById($id);
    }

    public function delete($id)
    {
        return $this->getDAO()->delete($id);
    }
}
