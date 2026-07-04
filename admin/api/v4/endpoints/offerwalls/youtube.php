<?php

$requestId = isset($data['requestId']) ? intval($data['requestId']) : 0;
$limit = isset($data['limit']) ? intval($data['limit']) : 0;
$offset = isset($data['offset']) ? intval($data['offset']) : 0;

$offerwalls = new offerwalls($dbo);
$result = $offerwalls->getYoutubeOffers($requestId, $limit, $offset);

jsonResponse($result, 200);
