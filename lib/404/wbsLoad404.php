<?php

defined('ABSPATH') || exit;
require_once "functions/wp-enqueue.php";

function wbsLoad404()
{
    require_once "template/404Page.php";
}