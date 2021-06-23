<?php
class CustomField
{
    public static $TYPES = array(
        "input" => "一行テキスト",
        "textarea" => "複数行テキスト",
        "checkbox" => "チェックボックス",
        "radio" => "ラジオボタン",
        "select" => "セレクトボックス",
        "image" => "画像",
        "file" => "ファイル",
        "richtext" => "リッチテキスト",
        "link" => "リンク",
        "entry" => "記事",
//      "label" => "ラベル",
        "pair" => "ペア",
    );

    private $id;
    private $label;
    private $type;
    private $value;

    /* 以下、高度な設定 */

    //radio,selectのオプション(string)
    private $option;

    //入力欄の表示/非表示
    private $showInput = true;

    //ラベルIDとの関連付け
    private $labelId;

    //どの属性値に出力するかの設定
    private $output;

    //フィールドの説明文
    private $description;

    //初期値設定
    private $defaultValue;

    //空の時の表示しない
    private $hideIfEmpty = 0;

    //空の時の値
    private $emptyValue;

    //追加属性値
    private $extraOutputs;

    //追加属性値の値
    private $extraValues;

  //記事フィールドでラベルの固定
    private $fixedLabelId;

    // style
    const STYLE_TEXTAREA = 'style="width:99%; font-size:90%; height:6em;"';

    public function __construct($array = array())
    {
        $obj = (object)$array;
        SOY2::cast($this, $obj);
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getLabel()
    {
        return $this->label;
    }
    public function setLabel($label)
    {
        $this->label = $label;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function getOption()
    {
        return $this->option;
    }
    public function setOption($option)
    {
        $this->option = $option;
    }
    public function getLabelId()
    {
        return $this->labelId;
    }
    public function setLabelId($labelId)
    {
        $this->labelId = $labelId;
    }
    public function getOutput()
    {
        return $this->output;
    }
    public function setOutput($output)
    {
        $this->output = $output;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getFormName()
    {
        return 'custom_field['.$this->getId().']';
    }
    public function getFormId()
    {
        return 'custom_field_'.$this->getId();
    }
    public function getExtraFormName($extraOutput)
    {
        return "custom_field_extra[" . $this->getId() . "][" . $extraOutput . "]";
    }
    public function getExtraFormId($extraOutput)
    {
        return "custom_field_" .$this->getId() . "_extra_" . $extraOutput;
    }

    public function hasOption()
    {
        return (boolean)($this->getType() == "radio" || $this->getType() == "select" || $this->getType() == "pair");
    }

    public function hasExtra()
    {
        return (boolean)($this->getType() == "image");
    }

    /**
     * 1.2.0でcheckboxを追加
     */
    public function getForm($pluginObj, $fieldValue, $extraValues = null)
    {

        //表示しないとき
        if (!$this->showInput) {
            return "";
        }

        $h_formName = soy2_h($this->getFormName());
        $h_formID = soy2_h($this->getFormId());

        $title = '<label for="'.$h_formID.'">'
                 .( ($pluginObj->displayTitle) ? 'カスタムフィールド：' : '' )
                 .soy2_h($this->getLabel())
                 .( ($pluginObj->displayID) ? ' ('.soy2_h($this->getId()).')' : '' )
                 .'</label>';
        $title .= (strlen($this->getDescription())) ? '<br><span>' . $this->getDescription() . '</span>' : "";

        switch ($this->getType()) {
            case "checkbox":
                //DefaultValueがあればそれを使う
                if (strlen($this->getDefaultValue()) > 0) {
                    $checkbox_value = $this->getDefaultValue();
                    //NULLであれば初期状態 0文字の文字列であれば一度記事を投稿したことになる
                    if ((null===$fieldValue)) {
                        $fieldValue = $this->getDefaultValue();
                    }
                } else {
                    $checkbox_value = $this->getLabel() ;
                }

                $h_checkbox_value = soy2_h($checkbox_value);
                $body = '<input type="checkbox" class="custom_field_checkbox"'
                       .' id="'.$h_formID.'"'
                       .' name="'.$h_formName.'"'
                       .' value="'.$h_checkbox_value.'"'
                       .( ($fieldValue == $checkbox_value) ? ' checked="checked"' : ""  )
                       .'>';

                break;
            case "radio":
                $options = explode("\n", str_replace(array("\r\n","\r"), "\n", $this->option));
                $value = ((null===$fieldValue)) ? $this->getDefaultValue() : $fieldValue ;

                $body = "";
                foreach ($options as $key => $option) {
                    $option = trim($option);
                    if (strlen($option)>0) {
                        $h_option = soy2_h($option);
                        $id = 'custom_field_radio_'.$this->getId().'_'.$key;

                        $body .= '<input type="radio" class="custom_field_radio"' .
                                 ' name="'.$h_formName.'"' .
                                 ' id="'.$id.'"'.
                                 ' value="'.$h_option.'"' .
                                 (($option == $value) ? ' checked="checked"' : "") .
                                 '>';
                        $body .= '<label for="'.$id.'">'.$h_option.'</label>';
                    }
                }

                break;
            case "select":
            case "pair":
                $options = explode("\n", str_replace(array("\r\n","\r"), "\n", $this->option));
                $value = ((null===$fieldValue)) ? $this->getDefaultValue() : $fieldValue ;

                $body = '<div class="form-inline">' . "\n";
                $body .= "\t" . '<select class="cstom_field_select form-control" name="'.$h_formName.'" id="'.$h_formID.'">' . "\n";
                $body .= "\t\t" . '<option value="">----</option>' . "\n";
                foreach ($options as $option) {
                    $option = trim($option);
                    if (strlen($option)>0) {
                        $h_option = soy2_h($option);
                        $body .= "\t\t" . '<option value="'.$h_option.'" ' .
                                 (($option == $value) ? 'selected="selected"' : "") .
                                 '>' . $h_option . '</option>' . "\n";
                    }
                }
                $body .= "\t" . '</select>' . "\n";
                $body .= '</div>';

                break;
            case "textarea":
                $h_value = soy2_h($fieldValue);
                $body = '<textarea class="custom_field_textarea" '.self::STYLE_TEXTAREA
                        .' id="'.$h_formID.'"'
                        .' name="'.$h_formName.'"'
                        .'>'
                        .$h_value.'</textarea>';
                break;
            case "richtext":
                $h_value = soy2_h($fieldValue);
                $body = '<textarea class="custom_field_textarea mceEditor" '.self::STYLE_TEXTAREA
                        .' id="'.$h_formID.'"'
                        .' name="'.$h_formName.'"'
                        .'>'
                        .$h_value.'</textarea>';
                break;
            case "image":
            case "file":
                $h_value = soy2_h($fieldValue);
                $body = '<input type="text" class="custom_field_input" style="width:50%"'
                       .' id="'.$h_formID.'"'
                       .' name="'.$h_formName.'"'
                       .' value="'.$h_value.'"'
                       .'>'
                       .'<button type="button" onclick="open_customfield_filemanager($(\'#'.$h_formID.'\'));" style="margin-right:10px;">ファイルを指定する</button>';

                if ($h_value) {
                    if ($this->getType() == "image") {
                        $body .= '<a href="#" onclick="return preview_customfield($(\'#'.$h_formID.'\'));">Preview</a>';
                    }
                    if ($this->getType() == "file") {
                        $body .= '<a href="'.$h_value.'" target="_blank" rel="noopener noreferrer" class="btn btn-default">'.basename($h_value).'</a>';
                    }
                }

                $extraOutputs = explode("\n", str_replace(array("\r\n", "\r"), "\n", $this->extraOutputs));

                foreach ($extraOutputs as $key => $extraOutput) {
                    $extraOutput = trim($extraOutput);
                    if (strlen($extraOutput) > 0) {
                        $h_extraformName = soy2_h($this->getExtraFormName($extraOutput));
                        $h_extraformID = soy2_h($this->getExtraFormId($extraOutput));
                        $h_extraOutput = soy2_h($extraOutput);
                        $extraValue = is_array($extraValues) && isset($extraValues[$h_extraOutput]) ? $extraValues[$h_extraOutput] : "";
                        $h_extraValue = soy2_h($extraValue);

                        $body .= '<br>' . $h_extraOutput . '&nbsp;<input type="text" class="custom_field_input form-control" style="width:50%"' .
                            ' id="'.$h_extraformID.'"'.
                            ' name="'.$h_extraformName.'"' .
                            ' value="'.$h_extraValue.'"' .
                            '>';
                    }
                }

                break;
            case "link":
                $h_value = soy2_h($fieldValue);
                $body =  '<div class="form-inline">'
                        .'<input type="text" class="custom_field_input form-control" style="width:70%"'
                       .' id="'.$h_formID.'"'
                       .' name="'.$h_formName.'"'
                       .' value="'.$h_value.'"'
                       .'>';
                if (strlen($h_value)) {
                    $body .= "&nbsp;<a href=\"" . $h_value . "\" class=\"btn btn-primary\" target=\"_blank\">確認</a>";
                }
                $body .= '</div>';
                break;
            case "entry":   //出力する記事を指定 カスタムフィールドアドバンスドのみ使用可
                $values = (strlen($fieldValue)) ? explode("-", $fieldValue) : array();
                $selectedLabelId = (isset($values[0]) && is_numeric($values[0])) ? (int)$values[0] : null;
                $selectedEntryId = (isset($values[1]) && is_numeric($values[1])) ? (int)$values[1] : 0;

                //ラベルの固定設定
                if ((null===$selectedLabelId) && strlen($this->getFixedLabelId()) && is_numeric($this->getFixedLabelId())) {
                    $selectedLabelId = $this->getFixedLabelId();
                }

                $html = array();
                //ラベル一覧
                $labels = $this->_getLabels();
                if (count($labels)) {
                    $html[] = "\t<select id=\"" . $this->getFormId() . "_select\" onchange='CustomFieldEntryField.change(this, \"" . $this->getFormId() . "\", \"" . $h_formName . "\", 0);'>";
                    $html[] = "\t\t<option></option>";
                    foreach ($labels as $labelId => $caption) {
                        if ($selectedLabelId == $labelId) {
                            $html[] = "\t\t<option value=\"" . $labelId . "\" selected>" . $caption . "</option>";
                        } else {
                            $html[] = "\t\t<option value=\"" . $labelId . "\">" . $caption . "</option>";
                        }
                    }
                    $html[] = "\t</select>";
                    $html[] = "<input type=\"hidden\" name=\"" . $h_formName . "\" value=\"\">";
                    $html[] = "<span id=\"" . $this->getFormId() . "\">";
                    if (isset($selectedLabelId) || $selectedEntryId > 0) {
                        $entries = SOY2Logic::createInstance("site_include.plugin.CustomField.logic.EntryFieldLogic")->getEntriesByLabelId($selectedLabelId);
                        if (count($entries)) {
                            $html[] = "<select name=\"" . $h_formName . "\">";
                            $html[] = "<option></option>";
                            foreach ($entries as $entry) {
                                $v = $selectedLabelId . "-" . $entry["id"];
                                if ($entry["id"] == $selectedEntryId) {
                                    $html[] = "<option value=\"" . $v . "\" selected>" . $entry["title"] . "</option>";
                                } else {
                                    $html[] = "<option value=\"" . $v . "\">" . $entry["title"] . "</option>";
                                }
                            }
                            $html[] = "</select>";
                        }
                    }
                    $html[] = "</span>";
                }
                $body = implode("\n", $html);
                break;
            case "input":
            default:
                $h_value = soy2_h($fieldValue);
                $body = '<input type="text" class="custom_field_input form-control" style="width:100%"'
                       .' id="'.$h_formID.'"'
                       .' name="'.$h_formName.'"'
                       .' value="'.$h_value.'"'
                       .'>';
                break;
        }

        switch ($this->type) {
            case "checkbox":
                $return = $title . "\n" . $body;
                break;
            case "textarea":
            case "input":
            default:
                $return = '<div class="form-group">' . "\n" . $title
                       .'<div style="margin:-0.5ex 0px 0.5ex 1em;">' . "\n" . $body ."\n" .'</div>' . "\n"
                       .'</div>';
                break;
        }

        if ($this->labelId) {
            return '<div class="toggled_by_label_'.$this->labelId.'" style="display:none;">' ."\n" . $return . "\n" . '</div>' . "\n";
        } else {
            return '<div class="toggled_by_label_'.$this->labelId.'">' . "\n" . $return . "\n" . '</div>' . "\n\n";
        }
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }
    public function getEmptyValue()
    {
        return $this->emptyValue;
    }
    public function setEmptyValue($emptyValue)
    {
        $this->emptyValue = $emptyValue;
    }
    public function getHideIfEmpty()
    {
        return $this->hideIfEmpty;
    }
    public function setHideIfEmpty($hideIfEmpty)
    {
        $this->hideIfEmpty = $hideIfEmpty;
    }
    public function getShowInput()
    {
        return $this->showInput;
    }
    public function setShowInput($showInput)
    {
        $this->showInput = $showInput;
    }

    public function getExtraOutputs()
    {
        return $this->extraOutputs;
    }
    public function setExtraOutputs($extraOutputs)
    {
        $this->extraOutputs = $extraOutputs;
    }
    public function getExtraValues()
    {
        return $this->extraValues;
    }
    public function setExtraValues($extraValues)
    {
        $this->extraValues = $extraValues;
    }

    public function getFixedLabelId()
    {
        return $this->fixedLabelId;
    }
    public function setFixedLabelId($fixedLabelId)
    {
        $this->fixedLabelId = $fixedLabelId;
    }

    /** @便利な関数 **/
    public function getLabels()
    {
        return $this->_getLabels();
    }

    private function _getLabels()
    {
        static $list;
        if ((null===$list)) {
            $list = array();
            try {
                $labels = SOY2DAOFactory::create("cms.LabelDAO")->get();
            } catch (Exception $e) {
                $labels = array();
            }

            if (count($labels)) {
                foreach ($labels as $label) {
                    $list[$label->getId()] = $label->getCaption();
                }
            }
        }
        return $list;
    }

    public function getPairForm()
    {
        $v = trim($this->option);
        if (!strlen($v)) {
            return "";
        }

        $html = array();

        $opts = explode("\n", $v);
        $pairConf = (strlen($this->extraValues)) ? soy2_unserialize($this->extraValues) : array();

        if (isset($pairConf["pair"])) {
            $values = (isset($pairConf["pair"]) && is_array($pairConf["pair"])) ? $pairConf["pair"] : array();
            $cnt = (isset($pairConf["count"]) && is_numeric($pairConf["count"]) && (int)$pairConf["count"] > 0) ? (int)$pairConf["count"] : 1;
        } else {
            $values = $pairConf;
            $cnt = 1;
        }

        $html[] = "<div class=\"form-inline\">";
        $html[] = "パターン：<input type=\"number\" name=\"pair_count\" class=\"form-control\" value=\"" . $cnt . "\" min=\"1\" style=\"width:70px;\">";
        $html[] = "</div>";


        for ($i = 0; $i < $cnt; ++$i) {
            $pairValues = (isset($values[$i])) ? $values[$i] : array();

            $html[] = "<table>";
            $html[] = "<caption><strong>ペア" . ($i + 1) . "</strong> (cms:id=\"" . $this->id . "_pair_" . ($i + 1) . "\")</caption>";
            foreach ($opts as $opt) {
                $opt = trim($opt);
                if (!strlen($opt)) {
                    continue;
                }
                $html[] = "<tr>";
                $html[] = "<td>" . soy2_h($opt) . "</td>";
                $idx = CustomfieldAdvancedUtil::createHash($opt);
                $html[] = "<td><input type=\"text\" class=\"form-control\" name=\"pair[" . $i . "][" . $idx . "]\" value=\"" . ((isset($pairValues[$idx])) ? $pairValues[$idx] : "") . "\"></td>";
                $html[] = "</tr>";
            }
            $html[] = "</table>";
        }

        return implode("\n", $html);
    }
}
