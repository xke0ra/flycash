<?php
$pagename = 'users';
	$container = '';
	
	include_once("inc/admin.inc.php");
	
    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>All Users</h4>
            <p>Showing All Users. You can Search for any User by using any user data like name, email or even user mobile.</p>
        </div>
    </div>

    <div class="admin-table-card">
        <div class="table-responsive">
            <table id="dataTable1" class="display table table-striped admin-table" data-table="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Account state</th>
                        <th>Points Bal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $result = $stats->getAccounts(0);
                    $users_loaded = count($result['users']);
                    
                    if ($users_loaded != 0) {
                        foreach ($result['users'] as $key => $value) {
                            draw($value);
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

    function draw($user)
    {
	?>
		<tr>
            <td class="text-left"><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8'); ?></td>
			<?php if (!APP_DEMO) { ?><td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td><?php }else{ ?><td data-toggle="tooltip" data-original-title="Not Available in the Demo Version">xxxxx@xxxxx.xxx</td><?php } ?>
            <td><?php if ($user['state'] == 0) {echo '<span class="badge badge-pill bg-success">Enabled</span>';} else {echo '<span class="badge badge-pill bg-danger">Blocked</span>';} ?></td>
            <td class="price"><?php echo $user['points']; ?></td>
            <td><a href="user-details.php?id=<?php echo (int)$user['id']; ?>" class="btn btn-primary btn-small">View</a> <a href="tracker.php?user=<?php echo urlencode($user['username']); ?>" class="btn btn-info btn-small">Track</a></td>
        </tr>
	<?php
    }
?>
