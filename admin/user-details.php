<?php
$pagename = '';
	$container = '';
	
	include_once("inc/admin.inc.php");

    $accountInfo = array();

    if (isset($_GET['id'])) {

        $accountId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $accountId = helper::clearInt($accountId);

        $account = new account($dbo, $accountId);
        $accountInfo = $account->get();

        if (empty($accountInfo) || !isset($accountInfo['id'])) {
            $accountInfo = array();
        }

        $accountInfo = array_merge([
            'id' => 0,
            'fullname' => 'Unknown',
            'username' => 'unknown',
            'email' => '',
            'points' => 0,
            'regtime' => time(),
            'state' => 0,
            'ip_addr' => '',
        ], $accountInfo);

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            switch ($act) {

                case "close": {

                    $auth->removeAll($accountId);

                    header("Location: user-details.php?id=".$accountInfo['id']);
                    break;
                }

                case "block": {

                    $account->setState(ACCOUNT_STATE_BLOCKED);

                    header("Location: user-details.php?id=".$accountInfo['id']);
                    break;
                }

                case "rem": {

                    $account->remAccount($accountId);

                    header("Location: users.php");
                    break;
                }

                case "unblock": {

                    $account->setState(ACCOUNT_STATE_ENABLED);

                    header("Location: user-details.php?id=".$accountInfo['id']);
                    break;
                }

                default: {

                    header("Location: user-details.php?id=".$accountInfo['id']);
                    exit;
                }
            }
        }

    } else {

        header("Location: users.php");
    }

    if ($accountInfo['error'] === true) {

        header("Location: users.php");
    }

    $stats = new stats($dbo);

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Account info</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
					<div class="col-md-4">
                        <div class="block mb-4" style="box-shadow: 0 7px 15px var(--primary-alpha-Dot25); transition: all 0.3s;">
							<div class="user-profile-menu bg-white">
								<div class="avatar-info">
									<img class="profile-img rounded-circle" align="middle" src="assets/images/person-placeholder.png" alt="profile image" />
									<h4 class="name"><?php echo htmlspecialchars($accountInfo['fullname'], ENT_QUOTES, 'UTF-8'); ?></h4>
									<p class="designation"><?php echo "Member Since : ". date("d M Y", (int)$accountInfo['regtime']); ?></p>
								</div>
							</div>
							
						</div>
					</div>
					
                    <div class="col-md-8">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Account info</h5>
                            </div>
								
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Account state</label>
                                        <div class="col-md-9 price">
                                            <?php

                                            if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                echo '<span class="badge badge-pill bg-success">Account is Active</span>';

                                            } else {
												
                                                echo '<span class="badge badge-pill bg-danger">Account is blocked</span>';
                                            }
                                        ?>
                                        </div>
                                    </div>
                                </div>

                            <form action="" class="horizontal-form" />
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Username</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" value="<?php echo htmlspecialchars($accountInfo['username'], ENT_QUOTES, 'UTF-8'); ?>" disabled/>
                                        </div>
                                    </div>
                                </div>
								
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">email</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" value="<?php if (!APP_DEMO) { echo htmlspecialchars($accountInfo['email'], ENT_QUOTES, 'UTF-8'); }else{ echo "xxxxx@xxxxx.xxx"; } ?>" disabled/>
                                        </div>
                                    </div>
                                </div>
								
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Points Balance</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" value="<?php echo (int)$accountInfo['points']; ?>" disabled/>
                                        </div>
                                    </div>
                                </div>
								
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">SignUp Ip address</label>
                                        <div class="col-md-9">
                                            <input class="form-control" type="text" value="<?php if (!APP_DEMO) { echo htmlspecialchars($accountInfo['ip_addr'], ENT_QUOTES, 'UTF-8'); }else{ echo "xxx.xxx.xxx.xx"; } ?>" disabled/>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                            </form>
                                <div class="ticket_controls">
                                    <?php

                                        if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                            ?>
                                                <a class="btn btn-danger" style="float: right; margin-top: 2%;" href="user-details.php?id=<?php echo (int)$accountInfo['id']; ?>&access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>&act=block">Block account</a>
                                            <?php

                                        } else {

                                            ?>
                                                <a class="btn btn-warning" style="float: right; margin-top: 2%;" href="user-details.php?id=<?php echo (int)$accountInfo['id']; ?>&access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>&act=unblock">Unblock account</a>
                                            <?php
                                        }
                                    ?>

                                    <a class="btn btn-primary" style="margin-top: 2%;" href="user-details.php?id=<?php echo (int)$accountInfo['id']; ?>&access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>&act=close">Close all authorizations</a>
                                   

                                    <button class="btn btn-success" style="margin-top: 2%;" data-target="#addPoints" data-toggle="modal">Add Points</button>
                                    
                                    <a class="btn btn-danger" style="float: right; margin-right: 2.5%; margin-top: 2%;" href="user-details.php?id=<?php echo (int)$accountInfo['id']; ?>&access_token=<?php echo htmlspecialchars(admin::getAccessToken(), ENT_QUOTES, 'UTF-8'); ?>&act=rem">Delete User</a>
                                </div>
                                <!----- Add Points Modal ----->
                                <div aria-hidden="true" aria-labelledby="addPoints" class="modal fade" id="addPoints" role="dialog" tabindex="-1">
    								<div class="modal-dialog modal-dialog-centered" role="document">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title">Add Points to <?php echo htmlspecialchars($accountInfo['fullname'], ENT_QUOTES, 'UTF-8'); ?></h5>
    											<button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true"> &times;</span></button>
    										</div>
    										<div class="modal-body">
<form name="form_add_points" id="form_add_points" action="process/add-points.php" method="post" role="form" data-toggle="validator">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="text" value="<?php echo (int)$accountInfo['id']; ?>" name="id" hidden>
    												<input type="text" value="<?php echo htmlspecialchars($accountInfo['username'], ENT_QUOTES, 'UTF-8'); ?>" name="user" hidden>
    												<div class="form-group">
    													<label> Points to Add</label>
    													<input class="form-control" placeholder="50" type="number" name="points_to_add" required="true" required>
    												</div>
    												<div class="form-group">
    													<label> Reason for Adding Points</label>
    													<input class="form-control" placeholder="Manual Admin Credit" name="reason_for_adding_points" type="text" maxlength="60" required="true">
    												</div>
    											</form>
    										</div>
    										<div class="modal-footer">
    											<button class="btn btn-secondary" data-dismiss="modal" type="button"> Close</button>
    											<button class="btn btn-primary" onclick="add_points()" type="submit" value="submit"> Add Points</button>
    										</div>
    									</div>
    								</div>
								</div>
	
                                
                        </div>
                    </div>
                    
                    
					<!-- END MAIN CONTENT HERE -->
					<?php include_once 'inc/support.php'; ?>
					
                </div>
            </div>
        </div>
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>

<!-- Notify JS-->
<script type="text/javascript" src="./assets/js/bootstrap-notify.min.js"></script>

<script>
    function add_points() {
        document.getElementById("form_add_points").submit();
   }
   
   <?php  $_SESSION["points_added"] = isset($_SESSION["points_added"]) ? $_SESSION["points_added"] : '0';
   
   if($_SESSION["points_added"] == 1){ $_SESSION["points_added"] = 0; ?>
   
   $.notify({
	    // options
	    message: ' Points Added Successfully',
	    icon: 'dripicons-checkmark'
	    
    },{
    	// settings
    	type: 'success'
    });
       
   <?php }else if($_SESSION["points_added"] == 2){ $_SESSION["points_added"] = 0; ?>
   
   $.notify({
	    // options
	    title: ' Points Not Added',
	    message: ' There was some issue for adding points, Try Again',
	    icon: 'dripicons-cross'
	    
    },{
    	// settings
    	timer: 10,
		enter: 'animated fadeInDown',
		exit: 'animated fadeOutUp',
    	type: 'danger'
    });
       
   <?php } ?>
   
</script>
