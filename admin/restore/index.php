<?php
include_once("../core/init.inc.php");
	$configs = new functions($dbo);

    if (admin::isSession()) {

        header("Location: ../admin.php");
    }
	
	if (isset($_GET['hash'])) {

        $hash = isset($_GET['hash']) ? $_GET['hash'] : '';

        $hash = helper::clearText($hash);
        $hash = helper::escapeText($hash);

        $restorePointInfo = $helper->getRestorePoint($hash);

        if ($restorePointInfo['error'] !== false) {

            header("Location: /");
        }

    } else {

        header("Location: /");
    }

    $error = false;
    $error_message = array();

    $user_password = '';
    $user_password_repeat = '';

    if (!empty($_POST)) {

        $error = false;

        $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
        $user_password_repeat = isset($_POST['user_password_repeat']) ? $_POST['user_password_repeat'] : '';
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $user_password = trim($user_password);
        $user_password_repeat = trim($user_password_repeat);

        if (!hash_equals((string)helper::getAuthenticityToken(), (string)$token)) {

            $error = true;
            $error_message[] = 'Error!';
        }

        if (!helper::isCorrectPassword($user_password)) {

            $error = true;
            $error_message[] = 'Incorrect password.';
        }

        if ($user_password != $user_password_repeat) {

            $error = true;
            $error_message[] = 'Passwords do not match.';
        }

        if (!$error) {

            $account = new account($dbo, $restorePointInfo['accountId']);

            $account->newPassword($user_password);
            $account->restorePointRemove();

            header("Location: success/");
            exit;
        }
    }

    helper::newAuthenticityToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta content="ie=edge" http-equiv="x-ua-compatible" />
	<title><?php echo $configs->getConfig('APP_NAME'); ?> - <?php echo $configs->getConfig('APP_DESC'); ?></title>

    <!--Preloader-CSS-->
    <link rel="stylesheet" href="../assets/plugins/preloader/preloader.css" />

    <!--bootstrap-4-->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />

    <!--Custom Scroll-->
    <link rel="stylesheet" href="../assets/plugins/customScroll/jquery.mCustomScrollbar.min.css" />
    <!--Font Icons-->
    <link rel="stylesheet" href="../assets/icons/simple-line/css/simple-line-icons.css" />
    <link rel="stylesheet" href="../assets/icons/dripicons/dripicons.css" />
    <link rel="stylesheet" href="../assets/icons/ionicons/css/ionicons.min.css" />
    <link rel="stylesheet" href="../assets/icons/eightyshades/eightyshades.css" />
    <link rel="stylesheet" href="../assets/icons/fontawesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="../assets/icons/foundation/foundation-icons.css" />
    <link rel="stylesheet" href="../assets/icons/metrize/metrize.css" />
    <link rel="stylesheet" href="../assets/icons/typicons/typicons.min.css" />
    <link rel="stylesheet" href="../assets/icons/weathericons/css/weather-icons.min.css" />
    <!--Main Css-->
    <link rel="stylesheet" href="../assets/css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<body>

<section style="background: url(../assets/images/bg.jpg);background-size: cover">
    <div class="height-100-vh bg-primary-trans">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="login-div">
						
						<?php if ($error){ ?>
						
							<div class="alert alert-danger">
								<?php foreach ($error_message as $msg) { echo $msg . "<br />"; } ?>
							</div>
							
						<?php } ?>
						
                        <p class="logo mb-1">Change Password</p>
                        <p class="mb-4" style="color: #a5b5c5">Create a new password</p>
                        <form id="needs-validation" action="index.php?hash=<?php echo $hash; ?>" method="post" novalidate="" />
                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">
							<div class="form-group">
                                <label>New Password</label>
                                <input class="form-control input-lg" placeholder="New Password" maxlength="24" id="user_password" maxlength="20" name="user_password" type="password" value="" required="" />
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input class="form-control input-lg" autocomplete="off" placeholder="Confirm New Password" type="password" id="user_password_repeat" maxlength="20" name="user_password_repeat" required="" />
                                <div class="invalid-feedback">This field is required.</div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Change</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--Jquery-->
<script type="text/javascript" src="../assets/js/jquery-3.2.1.min.js"></script>
<!--Bootstrap Js-->
<script type="text/javascript" src="../assets/js/popper.min.js"></script>
<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<!--Modernizr Js-->
<script type="text/javascript" src="../assets/js/modernizr.custom.js"></script>

<!--Morphin Search JS-->
<script type="text/javascript" src="../assets/plugins/morphin-search/classie.js"></script>
<!--Morphin Search JS-->
<script type="text/javascript" src="../assets/plugins/preloader/pathLoader.js"></script>
<script type="text/javascript" src="../assets/plugins/preloader/preloader-main.js"></script>

<!--Chart js-->
<script type="text/javascript" src="../assets/plugins/charts/Chart.min.js"></script>

<!--Sparkline Chart Js-->
<script type="text/javascript" src="../assets/plugins/sparkline/jquery.sparkline.min.js"></script>
<script type="text/javascript" src="../assets/plugins/sparkline/jquery.charts-sparkline.js"></script>

<!--Custom Scroll-->
<script type="text/javascript" src="../assets/plugins/customScroll/jquery.mCustomScrollbar.min.js"></script>
<!--Sortable Js-->
<script type="text/javascript" src="../assets/plugins/sortable2/sortable.min.js"></script>
<!--DropZone Js-->
<script type="text/javascript" src="../assets/plugins/dropzone/dropzone.js"></script>
<!--Date Range JS-->
<script type="text/javascript" src="../assets/plugins/date-range/moment.min.js"></script>
<script type="text/javascript" src="../assets/plugins/date-range/daterangepicker.js"></script>
<!--CK Editor JS-->
<script type="text/javascript" src="../assets/plugins/ckEditor/ckeditor.js"></script>
<!--Data-Table JS-->
<script type="text/javascript" src="../assets/plugins/data-tables/datatables.min.js"></script>
<!--Editable JS-->
<script type="text/javascript" src="../assets/plugins/editable/editable.js"></script>
<!--Full Calendar JS-->
<script type="text/javascript" src="../assets/plugins/full-calendar/fullcalendar.min.js"></script>

<!--- Main JS -->
<script src="../assets/js/main.js"></script>

</body>
</html>