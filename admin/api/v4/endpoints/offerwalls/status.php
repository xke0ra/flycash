<?php

if ($method !== 'POST') {
    jsonError(ERROR_UNKNOWN, "Method not allowed", 405);
}

requireParams(array('cid'), $data);

$cid = helper::clearText($data['cid']);
$cid = helper::escapeText($cid);

$stmt = $dbo->prepare("SELECT * FROM offer_status WHERE cid = :cid LIMIT 1");
$stmt->execute(array(':cid' => $cid));

$result = array(
    "error" => false,
    "error_code" => ERROR_SUCCESS,
    "status" => "pending"
);

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result['status'] = intval($row['status']) === 1 ? "completed" : "pending";
}

jsonResponse($result, 200);
