<?php

class RoleListComponent extends HTMLList
{
    private $roles;
    private $application;

    protected function populateItem($entity, $key)
    {

        $userId = (is_numeric($entity->getId())) ? (int)$entity->getId() : 0;

        $this->addLabel("user_name", array(
            "text" => (strlen($entity->getName())) ? $entity->getName() . " (".$entity->getUserId().")" : $entity->getUserId()
        ));


        if (is_array($this->roles) && isset($this->roles[$userId])) {
            $role = $this->roles[$userId];
            $roleValeu = $role->getAppRole();
        } else {
            $roleValeu = 0;
        }

        $this->addSelect("role", array(
            "options" => AppRole::getRoleLists($this->application["useMultipleRole"]),
            "indexOrder" => true,
            "name" => "AppRole[".$userId."]",
            "selected" => $roleValeu,
            "visible" => !$entity->getIsDefaultUser()
        ));
    }

    public function getRoles()
    {
        return $this->roles;
    }
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
    public function getApplication()
    {
        return $this->application;
    }
    public function setApplication($application)
    {
        $this->application = $application;
    }
}
