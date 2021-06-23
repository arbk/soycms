<?php
/**
 * @entity admin.Administrator
 * @date 2007-08-22 18:42:19
 */
abstract class AdministratorDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(Administrator $bean);

    abstract public function update(Administrator $bean);

    abstract public function delete($id);

    abstract public function get();

    /**
     * @return object
     */
    abstract public function getById($id);

    /**
     * @return object
     */
    abstract public function getByUserId($userId);

    /**
     * @return object
     */
    abstract public function getByEmail($email);

    /**
     * @index id
     * @column id,#userId#
     */
    abstract public function getNameMap();

    /**
     * @return column_count
     * @columns count(id) as count
     * @query default_user = 1
     */
    abstract public function countDefaultUser();

    /**
     * @return column_count
     * @columns count(id) as count
     */
    abstract public function countUser();

    /**
     * @return object
     */
    abstract public function getByToken($token);

    /**
     * @return object
     * @query #userId# = :userId AND #email# = :email
     */
    abstract public function getByUserIdAndEmail($userId, $email);
}
