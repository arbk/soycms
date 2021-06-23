<?php
require_once(dirname(__DIR__)."/Page/RemovePage.class.php");

class RemoveBlogPage extends RemovePage
{
    protected $pageToGoBack = "Blog.List";
}
