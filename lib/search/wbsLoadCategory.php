<?php


defined('ABSPATH') || exit;
require_once "functions/wp-enqueue.php";
require_once "functions/ajax.php";


function wbsLoadCategory()
{
    require_once "template/categoryPage.php";
}