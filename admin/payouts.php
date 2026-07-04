<?php

    /*!
	 * FLY CASH v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

	$pagename = 'payouts';
	$container = 'payouts';
	
	include_once("inc/admin.inc.php");
	
	$payouts = new redeem($dbo);

    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>Redeem Options</h4>
            <p>Manage payout methods</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="admin-table-card">
                <div class="card-header"><h5>Active Redeem Options</h5></div>
                <ul id="todo" style="list-style:none;padding:0;">
                    <?php
                        $result = $payouts->getPayouts(0);
                        $payouts_loaded = count($result['payouts']);
                        
                        if ($payouts_loaded != 0) {
                            foreach ($result['payouts'] as $key => $value) {
                                draw($value);
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="admin-table-card">
                <div class="card-header"><h5>Deactivated</h5></div>
                <ul id="inprogress" style="list-style:none;padding:0;">
                    <?php
                        if ($payouts_loaded != 0) {
                            foreach ($result['payouts'] as $key => $value) {
                                drawDeactives($value);
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <?php include_once 'inc/support.php'; ?>
</div><!-- /admin-content -->

<?php include_once 'inc/admin_footer.php'; ?>
<?php

    function draw($payout)
    {
        if($payout['payout_status'] == 'Active'){
	?>
		<li style="padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:1rem;">
			<img src="images/<?php echo htmlspecialchars($payout['payout_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" style="width:50px;height:50px;border-radius:8px;object-fit:cover;" />
			<div style="flex:1;">
				<strong><?php echo htmlspecialchars($payout['payout_title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
				<small><?php echo htmlspecialchars($payout['payout_subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
			</div>
			<div style="display:flex;gap:0.5rem;">
				<a href="process/redeem.php?id=<?php echo (int)$payout['payout_id']; ?>&action=0" class="btn btn-sm btn-danger">Deactivate</a>
				<a href="edit-payout.php?id=<?php echo (int)$payout['payout_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
			</div>
		</li>
	<?php
        }
    }
    
    function drawDeactives($payout)
    {
        if($payout['payout_status'] != 'Active'){
	?>
		<li style="padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:1rem;">
			<img src="images/<?php echo htmlspecialchars($payout['payout_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" style="width:50px;height:50px;border-radius:8px;object-fit:cover;" />
			<div style="flex:1;">
				<strong><?php echo htmlspecialchars($payout['payout_title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
				<small><?php echo htmlspecialchars($payout['payout_subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
			</div>
			<div style="display:flex;gap:0.5rem;">
				<a href="process/redeem.php?id=<?php echo (int)$payout['payout_id']; ?>&action=1" class="btn btn-sm btn-primary">Activate</a>
				<a href="edit-payout.php?id=<?php echo (int)$payout['payout_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
			</div>
		</li>
	<?php
        }
    }
?>
