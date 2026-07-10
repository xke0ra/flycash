<?php

$rel = dirname(__FILE__);
include_once $rel."/../admin/core/init.inc.php";

if (!account::isSession()) {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'error' => 'not_logged_in'));
    exit;
}

include_once $rel."/includes/user.inc.php";
include_once $rel."/../admin/core/class.notifications.inc.php";

$notif = new notifications($dbo);

$action = isset($_GET['action']) ? $_GET['action'] : '';

header('Content-Type: application/json');

switch ($action) {

    case 'fetch':
        $unread = $notif->getUnread($req_user_info['id']);
        $count = $notif->countUnread($req_user_info['id']);
        echo json_encode(array('success' => true, 'count' => $count, 'notifications' => $unread));
        break;

    case 'read':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $notif->markAsRead($id, $req_user_info['id']);
        }
        echo json_encode(array('success' => true));
        break;

    case 'read_all':
        $notif->markAllAsRead($req_user_info['id']);
        echo json_encode(array('success' => true));
        break;

    default:
        echo json_encode(array('success' => false, 'error' => 'Unknown action'));
}
