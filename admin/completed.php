<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */


	$pagename = 'completed-requests';
	$container = 'redeem-requests';
	
	include_once("inc/admin.inc.php");
	
    $completed = new completed($dbo);
    
    include_once 'inc/admin_header.php'; ?>
<div class="admin-content">

        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Completed Records</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					<div class="col-12">
                        <div class="block bg-white table-block mb-4">
                            <div class="block-heading">
                                <h5>Completed Records</h5>
                                <p class="mt-2">Showing All Completed Records. You can Delete a Record (or) Search for a Record.</p>
                            </div>

                            <div class="row">
                                <div class="table-responsive">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>User</th>
                                            <th>Requested To</th>
                                            <th>Gift Name</th>
                                            <th>Amount</th>
                                            <th>Points Used</th>
                                            <th>Device Name</th>
                                            <th>Model No.</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
										
										<?php
										
											$result = $completed->getRequests(0);
											$requests_loaded = count($result['requests']);
											
											if ($requests_loaded != 0) {
												
												foreach ($result['requests'] as $key => $value) {
													draw($value);
												}
											}
										?>
										
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
					
					
					<!-- END MAIN CONTENT HERE -->
					<?php include_once 'inc/support.php'; ?>
					
                </div>
            </div>
        </div>
    </div>
	
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>
<?php

	function draw($request)
    {
	?>
		<tr>
            <td class="text-left"><?php echo $request['rid']; ?></td>
            <td><?php echo $request['username']; ?></td>
			<?php if (!APP_DEMO) { ?><td><?php echo $request['request_from']; ?></td><?php }else{ ?><td data-toggle="tooltip" data-original-title="Not Available in the Demo Version">xxxxxxxx</td><?php } ?>
            <td><?php echo $request['gift_name']; ?></td>
            <td class="price"><?php echo $request['req_amount']; ?></td>
            <td class="price"><?php echo $request['points_used']; ?></td>
            <td><?php echo $request['dev_name']; ?></td>
            <td><?php echo $request['dev_man']; ?></td>
            <td class="date"><?php $timestamp = strtotime($request['date']); echo date('d M Y, D',$timestamp); ?></td>
            <td><a href="tracker.php?user=<?php echo $request['username']; ?>" class="btn btn-primary btn-small"><i class="dripicons-graph-line"></i>Track</a></td>
        </tr>
	<?php
    }
?>