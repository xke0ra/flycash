<?php

/**
 * KiwiWall Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/kiwiwall.php?sub_id={sub_id}&status={status}&amount={amount}
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\KiwiWallHandler($dbo);
$handler->handle();
