<?php

/**
 * AdGateMedia Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/adgatemedia.php?tx_id={tx}&user_id={s2}&point_value={points}
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\AdGateMediaHandler($dbo);
$handler->handle();
