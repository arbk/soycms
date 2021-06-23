<?php
 /**
 * @entity admin.AutoLogin
 */
abstract class AutoLoginDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(AutoLogin $bean);

    abstract public function update(AutoLogin $bean);

    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getByToken($token);

    /**
     * @query #limit# < :time
     */
    abstract public function deleteByTime($time);

    abstract public function get();

    abstract public function deleteByUserId($userId);
}
