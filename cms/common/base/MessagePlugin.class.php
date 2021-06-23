<?php

class MessagePlugin extends PluginBase
{
    public function executePlugin($soyValue)
    {
        $helpMessage = CMSMessageManager::get($soyValue);

        switch ($this->tag) {
            case "img":
                $this->_attribute["src"] = SOY2PageController::createRelativeLink("./image/icon/help.gif");
                $this->_attribute["class"] = "help_icon";
                $this->_attribute["onMouseOver"] = "this.style.cursor='pointer'";
                $this->_attribute["onMouseOut"] = "this.style.cursor='auto'";
                if ($soyValue) {
                    $this->_attribute["onclick"] = "common_show_message_popup(this,'".$helpMessage."')";
                }
                break;
            case "span":
                $helpMessage = SOY2HTML::ToText($helpMessage);
                $this->setInnerHTML('<i class="fa fa-question-circle fa-fw" data-toggle="tooltip" data-placement="right" title="'.soy2_h($helpMessage).'"></i>');
                break;
            default:
                ;
        }
    }

    public function getStartTag()
    {
        switch ($this->tag) {
            case "span":
                if (isset($this->_attribute["class"]) && strlen($this->_attribute["class"])) {
                    $this->_attribute["class"] = "help ".trim($this->_attribute["class"]);
                } else {
                    $this->_attribute["class"] = "help";
                }
                return "<span class=\"".soy2_h($this->_attribute["class"])."\">";
                break;
            case "img":
            default:
                return parent::getStartTag();
        }
    }

    public function getEndTag()
    {
        switch ($this->tag) {
            case "span":
                return "</span>";
                break;
            case "img":
            default:
                return parent::getEndTag();
        }
    }
}
