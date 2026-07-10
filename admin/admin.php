<?php
$pagename = 'dashboard';
	$container = '';
	
	include_once("inc/admin.inc.php");
	
    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>Overview</h4>
            <p>Welcome back, <?php echo htmlspecialchars($helper->getAdminFullName(admin::getAdminID()), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>

    <!-- Stats -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="stat-value"><?php echo $totalUsers; ?></div>
            <div class="stat-label">Total Users</div>
            <div class="stat-trend <?php echo $totalusersPercent >= 0 ? 'up' : 'down'; ?>"><?php echo $totalusersPercent; ?>%</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-value"><?php echo $pendingRequests + $processingRequests; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-value"><?php echo $newUsers; ?></div>
            <div class="stat-label">New Users</div>
            <div class="stat-trend <?php echo $usersIncreased ? 'up' : 'down'; ?>"><?php echo $newusersPercent; ?>%</div>
        </div>
        <div class="admin-stat-card gradient">
            <div class="stat-value"><?php echo $todayActiveusers; ?></div>
            <div class="stat-label">Active Today</div>
        </div>
    </div>

    <?php if($configs->getConfig("INCOME_OVERVIEW") == 1): ?>
    <div class="admin-stats-grid">
        <?php if($configs->getConfig("INCOME_OVERVIEW_TITLE") == 1): ?>
        <div class="col-12" style="grid-column:1/-1;padding:0;">
            <div class="section-title"><h4>Income Overview (Approx.)</h4></div>
        </div>
        <?php endif; ?>
        <div class="admin-stat-card">
            <div class="stat-value">$<?php echo $todayProfit; ?></div>
            <div class="stat-label">Today's Profit</div>
            <div class="stat-trend <?php echo $profitIncreased ? 'up' : 'down'; ?>"><?php echo $todayProfitPercent; ?>%</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-value">$<?php echo $weekProfitFinal; ?></div>
            <div class="stat-label">This Week</div>
            <div class="progress mt-2"><div class="progress-bar bg-success" style="width: <?php echo $configs->calcPercent($weekProfitFinal, "week"); ?>%"></div></div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-value">$<?php echo $monthProfitFinal; ?></div>
            <div class="stat-label">This Month</div>
            <div class="progress mt-2"><div class="progress-bar" style="width: <?php echo $configs->calcPercent($monthProfitFinal, "month"); ?>%"></div></div>
        </div>
        <div class="admin-stat-card gradient-alt">
            <div class="stat-value">$<?php echo $totalProfitFinal; ?></div>
            <div class="stat-label">All Time</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-9">
            <div class="admin-graph-card">
                <div class="graph-label">App Sessions</div>
                <div class="graph-value"><?php echo $todaySessions; ?></div>
                <div class="graph-time"><?php echo date('d M Y, D, g:i A', $time); ?></div>
                <canvas id="sessionAanalyticsChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-3">
            <div class="admin-chart-card">
                <div class="section-title"><h4>Orders</h4></div>
                <div class="doughnut-wrapper">
                    <canvas id="ordersChart" class="chart" style="max-height:180px;"></canvas>
                    <div class="doughnut-label">
                        <strong><?php echo $totalRequests; ?></strong>
                        <span>Total</span>
                    </div>
                </div>
                <div class="chart-legends">
                    <div class="legend-item"><span class="legend-dot" style="background:var(--warning)"></span> Processing</div>
                    <div class="legend-item"><span class="legend-dot" style="background:var(--gray-300)"></span> Pending</div>
                    <div class="legend-item"><span class="legend-dot" style="background:var(--success)"></span> Completed</div>
                    <div class="legend-item"><span class="legend-dot" style="background:var(--danger)"></span> Rejected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="admin-table-card">
                <div class="card-header"><h5>Recently Registered Users</h5></div>
                <div class="table-responsive" style="max-height:250px;overflow-y:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr><th>No.</th><th>Fullname</th><th>Email</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                                $recent5users = $stats->getRecentAccounts();
                                $recent5users_loaded = count($recent5users['users']);
                                $recentUsersCounter = 1;
                                if ($recent5users_loaded != 0) {
                                    foreach ($recent5users['users'] as $key => $value) {
                                        drawRecentUsers($value, $recentUsersCounter);
                                        $recentUsersCounter++;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-table-card">
                <div class="card-header"><h5>Recent Requests</h5></div>
                <div class="table-responsive" style="max-height:250px;overflow-y:auto;">
                    <table class="admin-table">
                        <thead>
                            <tr><th>User</th><th>Gift Name</th><th>Amount</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php
                                $recent5Requests = $requests->recentRequests();
                                $recent5Requests_loaded = count($recent5Requests['requests']);
                                if ($recent5Requests_loaded != 0) {
                                    foreach ($recent5Requests['requests'] as $key => $value) {
                                        drawRequests($value);
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'inc/support.php'; ?>

</div><!-- /admin-content -->

<?php include_once 'inc/admin_footer.php'; ?>
<?php

    function drawRecentUsers($user,$counter)
    {
	?>
		<tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8'); ?></td>
			<?php if (!APP_DEMO) { ?><td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td><?php }else{ ?><td data-toggle="tooltip" data-original-title="Not Available in the Demo Version">xxxxx@xxxxx.xxx</td><?php } ?>
            <td><a href="user-details.php?id=<?php echo (int)$user['id']; ?>" class="btn btn-sm btn-primary">View</a></td>
        </tr>
	<?php
    }

    function drawRequests($request)
    {
	?>
		<tr>
            <td><?php echo htmlspecialchars($request['username'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($request['gift_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($request['req_amount'], ENT_QUOTES, 'UTF-8'); ?></td>
			<?php if($request['status'] == 1){ ?>
				<td><span class="badge badge-success">Completed</span></td>
			<?php }else if($request['status'] == 2){ ?>
				<td><span class="badge badge-warning">Processing</span></td>
			<?php }else if($request['status'] == 3){ ?>
				<td><span class="badge badge-danger">Rejected</span></td>
			<?php }else{ ?>
				<td><span class="badge badge-warning">Pending</span></td>
			<?php } ?>
        </tr>
	<?php
    }
?>
