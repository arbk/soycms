<?php

class RemovePage extends CMSWebPageBase
{
    public function __construct($arg)
    {
        $id = @$arg[0];

        if (soy2_check_token()) {
            $result = SOY2ActionFactory::createInstance("Block.RemoveAction", array("id"=>$id))->run();
            $pageId = $result->getAttribute("pageId");
        } else {
            $result = $this->run("Block.DetailAction", array("id"=>$id));
            $pageId = $result->getAttribute("Block")->getPageId();
        }

        $webPage = SOY2HTMLFactory::createInstance("Block.BlockListPage", array(
            "pageId" => $pageId
        ));

        //BlockListPageはコンポーネントなので
        $webPage->execute();
        $html = $webPage->getObject();

        header("Content-Type: text/html; charset=" . SOY2::CHARSET . ";");
        echo "<!DOCTYPE html><html><head>";
        echo '<meta charset="' . SOY2::CHARSET . '"><title>-</title>';
        echo "</head><body>";
        echo '<div id="result" style="display:none;">'.$html.'</div>';
        echo "<script type=\"text/javascript\">";
        echo 'window.parent.document.main_form.s_token.value="'.soy2_get_token().'";';
        echo 'window.parent.document.getElementById("block_list").innerHTML = document.getElementById("result").innerHTML;';
        echo "</script>";
        echo "</body></html>";
        echo "\n";

        exit();
    }
}
