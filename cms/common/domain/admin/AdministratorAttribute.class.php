<?php
/**
 * @table AdministratorAttribute
 */
class AdministratorAttribute
{
    /**
     * @column admin_id
     */
    private $adminId;

    /**
     * @column admin_field_id
     */
    private $fieldId;

    /**
     * @column admin_value
     */
    private $value;

    public function getAdminId()
    {
        return $this->adminId;
    }
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    public function getFieldId()
    {
        return $this->fieldId;
    }
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }

    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
}

class AdministratorAttributeConfig
{
    /**
     * @return array
     * @param boolean is map
     */
    public static function load($flag = false)
    {
        if (!file_exists(SOY2::RootDir() . "config/administrator.attribute.php")) {
            return array();
        }
        require_once(SOY2::RootDir() . "config/administrator.attribute.php");
        if (!isset($adminAttributeConfig) || !is_array($adminAttributeConfig) || !count($adminAttributeConfig)) {
            return array();
        }

        $configs = array();
        foreach ($adminAttributeConfig as $conf) {
            $configs[] = SOY2::cast("AdministratorAttributeConfig", $conf);
        }

        if (!$flag) {
            return $configs;
        }

        $map = array();
        foreach ($configs as $config) {
            $map[$config->getFieldId()] = $config;
        }

        return $map;
    }

    public static function getTypes()
    {

        return array(
        "input" => "一行テキスト",
//      "textarea" => "複数行テキスト",
//      "checkbox" => "チェックボックス",
//      "checkboxes" => "チェックボックス(複数)",
//      "radio" => "ラジオボタン",
        "select" => "セレクトボックス",
//      "image" => "画像",
//      "file" => "ファイル",
//      "richtext" => "リッチテキスト",
//      "link" => "リンク"
        );
    }

    private $fieldId;

    private $label;

    private $type;

    //初期値
    private $defaultValue;

    //空の時の値
    private $emptyValue;

    //追加の属性（<img>でのみ有効）
    private $extraOutputs;

    private $config;

    public function getFieldId()
    {
        return $this->fieldId;
    }
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;
    }
    public function getLabel()
    {
        return $this->label;
    }
    public function setLabel($label)
    {
        $this->label = $label;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
    }
    public function getConfig()
    {
        return $this->config;
    }
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /* config method */

    public function getOutput()
    {
        return (isset($this->config["output"])) ? $this->config["output"] : null;
    }
    public function setOutput($output)
    {
        $this->config["output"] = $output;
    }
    public function getDescription()
    {
        return (isset($this->config["description"])) ? $this->config["description"] : null;
    }
    public function setDescription($description)
    {
        $this->config["description"] = $description;
    }
    public function getDefaultValue()
    {
        return (isset($this->config["defaultValue"])) ? $this->config["defaultValue"] : null;
    }
    public function setDefaultValue($defaultValue)
    {
        $this->config["defaultValue"] = $defaultValue;
    }
    public function getEmptyValue()
    {
        return (isset($this->config["emptyValue"])) ? $this->config["emptyValue"] : null;
    }
    public function setEmptyValue($emptyValue)
    {
        $this->config["emptyValue"] = $emptyValue;
    }
    public function getHideIfEmpty()
    {
        return (isset($this->config["hideIfEmpty"])) ? $this->config["hideIfEmpty"] : null;
    }
    public function setHideIfEmpty($hideIfEmpty)
    {
        $this->config["hideIfEmpty"] = $hideIfEmpty;
    }
    public function getExtraOutputs()
    {
        return (isset($this->config["extraOutputs"])) ? $this->config["extraOutputs"] : null;
    }
    public function setExtraOutputs($extraOutputs)
    {
        $this->config["extraOutputs"] = $extraOutputs;
    }
    public function getExtraValues()
    {
        return $this->extraValues;
    }
    public function setExtraValues($extraValues)
    {
        $this->extraValues = $extraValues;
    }
    public function getOption()
    {
        return (isset($this->config["option"])) ? $this->config["option"] : null;
    }
    public function setOption($option)
    {
        $this->config["option"] = $option;
    }
    public function hasOption()
    {
        return (boolean)($this->getType() == "checkboxes" || $this->getType() == "radio" || $this->getType() == "select");
    }
    public function hasExtra()
    {
        return (boolean)($this->getType() == "image");
    }

    public function getFormName()
    {
        return 'custom_field['.$this->getFieldId().']';
    }
    public function getFormId()
    {
        return 'custom_field_'.$this->getFieldId();
    }
    public function getExtraFormName($extraOutput)
    {
        return "custom_field_extra[" . $this->getFieldId() . "][" . $extraOutput . "]";
    }
    public function getExtraFormId($extraOutput)
    {
        return "custom_field_" .$this->getFieldId() . "_extra_" . $extraOutput;
    }
    public function isIndex()
    {
        return (boolean)$this->config["isIndex"];
    }

    public function getForm($value, $extraValues = null)
    {
        $h_formName = soy2_h($this->getFormName());
        $h_formID = soy2_h($this->getFormId());

        switch ($this->getType()) {
            case "select":
                $options = explode("\n", str_replace(array("\r\n","\r"), "\n", $this->getOption()));
                $value = ((null===$value)) ? $this->getDefaultValue() : $value ;

                $body = '<select class="form-control" name="'.$h_formName.'" id="'.$h_formID.'">';
//              $body .= '<option value="">----</option>';
                foreach ($options as $option) {
                    $option = trim($option);
                    if (strlen($option) > 0) {
                        $h_option = soy2_h($option);
                        $body .= '<option value="'.$h_option.'" ' .
                           (($option == $value) ? 'selected="selected"' : "") .
                           '>' . $h_option . '</option>' . "\n";
                    }
                }
                $body .= '</select>';
                break;
            case "input":
            default:
                $value = ((null===$value)) ? $this->getDefaultValue() : $value;
                $h_value = soy2_h($value);
                $body = '<input type="text" class="form-control"'
                 .' id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_value.'"';
//              if($readOnly){
//                  $body .= ' readonly="readonly"';
//              }
                $body .= '>';
                break;
        }

        return "<tr>\n<th>" . soy2_h($this->getLabel()) . "</th>\n<td>" . $body . "</td>\n</tr>\n";

  /**
      $session = SOY2ActionSession::getUserSession();
      $appLimit = $session->getAttribute("app_shop_auth_limit");
        //appLimitがfalseの場合は、在庫以外の項目をreadOnlyにする
      $readOnly = (!$appLimit) ? true : false;
        $h_formName = soy2_h($this->getFormName());
      $h_formID = soy2_h($this->getFormId());
        $title = '<dt id="' . $h_formID . '_dt"><label for="'.$h_formID.'">'
               .''
               .soy2_h($this->getLabel())
               //.' ('.soy2_h($this->getFieldId()).')'
               .' (cms:id="' . soy2_h($this->getFieldId()) . '")'
               .'</label>';
        $title .= (strlen($this->getDescription())) ? "<span class=\"option\">(" . $this->getDescription() . ")</span><br>" : "";
      $title .= '</dt>' . "\n";
        switch($this->getType()){
        case "checkbox":
          //DefaultValueがあればそれを使う
          $checkbox_value = (strlen($this->getDefaultValue()) > 0) ? $this->getDefaultValue() : $this->getLabel() ;
          $h_checkbox_value = soy2_h($checkbox_value);
          $body = '<input type="checkbox" class="custom_field_checkbox"'
                 .' id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_checkbox_value.'"'
                 .( ($value == $checkbox_value) ? ' checked="checked"' : ""  )
                 .' />';
            break;
        case "checkboxes":
          $options = explode("\n",str_replace(array("\r\n","\r"),"\n",$this->getOption()));
          //$value = ((null===$value)) ? $this->getDefaultValue() : $value ;
          if(isset($value) && strlen($value)){
            $values = explode(",", $value);
          }else{
            //カンマ区切りの初期値
            $values = strpos($this->getDefaultValue(), ",") ? array($this->getDefaultValue()) : explode(",", $this->getDefaultValue());
          }
            $body = "";
          foreach($options as $key => $option){
            $option = trim($option);
            if(strlen($option) > 0){
              $h_option = soy2_h($option);
              $id = 'custom_field_radio_'.$this->getFieldId().'_'.$key;
                $body .= '<input type="checkbox" class="custom_field_radio"' .
                   ' name="'.$h_formName.'[]"' .
                   ' id="'.$id.'"'.
                   ' value="'.$h_option.'"' .
                   ((!is_bool(array_search($option, $values))) ? ' checked="checked"' : "") .
                   ' />';
              $body .= '<label for="'.$id.'">'.$h_option.'</label>';
            }
          }
          break;
        case "radio":
          $options = explode("\n",str_replace(array("\r\n","\r"),"\n",$this->getOption()));
          $value = ((null===$value)) ? $this->getDefaultValue() : $value ;
            $body = "";
          foreach($options as $key => $option){
            $option = trim($option);
            if(strlen($option) > 0){
              $h_option = soy2_h($option);
              $id = 'custom_field_radio_'.$this->getFieldId().'_'.$key;
                $body .= '<input type="radio" class="custom_field_radio"' .
                   ' name="'.$h_formName.'"' .
                   ' id="'.$id.'"'.
                   ' value="'.$h_option.'"' .
                   (($option == $value) ? ' checked="checked"' : "") .
                   ' />';
              $body .= '<label for="'.$id.'">'.$h_option.'</label>';
            }
          }
            break;
        case "select":
          $options = explode("\n",str_replace(array("\r\n","\r"),"\n",$this->getOption()));
          $value = ((null===$value)) ? $this->getDefaultValue() : $value ;
            $body = '<select class="custom_field_select" name="'.$h_formName.'" id="'.$h_formID.'">';
          $body .= '<option value="">----</option>';
          foreach($options as $option){
            $option = trim($option);
            if(strlen($option) > 0){
              $h_option = soy2_h($option);
              $body .= '<option value="'.$h_option.'" ' .
                   (($option == $value) ? 'selected="selected"' : "") .
                   '>' . $h_option . '</option>' . "\n";
            }
          }
          $body .= '</select>';
            break;
        case "textarea":
          $value = ((null===$value)) ? $this->getDefaultValue() : $value;
          $h_value = soy2_h($value);
          $body = '<textarea class="custom_field_textarea" style="width:100%;"'
                  .' id="'.$h_formID.'"'
                  .' name="'.$h_formName.'"';
          if($readOnly){
            $body .= ' readonly="readonly"';
          }
              $body .= '>'
              .$h_value.'</textarea>';
          break;
        case "richtext":
          $value = ((null===$value)) ? $this->getDefaultValue() : $value;
          $h_value = soy2_h($value);
          $body = '<textarea class="custom_field_textarea mceEditor" style="width:100%;"'
                  .' id="'.$h_formID.'"'
                  .' name="'.$h_formName.'"'
                  .'>'
              .$h_value.'</textarea>';
          break;
        case "file":
          $value = ((null===$value)) ? $this->getDefaultValue() : $value ;
          $h_value = soy2_h($value);
            $html[] = '<div><input type="file" id="'.$h_formID.'_upload"'
                 .' name="'.$h_formName.'"'
                 .' value="" /></div>';
          $html[] = '<p><a class="button" href="javascript:void(0);" onclick="return doFileUpload(\''.$h_formID.'_upload\',\''.$h_formID.'\');">Upload</a></p>';
            $html[] = '<p>';
          $html[] = '<input type="text" id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_value.'" size="50" style="'.(((strlen($h_value) > 0)) ? "" : "display:none;").'" />';
          if(strlen($h_value) > 0){
            $html[] = ' <a href="' . $h_value . '" target="_blank" rel="noopener noreferrer">確認</a>';
            $html[] = ' <a class="button" href="javascript:void(0);" onclick="$(\'#'.$h_formID.'\').val(\'\');">Clear</a>';
          }
          $html[] = '</p>';
            $body = implode("",$html);
          break;
        case "image":
          $value = ((null===$value)) ? $this->getDefaultValue() : $value ;
          $h_value = soyshop_convert_file_path_on_admin(soy2_h($value));
            $style = (strlen($h_value) > 0) ? "" : "display:none;";
            $html = array();
          $html[] = '<div class="image_select" id="image_select_wrapper_'.$h_formID.'">';
            //選択ボタン
          $html[] = '<a class="button" href="javascript:void(0);" onclick="return ImageSelect.popup(\''.$h_formID.'\');">Select</a>';
            //クリアボタン
          $html[] = '<a class="button" href="javascript:void(0);" onclick="return ImageSelect.clear(\''.$h_formID.'\');">Clear</a>';
            //プレビュー画像
          $html[] = '<a id="image_select_preview_link_'.$h_formID.'" href="'.$h_value.'" onclick="return common_click_image_to_layer(this);" target="_blank" rel="noopener noreferrer">';
          $html[] = '<img class="image_select_preview" id="image_select_preview_'.$h_formID.'" src="'.$h_value.'"  style="'.$style.'" />';
          $html[] = '</a>';
            $html[] = '</div>';
          $html[] = '<input type="hidden" id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_value.'" />';
            $extraOutputs = explode("\n", str_replace(array("\r\n", "\r"), "\n", $this->getExtraOutputs()));
            foreach($extraOutputs as $key => $extraOutput){
            $extraOutput = trim($extraOutput);
            if(strlen($extraOutput) > 0){
              $h_extraformName = soy2_h($this->getExtraFormName($extraOutput));
              $h_extraformID = soy2_h($this->getExtraFormId($extraOutput));
              $h_extraOutput = soy2_h($extraOutput);
              $extraValue = is_array($extraValues) && isset($extraValues[$h_extraOutput]) ? $extraValues[$h_extraOutput] : "";
              $h_extraValue = soy2_h($extraValue);
                $html[] = '<br>' . $h_extraOutput . '&nbsp;<input type="text" class="custom_field_input" style="width:50%"' .
                ' id="'.$h_extraformID.'"'.
                ' name="'.$h_extraformName.'"' .
                ' value="'.$h_extraValue.'"' .
                ' />';
            }
          }
            $body = implode("",$html);
            break;
        case "link":
          $value = ((null===$value)) ? $this->getDefaultValue() : $value;
          $h_value = soy2_h($value);
          $body = '<input type="text" class="custom_field_input" style="width:70%"'
                 .' id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_value.'"';
          if($readOnly){
            $body .= ' readonly="readonly"';
          }
          $body .= ' />';
          if(strlen($h_value)){
            $body .= "&nbsp;<a href=\"" . $h_value . "\" target=\"_blank\">確認</a>";
          }
          break;
        case "input":
        default:
          $value = ((null===$value)) ? $this->getDefaultValue() : $value;
          $h_value = soy2_h($value);
          $body = '<input type="text" class="custom_field_input" style="width:100%"'
                 .' id="'.$h_formID.'"'
                 .' name="'.$h_formName.'"'
                 .' value="'.$h_value.'"';
          if($readOnly){
            $body .= ' readonly="readonly"';
          }
          $body .= ' />';
          break;
      }
        return $title . "<dd id=\"" . $h_formID . "\">" . $body . "</dd>\n";
      **/
    }
}
