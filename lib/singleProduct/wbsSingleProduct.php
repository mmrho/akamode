<?php


defined('ABSPATH') || exit;
require_once "functions/wp-enqueue.php";
require_once "functions/ajax.php";


function wbsLoadSingleProduct()
{
    require_once "template/singleProductPage.php";
}