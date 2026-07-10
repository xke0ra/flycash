<?php
$pagename = 'transactions';
	$container = '';

    include_once("includes/user.inc.php");

    $tracker = new tracker($dbo);

    // User's All Transactions
    $allTransactions = $tracker->getuserTransactionsAPI($req_user_info['id']);
    $allTransactions = $allTransactions['transactions'];

    function formatTransactionType(string $type): string {
        return match($type) {
            'cr' => 'Earned',
            'db' => 'Redeemed',
            default => ucfirst($type),
        };
    }

    function formatTransactionStatus(string $type, string $status): string {
        if ($type === 'cr') {
            return 'Completed';
        }
        return match($status) {
            'pending'    => 'Pending',
            'processing' => 'Processing',
            'completed'  => 'Completed',
            'rejected'   => 'Rejected',
            'cancelled'  => 'Cancelled',
            default      => ucfirst($status),
        };
    }

?><!DOCTYPE html>
<?php include_once 'includes/vendor_comments.php'; ?>
<html lang="en">
<head>
    <?php include_once 'includes/dashboard_title.php'; ?>
    <?php include_once 'includes/global_header_scripts.php'; ?>
</head>
<body>

    <?php include_once 'includes/dashboard_header_mobile.php'; ?>
    <?php include_once 'includes/dashboard_header.php'; ?>

    <div class="page-header">
        <div class="container">
            <div>
                <h1 class="page-title">Transactions</h1>
                <div class="page-breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="sep">/</span>
                    <span>Transactions</span>
                </div>
            </div>
            <div class="page-actions">
                <a href="redeem.php" class="btn btn-primary">Redeem</a>
            </div>
        </div>
    </div>

    <main class="page-content">
        <div class="container">

            <?php include_once("../admin/controller/notices.php"); ?>

            <div class="card-modern">
                <div class="card-modern-header">
                    <h3>All Transactions <small>Showing all Earning and Redeem history.</small></h3>
                    <div>
                        <a href="redeem.php" class="btn btn-primary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            New Redeem
                        </a>
                    </div>
                </div>
                <div class="card-modern-body" style="padding:0;">
                    <div style="padding:16px 24px;border-bottom:1px solid var(--gray-100);">
                        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                            <div style="position:relative;flex:1;min-width:180px;">
                                <input type="text" class="form-control" placeholder="Search..." id="generalSearch" style="padding-left:36px;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--gray-400);font-size:14px;">&#128269;</span>
                            </div>
                            <select class="form-control" id="kt_form_type" style="width:auto;min-width:120px;">
                                <option value="">All Types</option>
                                <option value="cr">Credit</option>
                                <option value="db">Debit</option>
                            </select>
                            <select class="form-control" id="kt_form_status" style="width:auto;min-width:120px;">
                                <option value="">All Status</option>
                                <option value="0">Pending</option>
                                <option value="1">Success</option>
                                <option value="2">Processing</option>
                                <option value="3">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="table-modern" id="html_table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Name</th>
                                    <th>Points</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($allTransactions) && is_array($allTransactions) && count($allTransactions) > 0) {
                                    foreach($allTransactions as $transaction){
                                        $tn_type = strtolower($transaction['tn_type']);
                                        $type_label = formatTransactionType($tn_type);
                                        $status_label = formatTransactionStatus($tn_type, strtolower($transaction['tn_status']));
                                        $status_class = match(strtolower($transaction['tn_status'])) {
                                            'pending', 'processing' => 'warning',
                                            'rejected', 'cancelled' => 'danger',
                                            default => 'success',
                                        };
                                    ?>
                                <tr>
                                    <td><?php echo esc_attr($transaction['tn_id']); ?></td>
                                    <td><?php echo esc_attr($transaction['tn_name']); ?></td>
                                    <td><?php echo esc_attr($transaction['tn_points']); ?></td>
                                    <td><?php echo esc_attr($transaction['tn_date']); ?></td>
                                    <td><?php echo esc_attr($req_user_info['fullname']); ?></td>
                                    <td><span class="badge badge-<?php echo $tn_type === 'cr' ? 'success' : 'warning'; ?>"><?php echo esc_attr($type_label); ?></span></td>
                                    <td><span class="badge badge-<?php echo esc_attr($status_class); ?>"><?php echo esc_attr($status_label); ?></span></td>
                                </tr>
                                <?php } } else { ?>
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:48px 16px;">
                                        <div class="notif-empty">
                                            <strong>No transactions yet</strong>
                                            <p style="margin:8px 0 0;color:var(--gray-400);font-size:13px;">Earn points by completing offers and surveys, then redeem them for rewards.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include_once 'includes/dashboard_footer.php'; ?>
    <?php include_once 'includes/dashboard_scroll_to_top.php'; ?>
    <?php include_once 'includes/global_footer_scripts.php'; ?>

    <script src="assets/js/pages/html-table.js" type="text/javascript"></script>

</body>
</html>
