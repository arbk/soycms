<?php

require_once(dirname(__DIR__)."/Page/PutTrashPage.class.php");

class PutTrashBlogPage extends PutTrashPage
{
    protected $pageToGoBack = "Blog.List";
}
