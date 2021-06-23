<?php
/**
 * @table EntryAttribute
 */
class EntryAttribute
{
    /**
     * @column entry_id
     */
    private $entryId;

    /**
     * @column entry_field_id
     */
    private $fieldId;

    /**
     * @column entry_value
     */
    private $value;

    /**
     * soy2_serialize対象
     * @column entry_extra_values
     */
    private $extraValues;

    /**
     * @no_persistent
     */
    private $customFieldObject;

    public function getEntryId()
    {
        return $this->entryId;
    }
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
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

    public function getExtraValues()
    {
        return $this->extraValues;
    }
    public function setExtraValues($extraValues)
    {
        $this->extraValues = $extraValues;
    }

    public function getExtraValuesArray()
    {
        $res = soy2_unserialize($this->extraValues);
        if (is_array($res)) {
            return $res;
        } else {
            return array();
        }
    }
    public function setExtraValuesArray($extraValues)
    {
        if (is_array($extraValues)) {
            $this->extraValues = soy2_serialize($extraValues);
        } else {
            $this->extraValues = soy2_serialize(array());
        }
    }

    public function getCustomFieldObject()
    {
        return $this->customFieldObject;
    }
    public function setCustomFieldObject($customFieldObject)
    {
        $this->customFieldObject = $customFieldObject;
    }

    public function getCustomFieldObjectArray()
    {
        $res = soy2_unserialize($this->customFieldObject);
        if ($res instanceof CustomField) {
            return $res;
        } else {
            return new CustomField();
        }
    }
    public function setCustomFieldObjectArray($customFieldObject)
    {
        if ($customFieldObject instanceof CustomField) {
            $this->customFieldObject = soy2_serialize($customFieldObject);
        } else {
            $this->customFieldObject = soy2_serialize(new CustomField());
        }
    }
}
