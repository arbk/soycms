<?php
/**
 * @entity admin.AdministratorAttribute
 */
abstract class AdministratorAttributeDAO extends SOY2DAO
{
    abstract public function insert(AdministratorAttribute $bean);

    /**
     * @query #adminId# = :adminId AND #fieldId# = :fieldId
     */
    abstract public function update(AdministratorAttribute $bean);

    /**
     * @index fieldId
     */
    abstract public function getByAdminId($adminId);

    /**
     * @return object
     * @query #adminId# = :adminId AND #fieldId# = :fieldId
     */
    abstract public function get($adminId, $fieldId);

    abstract public function deleteByAdminId($adminId);

    /**
     * @query #adminId# = :adminId AND #fieldId# = :fieldId
     */
    abstract public function delete($adminId, $fieldId);

    abstract public function deleteByFieldId($fieldId);
}
