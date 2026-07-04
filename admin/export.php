<?php

require_once "core/init.inc.php";

if (!admin::isSession()) {
    header("Location: login.php");
    exit;
}

$type = isset($_GET['type']) ? preg_replace('/[^a-z_]/', '', $_GET['type']) : '';
$fmt = isset($_GET['format']) && $_GET['format'] === 'csv' ? 'csv' : 'csv';

if (empty($type)) {
    header("Location: admin.php");
    exit;
}

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=\"{$type}_" . date('Y-m-d') . ".csv\"");
header("Cache-Control: no-cache, no-store, must-revalidate");

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

switch ($type) {
    case 'users':
        fputcsv($output, array('ID', 'Username', 'Email', 'Points', 'State', 'Refer', 'Refered', 'Reg Date', 'Last Access', 'IP'));
        $stmt = $dbo->query("SELECT id, login, email, points, state, refer, refered, regtime, last_access, ip_addr FROM users ORDER BY id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, array(
                $row['id'], $row['login'], $row['email'], $row['points'],
                $row['state'], $row['refer'], $row['refered'],
                $row['regtime'] ? date('Y-m-d H:i', $row['regtime']) : '',
                $row['last_access'] ? date('Y-m-d H:i', $row['last_access']) : '',
                $row['ip_addr']
            ));
        }
        break;

    case 'requests':
        fputcsv($output, array('ID', 'Username', 'Gift', 'Amount', 'Points', 'Status', 'Date', 'Note'));
        $stmt = $dbo->query("SELECT rid, username, gift_name, req_amount, points_used, status, date, note FROM requests ORDER BY rid");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statusMap = array(0 => 'Pending', 1 => 'Completed', 2 => 'Cancelled');
            fputcsv($output, array(
                $row['rid'], $row['username'], $row['gift_name'], $row['req_amount'],
                $row['points_used'], isset($statusMap[$row['status']]) ? $statusMap[$row['status']] : $row['status'],
                is_numeric($row['date']) ? date('Y-m-d', $row['date']) : $row['date'],
                $row['note']
            ));
        }
        break;

    case 'completed':
        fputcsv($output, array('ID', 'Username', 'Gift', 'Amount', 'Points', 'Status', 'Date', 'Note'));
        $stmt = $dbo->query("SELECT rid, username, gift_name, req_amount, points_used, status, date, note FROM completed ORDER BY rid");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, array(
                $row['rid'], $row['username'], $row['gift_name'], $row['req_amount'],
                $row['points_used'], $row['status'],
                is_numeric($row['date']) ? date('Y-m-d', $row['date']) : $row['date'],
                $row['note']
            ));
        }
        break;

    case 'tracker':
        fputcsv($output, array('ID', 'Username', 'Points', 'Type', 'Date'));
        $stmt = $dbo->query("SELECT id, username, points, type, date FROM tracker ORDER BY id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, array(
                $row['id'], $row['username'], $row['points'], $row['type'],
                is_numeric($row['date']) ? date('Y-m-d H:i', $row['date']) : $row['date']
            ));
        }
        break;

    default:
        fputcsv($output, array('Error', 'Unknown export type: ' . $type));
}

fclose($output);
