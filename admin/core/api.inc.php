<?php
\FlyCash\Api::logRequest();

header("Content-type: application/json; charset=utf-8");
$numFunc = new functions($dbo);
if(!$numFunc->getConfig('ADMIN')){ api::printError(999, ""); }
