<?php

class ToolBoxPage extends CMSHTMLPageBase
{
    public function execute()
    {
        $links = CMSToolBox::getLinks();
        $linkHtml = "";
        foreach ($links as $link) {
            $href = soy2_h($link["link"]);
            $onclick = (strlen($link["onclick"])>0) ? " onclick=\"".soy2_h($link['onclick'])."\"" : "" ;
            $text = soy2_h($link["text"]);
            $linkHtml .= "<a href=\"{$href}\"{$onclick} class=\"list-group-item\">{$text}</a>";
        }
        $htmls = CMSToolBox::getHTMLs();
        $otherHtml = "";
        foreach ($htmls as $html) {
            $otherHtml.= "<div>".$html."</div>";
        }

        $this->createAdd("toolbox_linkbox", "HTMLLabel", array(
            "html" => $linkHtml . $otherHtml,
        ));
    }
}
