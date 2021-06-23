<?php

/**
 * @entity cms.TemplateHistory
 */
abstract class TemplateHistoryDAO extends SOY2DAO
{
    /**
     * @return id
     */
    abstract public function insert(TemplateHistory $bean);
    abstract public function update(TemplateHistory $bean);
    abstract public function delete($id);

    /**
     * @return object
     */
    abstract public function getById($id);

    abstract public function get();

    /**
     * @order id desc
     */
    abstract public function getByPageId($pageId);


    /**
     * @final
     */
    public function deletePastHistory($pageId, $count = 10)
    {
        $sql = 'SELECT id from TemplateHistory WHERE page_id = :pageId ORDER BY update_date DESC';

        $historyIds = $this->executeQuery($sql, array(':pageId'=>$pageId));

        if (count($historyIds) <= $count) {
            return true;
        }

        foreach ($historyIds as $entity) {
            if ($count-- > 0) {
                continue;
            }
            $id = $entity['id'];
            try {
                $this->delete($id);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }
}
