<?php
$pagename = 'pending-requests';
	$container = 'redeem-requests';
	
	include_once("inc/admin.inc.php");
	
    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>Pending Requests</h4>
            <p>Showing All Requests from the Users. You can Proccess the Request, Reject the Request (or) Search for the Request.</p>
        </div>
    </div>

    <div class="admin-table-card">
        <div class="table-responsive">
            <table id="dataTable1" class="display table table-striped admin-table" data-table="data-table">
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
                    $result = $requests->getRequests(0, 0, 0, 'pending');
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

    <!----- Requests Modal ----->
    <div aria-hidden="true" aria-labelledby="requestsModel" class="modal fade" id="requestsModel" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalTitle">Add Some Note ?</h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form name="form_request" id="form_request" action="process/complete.php" method="post" role="form" data-toggle="validator">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input id="requestId" type="text" value="" name="id" hidden>
                        <input id="requestType" type="text" value="" name="type" hidden>
                        <div class="form-group">
                            <label>A Note for the User</label>
                            <textarea id="requestNote" class="form-control" placeholder="" name="note" type="text" maxlength="100" required="true"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
                    <button id="requestModalBtn" class="btn btn-primary" onclick="doRequest()" type="submit" value="submit">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'inc/support.php'; ?>
</div><!-- /admin-content -->

<script>
    function doRequest() {
        document.getElementById("form_request").submit();
    }

    function processRequest(type, rid){
        document.getElementById("requestId").value = rid;
        document.getElementById("requestType").value = type;
        
        var requestNotePlaceHolder = 'This is a fraud request';
        var requestModalTitle = 'Add Some Note ?';
        var requestModalBtn = 'OK';
        
        if(type == 'complete'){
            requestNotePlaceHolder = "Payment Reference like TransactionId or something";
            requestModalTitle = 'Request Processed ?'
            requestModalBtn = "Mark as Completed";
        }else if(type == 'process'){
            requestNotePlaceHolder = "you'll get your reward within 2 days";
            requestModalTitle = 'Move Request to Processing ?'
            requestModalBtn = "Mark as Processing";
        }else if(type == 'reject'){
            requestNotePlaceHolder = "We do not allow fake referrals";
            requestModalTitle = 'Reject this Request ?'
            requestModalBtn = "Mark as Rejected";
        }
        
        $('#requestNote').attr('placeholder',requestNotePlaceHolder);
        document.getElementById("requestModalTitle").innerHTML = requestModalTitle;
        document.getElementById("requestModalBtn").innerHTML = requestModalBtn;
        
        $('#requestsModel').modal('show');
    }
</script>

<?php include_once 'inc/admin_footer.php'; ?>
<?php

	function draw($request)
    {
	?>
		<tr>
            <td class="text-left"><?php echo $request['rid']; ?></td>
            <td><?php echo htmlspecialchars($request['username'], ENT_QUOTES, 'UTF-8'); ?></td>
			<?php if (!APP_DEMO) { ?><td><?php echo htmlspecialchars($request['request_from'], ENT_QUOTES, 'UTF-8'); ?></td><?php }else{ ?><td data-toggle="tooltip" data-original-title="Not Available in the Demo Version">xxxxxxxx</td><?php } ?>
            <td><?php echo htmlspecialchars($request['gift_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="price"><?php echo $request['req_amount']; ?></td>
            <td class="price"><?php echo $request['points_used']; ?></td>
            <td><?php echo htmlspecialchars($request['dev_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($request['dev_man'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="date"><?php $timestamp = strtotime($request['date']); echo date('d M Y, D',$timestamp); ?></td>
            <td>
                <a href="tracker.php?user=<?php echo urlencode($request['username']); ?>" class="btn btn-primary btn-small">Track</a>
                <a href="#" onclick="processRequest('process','<?php echo $request['rid']; ?>')" class="btn btn-warning btn-small">Processing</a>
                <a href="#" onclick="processRequest('reject','<?php echo $request['rid']; ?>')" class="btn btn-danger btn-small">Reject</a>
                <a href="#" onclick="processRequest('complete','<?php echo $request['rid']; ?>')" class="btn btn-success btn-small">Complete</a>
            </td>
        </tr>
	<?php
    }
?>
