<?php
/**
 * @table Block
 */
class Block
{
////表示順も兼ねます。
//const BLOCK_LIST = "EntryBlockComponent,LabeledBlockComponent,SiteLabeledBlockComponent,MultiLabelBlockComponent,PluginBlockComponent";

    /**
     * @id
     */
    private $id;

    /**
     * @column soy_id
     */
    private $soyId;

    /**
     * @column page_id
     */
    private $pageId;
    private $class;
    private $object;

    /**
     * @no_persistent
     */
    private $_object;

    /**
     * @no_persistent
     */
    private $isUse = false;
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getSoyId()
    {
        return $this->soyId;
    }
    public function setSoyId($soyId)
    {
        $this->soyId = $soyId;
    }
    public function getClass()
    {
        return $this->class;
    }
    public function setClass($class)
    {
        $this->class = $class;
    }
    public function getObject()
    {
        return $this->object;
    }
    public function setObject($object)
    {
        if (is_object($object)) {
            $this->object = serialize($object);
            $this->_object = $object;
        } else {
            $this->object = $object;
            $this->_object = unserialize($object);
        }
    }

    /**
     * Block#objectのインスタンスが欲しい時はこちらを呼びましょう。
     */
    public function getObjectInstance()
    {
        return $this->_object;
    }
    public function getPageId()
    {
        return $this->pageId;
    }
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    /**
     * @return BlockComponent
     *
     * BlockComponentの設置場所は「/common/site_include/block」以下
     *
     */
    public function getBlockComponent()
    {
        try {
            if (strlen($this->getClass()) < 1) {
                throw new Exception();
            }

            if (!class_exists($this->getClass())) {
                require_once(CMS_BLOCK_DIRECTORY . $this->getClass() . "/block.php");
            }

            if ($this->getObject()) {
                $component = unserialize($this->object);
            } else {
                $className = $this->getClass();
                $component = new $className();
            }

            return $component;
        } catch (Exception $e) {
        }

        return new BrokenBlockComponent();
    }

    /**
     * @return Array ブロックプラグインリスト
     */
    public static function getBlockComponentList()
    {
        $dir = CMS_BLOCK_DIRECTORY;

        $files = explode(",", SOYCMS_BLOCK_LIST);

        $array = array();
        foreach ($files as $key => $file) {
            if (!is_dir(CMS_BLOCK_DIRECTORY . $file)) {
                continue;
            }

            if (strstr($file, ".")) {
                continue;
            }

            include_once(CMS_BLOCK_DIRECTORY . $file . "/block.php");

            $array[$file] = new $file();
        }

        return $array;
    }

    /**
     * テンプレートに書かれているかどうか
     * @return boolean
     */
    public function isUse()
    {
        return $this->isUse;
    }
    public function setIsUse($value)
    {
        $this->isUse = (boolean)$value;
    }
}

/**
 * Block
 */
interface BlockComponent
{
    const ORDER_TYPE_CDT = "otcdt"; // 作成日
    const ORDER_TYPE_TTL = "otttl"; // タイトル

    const ORDER_ASC = "asc"; // 昇順
    const ORDER_DESC = "desc"; // 降順

    const ENTRY_OPDATA_ALL = "opall"; // 全て
    const ENTRY_OPDATA_CNT = "opcnt"; // 本文
    const ENTRY_OPDATA_TTL = "opttl"; // タイトル

    /**
     * @return SOY2HTML
     * 設定画面用のHTMLPageComponent
     */
    public function getFormPage();

    /**
     * @return SOY2HTML
     * 表示用コンポーネント
     */
    public function getViewPage($page);

    /**
     * @return SOY2HTML
     * 一覧表示用コンポーネント
     */
    public function getInfoPage();

    /**
     * @return string コンポーネント名
     */
    public function getComponentName();

    /**
     * @return string コンポーネント説明
     */
    public function getComponentDescription();
}

/**
 * 万が一データが壊れた場合にBroken情報を伝えるためのコンポーネント
 */
class BrokenBlockComponent implements BlockComponent
{
    /**
     * @return SOY2HTML
     * 設定画面用のHTMLPageComponent
     */
    public function getFormPage()
    {
        return "broken";
    }

    /**
     * @return SOY2HTML
     * 表示用コンポーネント
     */
    public function getViewPage($page)
    {
    }

    /**
     * @return SOY2HTML
     * 一覧表示用コンポーネント
     */
    public function getInfoPage()
    {
        return "this block is broken";
    }

    /**
     * @return string コンポーネント名
     */
    public function getComponentName()
    {
        return "this block is broken";
    }

    /**
     * @return string コンポーネント説明
     */
    public function getComponentDescription()
    {
        return "";
    }
}
