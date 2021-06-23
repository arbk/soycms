<?php
class AdministratorActionForm extends SOY2ActionForm
{
    public $id;
    public $userId;
    public $password;
    public $name;
    public $email;

    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * @validator string {"max" : 30, "min" : 4, "require" : true }
     */
    public function setUserId($value)
    {
        $this->userId = $value;
    }

    /**
     * @validator string {"max" : 30, "min" : 6, "require" : true }
     */
    public function setPassword($value)
    {
        $this->password = $value;
    }

    /**
     * @validator string {"max" : 255, "min" : 0}
     */
    public function setName($value)
    {
        $this->name = $value;
    }

    /**
     * @validator string {"max" : 255, "min" : 0}
     */
    public function setEmail($value)
    {
        $this->email = $value;
    }
}
