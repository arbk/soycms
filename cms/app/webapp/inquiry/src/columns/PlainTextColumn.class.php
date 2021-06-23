<?php

class PlainTextColumn extends SOYInquiry_ColumnBase
{

    //値の保存（出力）をするかどうか
    protected $noPersistent = false;//falseは「する」

    /**
     * フォームでの項目名は空にする：デフォルトテンプレートでcolspan=2になる
     */
    public function getLabel()
    {
        return "";
    }

    /**
     * フォーム用
     */
    public function getForm($attr = array())
    {
        return $this->label;
    }

    /**
     * 設定画面で表示する用のフォーム
     */
    public function getConfigForm()
    {
        $html = "入力欄はなく項目名だけがフォームに出力されます。<br>";

        $html .= '<label for="Column[config][noPersistent]'.$this->getColumnId().'_y">';
        $html .= '<input  id="Column[config][noPersistent]'.$this->getColumnId().'_y" type="radio" name="Column[config][noPersistent]" value="0" '.($this->noPersistent ? '' : 'checked="checked"').'>';
        $html .= 'メールに含める</label>';

        $html .= '<label for="Column[config][noPersistent]'.$this->getColumnId().'_n">';
        $html .= '<input  id="Column[config][noPersistent]'.$this->getColumnId().'_n" type="radio" name="Column[config][noPersistent]" value="1" '.($this->noPersistent ? 'checked="checked"' : '').'>';
        $html .= 'メールに含めない</label>';

        return $html;
    }


    /**
     * 保存された設定値を渡す
     */
    public function setConfigure($config)
    {
        SOYInquiry_ColumnBase::setConfigure($config);
        $this->noPersistent = (isset($config["noPersistent"])) ? str_replace("\"", "&quot;", $config["noPersistent"]) : null;
    }

    public function getConfigure()
    {
        $config = parent::getConfigure();
        $config["noPersistent"] = $this->noPersistent;
        return $config;
    }

    /**
     * データ投入用
     */
    public function getContent()
    {
        return "";
    }

    /**
     * 確認画面用
     */
    public function getView()
    {
        return $this->label;
    }
}
