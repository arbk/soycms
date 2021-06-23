<?php
/**
 * @entity SOYInquiry_Column
 */
abstract class SOYInquiry_ColumnDAO extends SOY2DAO
{
    /**
      * @return id
     */
    abstract public function insert(SOYInquiry_Column $bean);

    abstract public function update(SOYInquiry_Column $bean);

    abstract public function get();

    abstract public function getByFormId($formId);

    /**
     * @columns count(id) as count_columns
     * @return row_count_columns
     */
    abstract public function countByFormId($formId);

    /**
     * @order #order#
     */
    abstract public function getOrderedColumnsByFormId($formId);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function delete($id);

    abstract public function deleteByFormId($formId);

    /**
     * @columns #order#
     * @query id = :id
     */
    abstract public function updateDisplayOrder($id, $order);

    /**
     * @columns #columnId#
     * @query id = :id
     */
    abstract public function updateColumnId($id, $columnId);

    /**
     * @final
     */
    public function reorderColumns($formId)
    {
        $columns = $this->getOrderedColumnsByFormId($formId);

        $count = 1;
        foreach ($columns as $column) {
            $this->updateDisplayOrder($column->getId(), $count * 10);
            $this->updateColumnId($column->getId(), "column_" . $count);
            $count++;
        }
    }
}
