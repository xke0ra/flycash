<?php

    /*!
	 * FLY CASH v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

?>
<footer class="modern-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> <a href="../" target="_blank"><?php echo esc_attr($APP_NAME); ?></a> â€” All rights reserved.
            </div>
            <div class="footer-links">
                <a href="<?php echo esc_attr($configs->getConfig('APP_TERMS_URL')); ?>" target="_blank">Terms</a>
                <a href="<?php echo esc_attr($configs->getConfig('APP_POLICY_URL')); ?>" target="_blank">Privacy</a>
                <a href="<?php echo esc_attr($configs->getConfig('APP_CONTACT_US_URL')); ?>" target="_blank">Contact</a>
            </div>
        </div>
    </div>
</footer>
