<?php

class UploadPage extends CMSWebPageBase
{
    public function doPost()
    {
        if (soy2_check_token()) {
            $result = $this->run("EntryTemplate.TemplateUploadAction");

            if ($result->success()) {
                $this->addMessage("ENTRY_TEMPLATE_UPLOAD_SUCCESS");
            } else {
                $this->addErrorMessage("ENTRY_TEMPLATE_UPLOAD_FAILED");
            }
        }

        echo '<!DOCTYPE html><html><head><title>-</title><script type="text/javascript">parent.location.reload();</script></head></html>';
    }

    public function __construct()
    {
        parent::__construct();
        $this->createAdd("upload_form", "HTMLForm");
    }
}
