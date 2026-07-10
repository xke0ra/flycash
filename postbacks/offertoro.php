<?php

/**
 * OfferToro Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/offertoro.php?user_id={user_id}&amount={amount}
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\OfferToroHandler($dbo);
$handler->handle();
