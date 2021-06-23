<?php

class CopyPage extends CMSWebPageBase
{
    public function __construct($args)
    {
        if (soy2_check_token()) {
            $id = $args[0];

            $pageDAO = SOY2DAOFactory::create("cms.PageDAO");
            $blockDAO = SOY2DAOFactory::create("cms.BlockDAO");

            try {
                $page = $pageDAO->getById($id);

                if ($page->getPageType() == Page::PAGE_TYPE_ERROR) {
                    throw new Exception("The 404 Not Found Page cannot be copied.");
                }

                $page->setTitle($this->getMessage("SOYCMS_COPY_MESSAGE") . $page->getTitle());
                $page->setUri($page->getUri() . "_" . SOYCMS_NOW);

                $blocks = $blockDAO->getByPageId($id);

                $page->setId(null);
                $id = $pageDAO->insert($page);

                $page->setId($id);
                $page->setIsPublished(false);

                foreach ($blocks as $block) {
                    $block->setPageId($id);

                    $blockDAO->insert($block);
                }

                $this->jump("Page.Detail." . $id . "?msg=create");
            } catch (Exception $e) {
                error_log("Page copy failed. : " . __METHOD__);
            }
        }

        $this->jump("Page");
        exit();
    }
}
