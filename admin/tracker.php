<?php

    /*!
	 * FLY CASH v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

	$pagename = 'tracker';
	$container = '';
	
	include_once("inc/admin.inc.php");
	
	if(!empty($_GET)){
		$user = isset($_GET['user']) ? $_GET['user'] : '';
		$completed = new completed($dbo);
		$tracker = new tracker($dbo);
	}

    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>Tracking User's History</h4>
            <p>Enter a username to track their activity</p>
        </div>
    </div>

    <div class="admin-table-card">
        <div class="card-header"><h5>Enter UserName to Track</h5></div>
        <form class="form-inline" action="tracker.php" method="get" style="padding:1rem;">
            <div class="input-group" style="flex:1;">
                <span class="input-group-addon">@</span>
                <input class="form-control" placeholder="Username" name="user" type="text" />
            </div>
            <button class="btn btn-primary ml-2" type="submit">Submit</button>
        </form>
    </div>

    <div class="admin-table-card">
        <div class="card-header"><h5>User Redeem History</h5></div>
        <div class="table-responsive">
            <table id="dataTable1" class="display table table-striped admin-table" data-table="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Gift Name</th>
                        <th>Requested To</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Points Used</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($user)){
                    $result = $requests->getuserRequests($user);
                    $requests_loaded = count($result['requests']);
                    $counter = 1;
                    
                    if ($requests_loaded != 0) {
                        foreach ($result['requests'] as $key => $value) {
                            draw($value,$counter);
                            $counter ++;
                        }
                    }
                    
                    $result = $completed->getuserRequests($user);
                    $requests_loaded = count($result['requests']);
                    
                    if ($requests_loaded != 0) {
                        foreach ($result['requests'] as $key => $value) {
                            draw($value,$counter);
                            $counter ++;
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-table-card">
        <div class="card-header"><h5>User Earning History</h5></div>
        <div class="table-responsive">
            <table id="dataTable2" class="display table table-striped admin-table" data-table="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>User</th>
                        <th>Earned From</th>
                        <th>Date</th>
                        <th>Credited Points</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($user)){
                    $result = $tracker->getuserTrackerData($user);
                    $trackerdata_loaded = count($result['requests']);
                    $trackercounter = 1;
                    
                    if ($trackerdata_loaded != 0) {
                        foreach ($result['requests'] as $key => $value) {
                            drawTracker($value,$trackercounter);
                            $trackercounter ++;
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include_once 'inc/support.php'; ?>
</div><!-- /admin-content -->

<?php include_once 'inc/admin_footer.php'; ?>
<?php

	function draw($request,$counter)
    {
	?>
		<tr>
            <td class="text-left"><?php echo $counter; ?></td>
            <td><?php echo htmlspecialchars($request['username'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="price"><?php echo $request['req_amount']; ?></td>
            <td><?php echo htmlspecialchars($request['gift_name'], ENT_QUOTES, 'UTF-8'); ?></td>
			<?php if (!APP_DEMO) { ?><td><?php echo htmlspecialchars($request['request_from'], ENT_QUOTES, 'UTF-8'); ?></td><?php }else{ ?><td data-toggle="tooltip" data-original-title="Not Available in the Demo Version">xxxxxxxx</td><?php } ?>
			<?php if($request['status'] == 1){ ?>
				<td><span class="badge badge-pill bg-success">Completed</span></td><?php	
			}else if($request['status'] == 2){ ?>
				<td><span class="badge badge-pill bg-warning">Processing</span></td><?php	
			}else if($request['status'] == 3){ ?>
				<td><span class="badge badge-pill bg-danger">Rejected</span></td><?php	
			}else if($request['status'] == 0){ ?>
				<td><span class="badge badge-pill bg-warning">Pending</span></td><?php	
			}
			?>
            <td class="date"><?php $timestamp = strtotime($request['date']); echo date('d M Y, D',$timestamp); ?></td>
            <td class="price"><?php echo "- ".$request['points_used']; ?></td>
        </tr>
	<?php
    }

	function drawTracker($request,$trackercounter)
    {
	?>
		<tr>
            <td class="text-left"><?php echo $trackercounter; ?></td>
            <td><?php echo htmlspecialchars($request['username'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($request['type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="date"><?php $timestamp = strtotime($request['date']); echo date('d M Y, D',$timestamp); ?></td>
            <td class="price"><?php echo "+ ".$request['points']; ?></td>
        </tr>
	<?php
    }
?>
