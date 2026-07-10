<?php
$pagename = 'push-single';
	$container = 'push';
	
	include_once("inc/admin.inc.php");

    $stats = new stats($dbo);

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Overview</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
					<div class="col-md-6">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Text Notication</h5>
                            </div>

                            <form action="process/pushnotify.php" method="post" />
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <label>Select User</label>
                                    <select class="custom-select form-control" name="fcm" required="">
                                        <option selected="" value="null" disabled>Select User</option>
										<?php 
										
											$result = $stats->getAccounts(0);
											$users_loaded = count($result['users']);
											
											if ($users_loaded != 0) {
												
												foreach ($result['users'] as $key => $value) {
													draw($value);
												}
											}
											
										?>
                                    </select>
                                </div>
								
                                <div class="form-group">
                                    <input class="form-control" placeholder="Image" type="text" name="img" value="none" hidden/>
                                </div>
								
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" placeholder="Title" type="text" name="title" required=""/>
                                </div>

                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" placeholder="Message" name="msg" rows="3" required=""></textarea>
                                </div>

                                <hr />
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
					
					<div class="col-md-6">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Image Notication</h5>
                            </div>

                            <form action="process/pushnotify.php" method="post" />
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <label>Select User</label>
                                    <select class="custom-select form-control" name="fcm" required="">
                                        <option selected="" value="nulll" disabled>Select User</option>
										<?php 
										
											$result = $stats->getAccounts(0);
											$users_loaded = count($result['users']);
											
											if ($users_loaded != 0) {
												
												foreach ($result['users'] as $key => $value) {
													draw($value);
												}
											}
											
										?>
                                    </select>
                                </div>
								
                                <div class="form-group">
                                    <label>Image url</label>
									<div class="input-group">
										<span class="input-group-addon text-dark"><i class="ion-ios-calendar-outline"></i></span>
										<input class="form-control" type="text" name="img" placeholder="Image URL" required=""/>
									</div>
                                </div>
								
                                <div class="form-group">
                                    <label>Title</label>
                                    <input class="form-control" placeholder="Title" name="title" type="text" required=""/>
                                </div>

                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control" placeholder="Message" name="msg" rows="3" required=""></textarea>
                                </div>

                                <hr />
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
					
					
					<!-- END MAIN CONTENT HERE -->
					<?php include_once 'inc/support.php'; ?>
					
                </div>
            </div>
        </div>
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>
<?php

    function draw($user)
    {
	?>
                                        <option value="<?php echo htmlspecialchars($user['gcm'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8'); ?></option>
	<?php
    }
?>
