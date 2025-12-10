<?php


defined('ABSPATH') || exit;
require_once "functions/wp-enqueue.php";

function wbsLoadReceiveNewProductEmails()
{
    require_once "template/receiveNewProductEmailsPage.php";
}