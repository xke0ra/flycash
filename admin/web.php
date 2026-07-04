<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

	$pagename = '';
	$container = '';

	include_once("inc/admin.inc.php");

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
    <div class="content sm-gutter">
        <div class="container-fluid padding-25 sm-padding-10">
            <div class="row">
                <div class="col-12">
                    <div class="block form-block mb-4">
                        <div class="block-heading">
                            <h5>Web Panel Version</h5>
                        </div>
                        <p><?php echo htmlspecialchars("web Panel version - v3.7", ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /admin-content -->
<?php include_once 'inc/admin_footer.php'; ?>
