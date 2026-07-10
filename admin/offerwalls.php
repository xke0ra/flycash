<?php
$pagename = 'offerwalls';
	$container = 'offerwalls';
	
	include_once("inc/admin.inc.php");
	
	$offerwalls = new offerwalls($dbo);

    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>OfferWalls Options</h4>
            <p>Manage offerwall integrations</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="admin-table-card">
                <div class="card-header"><h5>Active OfferWalls</h5></div>
                <ul id="todo" style="list-style:none;padding:0;">
                    <?php
                        $result = $offerwalls->getOfferwalls(0);
                        $offerwalls_loaded = count($result['offerwalls']);
                        
                        if ($offerwalls_loaded != 0) {
                            foreach ($result['offerwalls'] as $key => $value) {
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
                        if ($offerwalls_loaded != 0) {
                            foreach ($result['offerwalls'] as $key => $value) {
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

    function draw($offerwall)
    {
        if($offerwall['offer_status'] == 'Active'){
	?>
		<li style="padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:1rem;">
			<img src="images/<?php echo htmlspecialchars($offerwall['offer_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" style="width:50px;height:50px;border-radius:8px;object-fit:cover;" />
			<div style="flex:1;">
				<strong><?php echo htmlspecialchars($offerwall['offer_title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
				<small><?php echo htmlspecialchars($offerwall['offer_subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
			</div>
			<div style="display:flex;gap:0.5rem;">
				<a href="process/featured.php?type=<?php echo urlencode($offerwall['offer_type']); ?>&action=0" class="btn btn-sm btn-danger">Deactivate</a>
				<a href="edit-featured.php?id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
			</div>
		</li>
	<?php
        }
    }
    
    function drawDeactives($offerwall)
    {
        if($offerwall['offer_status'] != 'Active'){
	?>
		<li style="padding:0.75rem 1rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:1rem;">
			<img src="images/<?php echo htmlspecialchars($offerwall['offer_thumbnail'], ENT_QUOTES, 'UTF-8'); ?>" style="width:50px;height:50px;border-radius:8px;object-fit:cover;" />
			<div style="flex:1;">
				<strong><?php echo htmlspecialchars($offerwall['offer_title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
				<small><?php echo htmlspecialchars($offerwall['offer_subtitle'], ENT_QUOTES, 'UTF-8'); ?></small>
			</div>
			<div style="display:flex;gap:0.5rem;">
				<a href="process/featured.php?type=<?php echo urlencode($offerwall['offer_type']); ?>&action=1" class="btn btn-sm btn-primary">Activate</a>
				<a href="edit-featured.php?id=<?php echo (int)$offerwall['offer_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
			</div>
		</li>
	<?php
        }
    }
?>
