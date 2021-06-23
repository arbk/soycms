<?php
/**
 * @entity AdminDataSets
 */
abstract class AdminDataSetsDAO extends SOY2DAO
{
    abstract public function insert(AdminDataSets $bean);

    /**
     * @return object
     * @query class_name = :class
     */
    abstract public function getByClass($class);

    /**
     * @sql delete from soycms_admin_data_sets where class_name = :class
     */
    abstract public function clear($class);

    abstract public function get();
}
