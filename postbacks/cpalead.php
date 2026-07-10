<?php

/**
 * CpaLead Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/cpalead.php?subid={subid}&subid2={subid2}&virtual_currency={virtual_currency}
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\CpaLeadHandler($dbo);
$handler->handle();
