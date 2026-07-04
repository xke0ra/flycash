<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

?>
<!--Footer Fixed-->
        <div class="footer" style="background-color: #fff; bottom: 0; left: 0; align-items: center; display: flex; justify-content: space-between; width: 100%; border-top: 1px solid var(--light-color);">
            <div class="container-fluid" style="padding: 10px 25px">
                <div class="row">
                    <div class="col-9 col-md-6 col-lg-8" style="text-align:  left;">
						FLY CASH - Designed &amp; Developed by <a href="https://www.aym.com" target="_blank">AYM</a>
					</div>
                    <div class="col-3 col-md-6 col-lg-4" style="text-align:  right;">
						<a href="https://www.codyhub.com/item/pocket-admin" target="_blank">v<?php $configs = isset($configs) ? $configs : new functions($dbo); echo $configs->getConfig('VERSION'); ?></a>
                    </div>
                </div>
            </div>
        </div>
