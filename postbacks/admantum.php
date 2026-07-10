<?php

/**
 * AdMantum Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/admantum.php?user_id={uid}&amount={virtual_currency}
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\AdMantumHandler($dbo);
$handler->handle();
