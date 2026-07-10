<?php
$pagename = 'postbacks';
	$container = 'settings';
	
	include_once("inc/admin.inc.php");
	
	$configs->updateConfigs(time(),'LAST_ADMIN_ACCESS');
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'].= $_SERVER['REQUEST_URI'];

include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Postbacks</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading" style="border: none;">
                                <h5>Postbacks S2S ( Server to Server )</h5>
                                <p class="mt-2">Whenever a user completes an offer, the AdNetworks will send a URL request, called a 'Server to Server Postback' with some information. Using this information, we can Reward the user who performed/completed the action/offer accordingly.
                                To receive a successful postback request, you need to give below url's as a postback url for each AdNetwork Accordingly or else user will not be rewarded. Please Read the Documentation for more information on it.<br><br>
                                The Below URL's are given asuming that the postback files are in the folder named <strong>postbacks</strong>. Incase if you change the postbacks folder name to something else, then you need to change the same in the url while giving it to the AdNetworks ..</p>
                            </div>
                            
                            <form action="" method="post" class="horizontal-form">
                                
                                <h5 class="block-bw-heading">AdMantum</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postback URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/admantum.php?user_id={uid}&amount={virtual_currency}" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="block-bw-heading">Wannads</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postback URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/wannads.php" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="block-bw-heading">CpaLead</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postback URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/cpalead.php?subid={subid}&subid2={subid2}&virtual_currency={virtual_currency}" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="block-bw-heading">AdGatemedia</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postbacks URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/adgatemedia.php?tx_id={transaction_id}&user_id={s2}&point_value={points}&usd_value={payout}&offer_title={vc_title}" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="block-bw-heading">AdscendMedia</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postbacks URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/adscendmedia.php?offerid=[OID]&name=[ONM]&rate=[CUR]&sub1=[SB1]" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="block-bw-heading">KiwiWall</h5>
                                
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-2">Postbacks URL</label>
                                        <div class="col-md-10">
                                            <input name="oftro_pb" class="form-control" value="<?php echo htmlspecialchars($configs->getConfig('WEB_ROOT'), ENT_QUOTES, 'UTF-8'); ?>postbacks/kiwiwall.php" type="text" autocomplete="off" required="" style="background: #e9ecef; " disabled/>
                                        </div>
                                    </div>
                                </div>
								
                                <hr/>
								<br><br>
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
