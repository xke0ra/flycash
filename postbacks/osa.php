<?php

/**
 * OgAds / OSA Postback Handler
 *
 * Cleaned version — uses unified router via index.php
 * Legacy file kept for backward compatibility with existing offerwall setup.
 */

include_once("../admin/core/init.inc.php");

$handler = new FlyCash\Postback\Handlers\OgAdsHandler($dbo);
$handler->handle();
