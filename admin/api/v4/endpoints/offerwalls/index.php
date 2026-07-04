<?php

$requestId = isset($data['requestId']) ? intval($data['requestId']) : 0;
$limit = isset($data['limit']) ? intval($data['limit']) : 0;
$offset = isset($data['offset']) ? intval($data['offset']) : 0;

$offerwalls = new offerwalls($dbo);
$result = $offerwalls->getOfferwalls($requestId, $limit, $offset);

if (count($result['offerwalls']) < 1) {
    jsonError(ERROR_UNKNOWN, "No offerwalls available.", 404);
}

jsonResponse($result, 200);
