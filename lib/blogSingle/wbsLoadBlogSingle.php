<?php


defined('ABSPATH') || exit;
require_once "functions/wp-enqueue.php";
require_once "functions/ajax.php";


function wbsLoadBlogSingle()
{
    require_once "template/blogSinglePage.php";
}