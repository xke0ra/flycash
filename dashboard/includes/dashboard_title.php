<?php
?>
	    
	    <!-- Favicon -->
		<link rel="shortcut icon" href="../admin/assets/images/<?php echo $configs->getConfig('SITE_FAVICON'); ?>" />

		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo $APP_NAME; ?> | <?php echo $APP_DESC; ?></title>
		<meta name="description" content="<?php echo $APP_DESC; ?>">
		<meta name="theme-color" content="#6366f1">
		<meta name="csrf-token" content="<?php echo helper::getAuthenticityToken(); ?>">