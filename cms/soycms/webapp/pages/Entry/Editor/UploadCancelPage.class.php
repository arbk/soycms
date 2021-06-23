<?php

class UploadCancelPage extends CMSWebPageBase
{
    public function doPost()
    {
        echo json_encode(
            $this->run("Entry.CancelUploadFileAction")->getAttribute("result")
        );
        exit;
    }
}
