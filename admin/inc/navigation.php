<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */


$configsMain = new functions($dbo);

?>

<!--Navigation-->
<nav id="navigation" class="navigation-sidebar">
    <div class="navigation-header">
        <a href="admin.php"><span class="logo"><?php echo strtoupper($configsMain->getConfig('APP_NAME')); ?></span></a>
    </div>

    <!--Navigation Profile area-->
    <div class="navigation-profile">
        <span class="profile-initials"><?php echo strtoupper(substr($helper->getAdminFullName(admin::getAdminID()), 0, 1)); ?></span>
        <h4 class="name"><?php echo $helper->getAdminFullName(admin::getAdminID()); ?></h4>
        <span class="designation">ADMIN</span>
    </div>

    <!--Navigation Menu Links-->
    <div class="navigation-menu">

        <ul class="menu-items custom-scroll">
            <li>
                <a href="admin.php" <?php if($pagename == 'dashboard') { echo 'class="active"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-browser"></i></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="users.php" <?php if($pagename == 'users') { echo 'class="active"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-user-group"></i></span>
                    <span class="title">All Users</span>
                </a>
            </li>
			
            <li>
                <a href="javascript:void(0);" <?php if($container == 'redeem-requests') { echo 'class="have-submenu active"'; }else{ echo 'class="have-submenu"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-list"></i></span>
                    <span class="title">Records</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="requests.php" <?php if($pagename == 'pending-requests') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Pending</span>
                        </a>
                    </li>
                    <li>
                        <a href="processing-requests.php" <?php if($pagename == 'processing-requests') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Processing</span>
                        </a>
                    </li>
                    <li>
                        <a href="completed.php" <?php if($pagename == 'completed-requests') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Completed</span>
                        </a>
                    </li>
                    <li>
                        <a href="rejected-requests.php" <?php if($pagename == 'rejected-requests') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Rejected</span>
                        </a>
                    </li>
                </ul>
            </li>
            
			
            <li>
                <a href="javascript:void(0);" <?php if($container == 'offerwalls') { echo 'class="have-submenu active"'; }else{ echo 'class="have-submenu"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-list"></i></span>
                    <span class="title">OfferWalls</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="offerwalls.php" <?php if($pagename == 'offerwalls') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">All OfferWalls</span>
                        </a>
                    </li>
                    <li>
                        <a href="add-offerwall.php" <?php if($pagename == 'add-offerwall') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Add Offerwall</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:void(0);" <?php if($container == 'youtube-offers') { echo 'class="have-submenu active"'; }else{ echo 'class="have-submenu"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-list"></i></span>
                    <span class="title">Youtube Offers</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="youtube-offers.php" <?php if($pagename == 'youtube-offers') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">All Youtube Offers</span>
                        </a>
                    </li>
                    <li>
                        <a href="add-youtube-offer.php" <?php if($pagename == 'add-youtube-offer') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Add Youtube Offer</span>
                        </a>
                    </li>
                </ul>
            </li>
			
            <li>
                <a href="javascript:void(0);" <?php if($container == 'payouts') { echo 'class="have-submenu active"'; }else{ echo 'class="have-submenu"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-list"></i></span>
                    <span class="title">Payouts</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="payouts.php" <?php if($pagename == 'payouts') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Redeem Options</span>
                        </a>
                    </li>
                    <li>
                        <a href="add-payout.php" <?php if($pagename == 'add-payout') { echo 'class="active"'; } ?>>
                            <span class="icon-thumbnail"  style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
                            <span class="title">Add Redeem</span>
                        </a>
                    </li>
                </ul>
            </li>
			
            <li>
                <a href="javascript:void(0);" <?php if($container == 'settings') { echo 'class="have-submenu active"'; }else{ echo 'class="have-submenu"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-gear"></i></span>
                    <span class="title">Settings</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
					<li>
						<a href="profile.php" <?php if($pagename == 'admin-profile') { echo 'class="active"'; } ?>>
							<span class="icon-thumbnail" style="margin-right: 5px;"><i class="ion-ios-person-outline"></i></span>
							<span class="title">Admin Profile</span>
						</a>
					</li>
					<li>
						<a href="settings.php" <?php if($pagename == 'configuration') { echo 'class="active"'; } ?>>
							<span class="icon-thumbnail" style="margin-right: 5px;"><i class="ion-ios-locked-outline"></i></span>
							<span class="title">Configuration</span>
						</a>
					</li>
					<li>
						<a href="postbacks.php" <?php if($pagename == 'postbacks') { echo 'class="active"'; } ?>>
							<span class="icon-thumbnail" style="margin-right: 5px;"><i class="ion-ios-bolt-outline"></i></span>
							<span class="title">Postbacks S2S</span>
						</a>
					</li>
                </ul>
            </li>
            
            <li>
                <a href="push.php" <?php if($pagename == 'push-single') { echo 'class="active"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-broadcast"></i></span>
                    <span class="title">Send Push</span>
                </a>
            </li>
            
            <li>
                <a href="tracker.php" <?php if($pagename == 'tracker') { echo 'class="active"'; } ?>>
                    <span class="icon-thumbnail"><i class="dripicons-graph-line"></i></span>
                    <span class="title">Tracker</span>
                </a>
            </li>
			
            <li>
                <a href="logout.php/?access_token=<?php echo admin::getAccessToken(); ?>">
                    <span class="icon-thumbnail"><i class="dripicons-exit"></i></span>
                    <span class="title">Logout</span>
                </a>
            </li>
			
        </ul>
    </div>
</nav>
<!--Navigation-->

<!--
Template : FLY CASH - Money Making Script
Author: AYM
Website: http://www.aym.com/
Contact: support@aym.com
Support: http://www.aym.com/support

Purchase from Admin Panel [CodyHub]: https://www.codyhub.com/item/pocket-admin/

Purchase from Web Version [CodyHub]: https://www.codyhub.com/item/web-rewards-app-pocket/
Purchase from Android Version [CodyHub]: https://www.codyhub.com/item/android-rewards-app-pocket/
Purchase from IOS Version [CodyHub]: https://www.codyhub.com/item/ios-rewards-app-pocket/

Purchase from Web Version [Codecanyon]: https://codecanyon.net/item/web-rewards-app-pocket/25104366
Purchase from Android Version [Codecanyon]: https://codecanyon.net/item/android-rewards-app-pocket/17413949
Purchase from IOS Version [Codecanyon]: https://codecanyon.net/item/ios-rewards-app-pocket/24811671

License: You must have a valid license purchased only from codyhub or codecanyon (the above links) in order to legally use this product.
-->

