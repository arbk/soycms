<?php
/**
 * @table soycms_admin_data_sets
 */
class AdminDataSets
{
    /**
     * @id
     */
    private $id;

    /**
     * @column class_name
     */
    private $className;

    /**
     * @column object_data
     */
    private $object;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getClassName()
    {
        return $this->className;
    }
    public function setClassName($className)
    {
        $this->className = $className;
    }
    public function getObject()
    {
        return $this->object;
    }
    public function setObject($object)
    {
        $this->object = $object;
    }

    public static function put($class, $obj)
    {
        $data = new AdminDataSets();
        $data->setClassName($class);
        $data->SetObject(serialize($obj));

        $dao = SOY2DAOFactory::create("admin.AdminDataSetsDAO");

        try {
            $dao->clear($class);
        } catch (Exception $e) {
        }

        $dao->insert($data);
    }

    public static function get($class, $onNull = false)
    {
        try {
            $dao = SOY2DAOFactory::create("admin.AdminDataSetsDAO");
            $data = $dao->getByClass($class);

            $res = unserialize($data->getObject());
            if ($res === false) {
                throw new Exception();
            }

            return $res;
        } catch (Exception $e) {
            if ($onNull !== false) {
                return $onNull;
            }

            throw $e;
        }
    }

    public static function delete($class)
    {
        $dao = SOY2DAOFactory::create("admin.AdminDataSetsDAO");
        $dao->clear($class);
    }
}
