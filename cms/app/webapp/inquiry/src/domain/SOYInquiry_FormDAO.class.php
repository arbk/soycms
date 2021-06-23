<?php
/**
 * @entity SOYInquiry_Form
 */
abstract class SOYInquiry_FormDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(SOYInquiry_Form $bean);

    abstract public function update(SOYInquiry_Form $bean);

    /**
     * @index id
     */
    abstract public function get();

    /**
     * @return object
     */
    abstract public function getByFormId($formId);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function delete($id);
}
