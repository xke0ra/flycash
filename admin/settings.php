<?php
$pagename = 'configuration';
	$container = 'settings';
	
	include_once("inc/admin.inc.php");
	
	$url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	
    include_once 'inc/admin_header.php';
?>

<div class="admin-content">

    <div class="admin-page-header">
        <div>
            <h4>Configuration Settings</h4>
            <p>Manage your application configuration</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="admin-table-card">
                <div class="card-header"><h5>Application Configuration</h5></div>
                <form action="process/settings.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Application Name</label>
                            <div class="col-md-9">
                                <input name="appname" class="form-control" placeholder="App Name" value="<?php echo $configs->getConfig('APP_NAME'); ?>" type="text" autocomplete="off" required="" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Application TagLine</label>
                            <div class="col-md-9">
                                <input name="tagline" class="form-control" placeholder="App TagLine" value="<?php echo $configs->getConfig('APP_DESC'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Support Email</label>
                            <div class="col-md-9">
                                <input class="form-control" name="support_email" placeholder="Email Address" value="<?php echo $configs->getConfig('SUPPORT_EMAIL'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Web ROOT</label>
                            <div class="col-md-9">
                                <input class="form-control" name="webpanel_url" placeholder="WebPanel URL" value="<?php echo dirname(dirname($url))."/"; ?>" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">PRIVACY POLICY URL</label>
                            <div class="col-md-9">
                                <input class="form-control" name="policy_url" placeholder="Policy URL" value="<?php echo $configs->getConfig('APP_POLICY_URL'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">TERMS URL</label>
                            <div class="col-md-9">
                                <input class="form-control" name="terms_url" placeholder="Terms URL" value="<?php echo $configs->getConfig('APP_TERMS_URL'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">CONTACT URL</label>
                            <div class="col-md-9">
                                <input class="form-control" name="contact_url" placeholder="ContactUs URL" value="<?php echo $configs->getConfig('APP_CONTACT_US_URL'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Company Details</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Company Name</label>
                            <div class="col-md-9">
                                <input class="form-control" name="company_name" placeholder="Company Name" value="<?php echo $configs->getConfig('COMPANY_NAME'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Company Website</label>
                            <div class="col-md-9">
                                <input class="form-control" name="company_site" placeholder="Company Website URL" value="<?php echo $configs->getConfig('COMPANY_SITE'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Phone No.</label>
                            <div class="col-md-9">
                                <input class="form-control" name="company_phone" placeholder="Phone No." value="<?php echo $configs->getConfig('SUPPORT_PHONE'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Company Email</label>
                            <div class="col-md-9">
                                <input class="form-control" name="company_email" placeholder="Email Address" value="<?php echo $configs->getConfig('COMPANY_EMAIL'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Country</label>
                            <div class="col-md-9">
                                <input class="form-control" name="country" placeholder="Country" value="<?php echo $configs->getConfig('COMPANY_COUNTRY'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading"><span data-original-title="All Calculations are Approximate only, original Income may vary" data-placement="top" data-toggle="tooltip">Profit Overview</span></h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3" data-original-title="Show Income Overview on Dashboard" data-placement="top" data-toggle="tooltip">Income Overview</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="income_activate" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('INCOME_OVERVIEW') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="income_activate" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('INCOME_OVERVIEW') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Admin Ratio</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark" data-original-title="$1 USD = <?php echo $configs->getConfig('ADMIN_RATIO'); ?> Points for Admin" data-placement="top" data-toggle="tooltip">$1 USD = </span>
                                    <input class="form-control" type="text" name="admin_ratio" value="<?php echo $configs->getConfig('ADMIN_RATIO'); ?>" placeholder="1000 Points" autocomplete="off" required=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">User Ratio</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark" data-original-title="$1 USD = <?php echo $configs->getConfig('USER_RATIO'); ?> Points for Users" data-placement="top" data-toggle="tooltip">$1 USD = </span>
                                    <input class="form-control" type="text" value="<?php echo $configs->getConfig('USER_RATIO'); ?>" placeholder="1000 Points" autocomplete="off" required="" style="background: #e9ecef; "disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Transactions Id's</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Transactions Prefix</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark"><i class="ion-ios-list-outline"></i></span>
                                    <input class="form-control" type="text" name="tn_prefix" value="<?php echo $configs->getConfig('TRANSACTION_PREFIX'); ?>" placeholder="PCKT" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Credit Prefix</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark"><i class="ion-ios-plus-outline"></i></span>
                                    <input class="form-control" type="text" name="credit_prefix" value="<?php echo $configs->getConfig('TRANSACTION_CREDIT_PREFIX'); ?>" placeholder="CR010" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Debit Prefix</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark"><i class="ion-ios-minus-outline"></i></span>
                                    <input class="form-control" type="text" name="debit_prefix" value="<?php echo $configs->getConfig('TRANSACTION_DEBIT_PREFIX'); ?>" placeholder="DB010" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Email Configuration (SMTP)</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Email<br><small>From address for emails</small></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="smtp_email" value="<?php echo $configs->getConfig('SMTP_EMAIL'); ?>" placeholder="example@gmail.com" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Host</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="smtp_host" value="<?php echo $configs->getConfig('SMTP_HOST'); ?>" placeholder="smtp.gmail.com" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Secure</label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="smtp_secure" value="<?php echo $configs->getConfig('SMTP_SECURE'); ?>" placeholder="TLS" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Port</label>
                            <div class="col-md-9">
                                <input class="form-control" type="number" name="smtp_port" value="<?php echo $configs->getConfig('SMTP_PORT'); ?>" placeholder="587" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP AUTHENTICATION<br><small>Recomended to Enable</small></label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="smtp_auth" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('SMTP_AUTH') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="smtp_auth" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('SMTP_AUTH') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Username<br><small>email for authentication</small></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="smtp_username" value="<?php echo $configs->getConfig('SMTP_USERNAME'); ?>" placeholder="example@gmail.com" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">SMTP Password<br><small>email password for authentication</small></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="smtp_password" value="<?php echo $configs->getConfig('SMTP_PASSWORD'); ?>" placeholder="Your Email Password Here" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Push Notifications</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Firebase API Key</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon text-dark"><i class="ion-ios-bell-outline"></i></span>
                                    <input class="form-control" type="text" name="firebase_key" value="<?php echo $configs->getConfig('FIREBASE_API_KEY'); ?>" placeholder="AIzaSyAv23NYMxosFEZdEF7kkWxs2Dv_FwdOGqo" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Global Configuration</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Enable OAOD<br><small>One Account for One Device</small></label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="oaod_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('OAOD_CHECK') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="oaod_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('OAOD_CHECK') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Redeem Page Notice</label>
                            <div class="col-md-9">
                                <input class="form-control" name="notice_redeem" placeholder="App Payouts will be processed within 24 Hours." value="<?php echo $configs->getConfig('NOTICE_REDEEM'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Refer Page Notice</label>
                            <div class="col-md-9">
                                <input class="form-control" name="notice_refer" placeholder="Self referring and Fake Referrals are not permitted." value="<?php echo $configs->getConfig('NOTICE_REFER_AND_EARN'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Transactions Page Notice</label>
                            <div class="col-md-9">
                                <input class="form-control" name="notice_transactions" placeholder="Showing all Credits and Debits on your Account" value="<?php echo $configs->getConfig('NOTICE_TRANSACTIONS'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Share Text</label>
                            <div class="col-md-9">
                                <input class="form-control" name="sharetext" placeholder="Hey Look, Check out this.." value="<?php echo $configs->getConfig('APP_SHARE_TEXT'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Web App Configuration</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Site Favicon</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <img id="site_favicon_image_display" src="images/<?php echo $configs->getConfig('SITE_FAVICON'); ?>" style="width: 40px; height: 40px; margin-right: 20px; box-shadow: 0 2px 10px var(--primary-alpha-Dot25);" />
                                    <input id="site_favicon_name" class="form-control" type="text" name="site_favicon_name" value="<?php echo $configs->getConfig('SITE_FAVICON'); ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
                                    <span class="input-group-addon text-dark"><label for="site_favicon_image" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
                                    <input id="site_favicon_image" name="site_favicon_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Site Logo Dark</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <img id="site_logo_dark_image_display" src="images/<?php echo $configs->getConfig('SITE_LOGO_DARK'); ?>" style="width: 180px;height: 40px; margin-right: 20px; box-shadow: 0 2px 10px var(--primary-alpha-Dot25);" />
                                    <input id="site_logo_dark_name" class="form-control" type="text" name="site_logo_dark_name" value="<?php echo $configs->getConfig('SITE_LOGO_DARK'); ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
                                    <span class="input-group-addon text-dark"><label for="site_logo_dark_image" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
                                    <input id="site_logo_dark_image" name="site_logo_dark_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Site Logo Light</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <img id="site_logo_light_image_display" src="images/<?php echo $configs->getConfig('SITE_LOGO_LIGHT'); ?>" style="width: 180px;height: 40px; margin-right: 20px; background: #000; box-shadow: 0 2px 10px var(--primary-alpha-Dot25);" />
                                    <input id="site_logo_light_name" class="form-control" type="text" name="site_logo_light_name" value="<?php echo $configs->getConfig('SITE_LOGO_LIGHT'); ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
                                    <span class="input-group-addon text-dark"><label for="site_logo_light_image" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
                                    <input id="site_logo_light_image" name="site_logo_light_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Show Recent Payouts</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="recent_payouts_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_RECENT_PAYOUTS') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="recent_payouts_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_RECENT_PAYOUTS') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Show New Feature</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="feature_notice_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_NEW_FEATURE_NOTICE') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="feature_notice_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_NEW_FEATURE_NOTICE') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Site Announcement</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="announce_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_ANNOUNCEMENT') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="announce_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('WEB_SHOW_ANNOUNCEMENT') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Announcement Text</label>
                            <div class="col-md-9">
                                <input class="form-control" name="announce_text" placeholder="Any Announcements Here" value="<?php echo $configs->getConfig('WEB_ANNOUNCEMENT_TEXT'); ?>" type="textarea" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Enable Google Login</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="google_login_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('GOOGLE_LOGIN_WEB') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="google_login_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('GOOGLE_LOGIN_WEB') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Google Client Id</label>
                            <div class="col-md-9">
                                <input class="form-control" name="google_login_clientid" placeholder="Google Client Id" value="<?php echo $configs->getConfig('GOOGLE_CLIENT_ID'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Google Secret Id</label>
                            <div class="col-md-9">
                                <input class="form-control" name="google_login_secret" placeholder="Google Secret Id" value="<?php echo $configs->getConfig('GOOGLE_SECRET_ID'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Enable Facebook Login</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="facebook_login_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('FACEBOOK_LOGIN_WEB') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="facebook_login_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('FACEBOOK_LOGIN_WEB') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Facebook App Id</label>
                            <div class="col-md-9">
                                <input class="form-control" name="facebook_login_appid" placeholder="Facebook App Id" value="<?php echo $configs->getConfig('FACEBOOK_APP_ID'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Facebook Secret Id</label>
                            <div class="col-md-9">
                                <input class="form-control" name="facebook_login_secret" placeholder="Facebook Secret Id" value="<?php echo $configs->getConfig('FACEBOOK_SECRET_ID'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Enable AdBlocker Detection</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="adblock_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('ADBLOCK_WEB') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="adblock_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('ADBLOCK_WEB') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">ADBLOCK Notice Title</label>
                            <div class="col-md-9">
                                <input class="form-control" name="adblock_notice_title" placeholder="App Id" value="<?php echo $configs->getConfig('NOTICE_ADBLOCK_TITLE'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">ADBLOCK Notice TEXT</label>
                            <div class="col-md-9">
                                <input class="form-control" name="adblock_notice_text" placeholder="App Id" value="<?php echo $configs->getConfig('NOTICE_ADBLOCK'); ?>" type="text" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">Android App Configuration</h5>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">App Package Name</label>
                            <div class="col-md-9">
                                <input class="form-control" name="package" placeholder="Package Name" value="<?php echo $configs->getConfig('PACKAGE_NAME'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Android SHARE URL</label>
                            <div class="col-md-9">
                                <input class="form-control" name="android_share_url" placeholder="Android SHARE URL" value="<?php echo $configs->getConfig('APP_SHARE_URL_ANDROID'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Navigation Bar</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="navbar_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('APP_NAVBAR_ENABLE') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="navbar_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('APP_NAVBAR_ENABLE') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">APP Tabs UI</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="tabs_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('APP_TABS_ENABLE') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="tabs_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('APP_TABS_ENABLE') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Global API Offers</label>
                            <div class="col-md-2">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio success">
                                        <input name="apioffers_enable" id="activation" value="1" class="custom-control-input" type="radio" <?php if($configs->getConfig('API_OFFERS_ACTIVE') == '1') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Enable
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-controls">
                                    <label class="custom-control custom-radio danger">
                                        <input name="apioffers_enable" id="deactivation" value="0" class="custom-control-input" type="radio" <?php if($configs->getConfig('API_OFFERS_ACTIVE') == '0') { echo "checked='checked'"; } ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Disable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">Country Detection</label>
                            <div class="col-md-9 price">
                                <span class="badge badge-pill bg-success">Automatic</span>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="block-bw-heading">IOS App Configuration</h5>
                
                    <div class="form-group">
                        <div class="form-row">
                            <label class="col-md-3">IOS SHARE URL</label>
                            <div class="col-md-9">
                                <input class="form-control" name="ios_share_url" placeholder="IOS SHARE URL" value="<?php echo $configs->getConfig('APP_SHARE_URL_IOS'); ?>" type="text" autocomplete="off" required=""/>
                            </div>
                        </div>
                    </div>
                    
                    <hr />

                    <button class="btn btn-primary mr-3 pull-right" type="submit">Save Settings</button>
                    <br><br>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="admin-table-card">
                <div class="card-header"><h5>Stats</h5></div>
                <div style="padding: 1rem; line-height: 2;">
                    <?php 
                    echo "WebPanel Version: <b>".$configs->getConfig('VERSION')."</b><br>";
                    $result = $dbo->query('select version()')->fetchColumn();
                    echo "MySQL Version : <b>".$result."</b><br>";
                    echo "PHP Version : <b>".phpversion()."</b><br>";
                    ?>
                    Changelog : <a href="https://www.codyhub.com/item/android-rewards-app-pocket/#change-log" target="_blank">Changelog</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'inc/support.php'; ?>
</div><!-- /admin-content -->

<?php include_once 'inc/admin_footer.php'; ?>
