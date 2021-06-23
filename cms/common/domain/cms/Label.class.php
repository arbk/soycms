<?php

/**
 * @table Label
 */
class Label
{
    const ORDER_MAX = 10000000;

    /**
     * @id
     */
    private $id;

    private $caption;

    private $description;

    private $alias;

    private $icon;

    private $color = 0;

    /**
     * @column background_color
     */
    private $backgroundColor = 16777215;

    /**
     * @column display_order
     */
    private $displayOrder;

    /**
     * @no_persistent
     */
    private $entryCount = 0;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getCaption()
    {
        return $this->caption;
    }
    public function getDisplayCaption()
    {
        return soy2_h($this->caption);
    }
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    public function getAlias()
    {
        if (strlen($this->alias)<1) {
            return $this->getId();
        }
        return $this->alias;
    }
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getDescription()
    {
        return $this->description;
    }
    public function getDisplayDescription()
    {
        return soy2_h($this->description);
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getIcon()
    {
        return $this->icon;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function getIconUrl()
    {

        $icon = $this->getIcon();

        if (!$icon) {
            $icon = "default.gif";
        }

        return CMS_LABEL_ICON_DIRECTORY_URL . $icon;
    }

    public function getEntryCount()
    {
        return $this->entryCount;
    }
    public function setEntryCount($entryCount)
    {
        $this->entryCount = $entryCount;
    }

    public function getColor()
    {
        return $this->color;
    }
    public function setColor($color)
    {
        $this->color = $color;
    }
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }
    public function setDisplayOrder($displayOrder)
    {
        if (((int)$displayOrder) >= Label::ORDER_MAX) {
            return;
        }
        $this->displayOrder = $displayOrder;
    }
    public function setDefaultDisplayOrder()
    {
        $this->displayOrder = Label::ORDER_MAX;
    }

    public function compare($label)
    {
        $a1 = $this->getDisplayOrder();
        $b1 = $label->getDisplayOrder();
        if ((null===$a1)) {
            $a1 = Label::ORDER_MAX;
        }
        if ((null===$b1)) {
            $b1 = Label::ORDER_MAX;
        }

        if ($a1 === $b1) {
            return ($this->getId() < $label->getId()) ? +1 : -1;
        }

        return ($a1 < $b1) ? -1 : +1;
    }

    /**
     * サイトの管理者でないユーザが編集可能なラベルかどうか
     */
    public function isEditableByNormalUser()
    {
        return (strpos($this->getCaption(), "*") !== 0);
    }

    /**
     * ラベル名の1つ目の/の左側をカテゴリー名、右側をサブラベル名とする
     * 例）
     * 大分類/小分類 => カテゴリー名：大分類、サブラベル名：小分類
     * 大分類/中分類/小分類 => カテゴリー名：大分類、サブラベル名：中分類/小分類
     */
    public function getCategoryName()
    {
        static $useLabelCategory;
        if ((null===$useLabelCategory)) {
            if (!class_exists("UserInfoUtil")) {
                SOY2::import("util.UserInfoUtil");
            }
            $useLabelCategory = UserInfoUtil::getSiteConfig("useLabelCategory");
        }

        if ($useLabelCategory && ( $pos = strpos($this->caption, "/") ) > 0) {
            return substr($this->caption, 0, $pos);
        } else {
            return "";
        }
    }
    public function getBranchName()
    {
        static $useLabelCategory;
        if ((null===$useLabelCategory)) {
            if (!class_exists("UserInfoUtil")) {
                SOY2::import("util.UserInfoUtil");
            }
            $useLabelCategory = UserInfoUtil::getSiteConfig("useLabelCategory");
        }
        if ($useLabelCategory && ( $pos = strpos($this->caption, "/") ) > 0) {
            return substr($this->caption, $pos+1);
        } else {
            return $this->caption;
        }
    }
}
