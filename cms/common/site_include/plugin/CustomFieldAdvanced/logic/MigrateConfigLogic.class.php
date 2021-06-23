<?php

class MigrateConfigLogic extends SOY2LogicBase
{
    private $pluginObj;

    public function __construct()
    {
    }

    public function import()
    {
        set_time_limit(EXEC_TIME_NO_LIMIT);

        $obj = CMSPlugin::loadPluginConfig("CustomField");

        // 設定がない場合は何もしない。
        if (count($obj->customFields) === 0) {
            return;
        }

        foreach ($obj->customFields as $customField) {
            if (strlen($customField->getId()) > 0) {
                $this->pluginObj->insertField($customField);
            }
        }

        $entryDao = SOY2DAOFactory::create("cms.EntryDAO");
        $entryDao->setOrder("id ASC");
        try {
            $entries = $entryDao->getOnlyId();
        } catch (Exception $e) {
            return;
        }

        $dao = SOY2DAOFactory::create("cms.EntryAttributeDAO");
        foreach ($entries as $entry) {
            $entryId = $entry->getId();
            $fields = $obj->getCustomFields($entry->getId());

            foreach ($fields as $field) {
                $attr = new EntryAttribute();
                $attr->setEntryId($entryId);
                $attr->setFieldId($field->getId());
                $attr->setValue($field->getValue());
                $attr->setExtraValuesArray($field->getExtraValues());
                $dao->insert($attr);
            }
        }

        CMSUtil::notifyUpdate();
        CMSPlugin::redirectConfigPage();
    }

    public function setPluginObj($pluginObj)
    {
        $this->pluginObj = $pluginObj;
    }
}
