<?php

class ApplyTemplatePage extends CMSWebPageBase
{
    private $id;
    private $page;
    private $mode;

    public function doPost()
    {
        $redirect = SOY2PageController::createLink("Blog.Template." . $this->id . "." . $this->mode);

        if (soy2_check_token()) {
            $target_mode = $this->mode;
            if (isset($_POST['all_template_update']) && 1==$_POST['all_template_update']) {
                $target_mode = null;
            }
            $res = $this->run("Page.ApplyTemplateAction", array("pageId"=>$this->id, "mode"=>$target_mode));

            if ($res->success()) {
                $redirect = SOY2PageController::createLink("Blog.Template." . $this->id . "." . $this->mode);
            }
        }

        echo "<!DOCTYPE html><html><head><title>-</title></head><body>";
        echo "<script type=\"text/javascript\">window.parent.location.href='" . $redirect . "';</script>";
        echo "</body></html>";

        exit();
    }

    public function __construct($arg)
    {
        $id = @$arg[0];
        $this->id = $id;
        $this->mode = @$arg[1];
        if ((null===$id) || (null===$this->mode)) {
            echo CMSMessageManager::get("SOYCMS_ERROR");
            exit();
        }

        $res = $this->run("Template.TemplateListAction");
        $templates = $res->getAttribute("list");

        $res = $this->run("Page.DetailAction", array("id"=>$id));
        if (!$res->success()) {
            echo CMSMessageManager::get("SOYCMS_ERROR");
            exit();
        }
        $page = $res->getAttribute("Page");
        $this->page = $page;

        parent::__construct();
        $this->createAdd("main_form", "HTMLForm");

        $this->createAdd("normal_template_select", "HTMLLabel", array(
            "html"=>$this->buildTemplateList(),
            "name"=>"template",
            "visible"=>($page->getPageType() == Page::PAGE_TYPE_NORMAL)
        ));
        $this->createAdd("blog_template_select", "HTMLLabel", array(
            "html"=>$this->buildBlogTemplateList(),
            "name"=>"template",
            "visible"=>($page->getPageType() == Page::PAGE_TYPE_BLOG)
        ));

        $this->createAdd("all_template_update", "HTMLCheckBox", array(
            "name"=>"all_template_update",
            "value"=>1,
            "type"=>"checkbox",
            "label"=>$this->getMessage('SOYCMS_APPLY_WEBPAGE_TEMPLATEPACK_ALL'),
            "visible"=>($page->getPageType() == Page::PAGE_TYPE_BLOG)
        ));
    }

    public function buildTemplateList()
    {
        $logic = SOY2Logic::createInstance("logic.site.Template.TemplateLogic");
        $templates = $logic->getByPageType(Page::PAGE_TYPE_NORMAL);
        $html = array();
        $html[] = '<option value="">' . CMSMessageManager::get("SOYCMS_ASK_TO_CHOOSE_PAGE_TEMPLATE_PACK") . '</option>';
        foreach ($templates as $template) {
            if (!$template->isActive()) {
                continue;
            }

            $html[] = '<optgroup label="' . $template->getName() . '">';

            foreach ($template->getTemplate() as $id => $array) {
                $html[] = '<option value="' . $template->getId() . "/" . $id . '">' . $array["name"] . '</option>';
            }

            $html[] = "</optgroup>";
        }

        return implode("\n", $html);
    }

    public function buildBlogTemplateList()
    {
        $logic = SOY2Logic::createInstance("logic.site.Template.TemplateLogic");
        $templates = $logic->getByPageType(Page::PAGE_TYPE_BLOG);
        $html = array();
        $html[] = '<option value="">' . CMSMessageManager::get("SOYCMS_ASK_TO_CHOOSE_PAGE_TEMPLATE_PACK") . '</option>';
        foreach ($templates as $template) {
            if (!$template->isActive()) {
                continue;
            }
            $html[] = '<option value="' . $template->getId() . '">' . $template->getName() . '</option>';
        }

        return implode("\n", $html);
    }

    public function getTemplateList()
    {
        $result = SOY2ActionFactory::createInstance("Template.TemplateListAction")->run();

        $list = $result->getAttribute("list");

        return $list;
    }
}
