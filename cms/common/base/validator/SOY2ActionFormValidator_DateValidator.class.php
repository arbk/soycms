<?php
/**
 * 日付型Validator
 * 2007-30-56　なのはﾀﾞﾒ
 */
class SOY2ActionFormValidator_DateValidator extends SOY2ActionFormValidator
{
    private $max;
    private $min;

    public function __construct($obj)
    {
        if (null!==@$obj->max) {
            $this->max = strtotime($obj->max);
        } else {
            $this->max = null;
        }

        if (null!==@$obj->min) {
            $this->min = strtotime($obj->min);
        } else {
            $this->min = null;
        }
    }

    public function validate(SOY2ActionForm &$form, $propName, $value, $require)
    {
        $tmpDate = strtotime($value);

        if ($require && strlen($value) < 1) {
            $form->setError($propName, new ActionFormError(get_class($form), $propName, get_class($this), "require", $this->getMessage("require")));
        }

        if (!$require && strlen($value) < 1) {
            return null;
        }

        if ($tmpDate === false) {
            $form->setError($propName, new ActionFormError(get_class($form), $propName, get_class($this), "format", $this->getMessage("format")));
        } else {
            if (null!==$this->max && $this->max < $tmpDate) {
                $form->setError($propName, new ActionFormError(get_class($form), $propName, get_class($this), "max", $this->getMessage("max")));
            }

            if (null!==$this->min && $this->min > $tmpDate) {
                $form->setError($propName, new ActionFormError(get_class($form), $propName, get_class($this), "min", $this->getMessage("min")));
            }
        }

        return $value;
    }
}
