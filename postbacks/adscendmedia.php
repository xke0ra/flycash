<?php

/**
 * AdScendMedia Postback Handler
 *
 * Legacy file — delegates to unified postback router.
 * URL: /postbacks/adscendmedia.php?offerid=[OID]&name=[ONM]&rate=[CUR]&sub1=[SB1]
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\AdScendMediaHandler($dbo);
$handler->handle();
