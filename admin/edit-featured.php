<?php
$pagename = 'offerwalls';
	$container = 'offerwalls';
	
	include_once("inc/admin.inc.php");
	$valid = false;

    if(isset($_GET['id'])){
		
		$ID = (int)$_GET['id'];
		$configs = new functions($dbo);
        $offerwalls = new offerwalls($dbo);
        $result = $offerwalls->getSingleOfferWall($ID);
        
        if(isset($result['offer_id'])){
            
            $valid = true;
        }
        
    }
    
    if(!$valid){
        
        header("Location: offerwalls.php");
        
    }
    
include_once 'inc/admin_header.php';
?>
<div class="admin-content">
        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Edit Featured option Details</h4>
                        </div>
                    </div>
					<?php if(APP_DEMO) { include_once 'inc/demo-notice.php'; } ?>
					
					<!-- START MAIN CONTENT HERE -->
					
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5><?php if($valid){ echo htmlspecialchars($result['offer_title'], ENT_QUOTES, 'UTF-8'); } ?> Configuration</h5>
                            </div>

                            <form action="process/edit-featured.php" method="post" enctype="multipart/form-data" class="horizontal-form">
							<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Position</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="position" type="number" value="<?php if($valid){ echo (int)$result['offer_position']; } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="id" type="text" value="<?php if($valid){ echo (int)$result['offer_id']; } ?>" hidden/>
                                            <input class="form-control" name="type" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_type'], ENT_QUOTES, 'UTF-8'); } ?>" hidden/>
                                            <input class="form-control" name="name" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_title'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Sub Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="sub" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_subtitle'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Image</label>
                                        <div class="col-md-9">
                                            <div class="input-group">
                                                <input id="image_name" class="form-control" type="text" name="image_name" value="<?php if($valid){ echo htmlspecialchars(basename($result['offer_thumbnail']), ENT_QUOTES, 'UTF-8'); } ?>" placeholder="Choose Image" style="background: #e9ecef; " autocomplete="off" disabled/>
												<span class="input-group-addon text-dark"><label for="file-upload" class="custom-file-upload"><i class="ion-ios-folder"></i><span>Change Image</span></label>
													<input id="file-upload" onchange="readURL(this);" name="offer_image" accept="image/png, image/jpeg, image/jpg" type="file"/>
												</span>
											</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if($valid){ if(strpos($result['offer_type'], 'custom_offerwall_') !== false){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">OfferWall URL</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="https://example.com/?user={user_id}" type="text" value="<?php if($valid){ echo htmlspecialchars($result['offer_url'], ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                            <br><small style="text-transform: none;">use {user_id} in the url to replace the original user id of the system</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "checkin" || $result['offer_type'] == "refer"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3"><?php echo htmlspecialchars($result['offer_title'], ENT_QUOTES, 'UTF-8'); ?> Reward</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="points" placeholder="25" type="text" value="<?php if($valid){ echo (int)$result['offer_points']; } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "admantum"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdMantum PubId</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="217543" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdMantum_PubId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdMantum AppId</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="11969" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdMantum_AppId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdMantum Secret Key</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val3" placeholder="adm1234567" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdMantum_SecretKey'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "adgatemedia"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdGateMedia Wall Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="naulrg" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdGate_Media_WallId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "fyber"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Fyber App Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="105937" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('Fyber_AppId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Fyber Security Token</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="dfff3dda950b7b7e76be7845e2635" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('Fyber_SecurityToken'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "adscendmedia"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdscendMedia Pub Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="107461" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdScendMedia_PubId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdscendMedia Ad Wall Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="7351" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdScendMedia_AdwallId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "spin"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Spin Credit Title</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="Spin Wheel Credit" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('SPIN_REWARD_TITLE'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "cpalead"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">CpaLead Direct Link</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="https://viral782.com/list/381406" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('CpaLead_DirectLink'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "wannads"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Wannads API Key</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="5de64c1be7d46893158082" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('WannadsApiKey'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "kiwiwall"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">KiwiWall Wall Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="x7WSuXuvjGsHLevNVY4qiORPLFb7RBAS" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('KiwiWall_WallId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">KiwiWall API Key</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="2udP6T46zDewoo973hLiIg9h6Gj4jF3J" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('KiwiWall_APIKEY'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">KiwiWall Secret Key</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val3" placeholder="rHyprpB1hgjhNu86ASSZ7VBXqed5nmI" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('KiwiWall_SECKEY'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "adgem"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdGem App Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="2056" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdGem_AppId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">AdGem API KEY <br><small>APP Security Token (optional)</small></label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="token1234" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('AdGem_ApiKey'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>
                                
                                <?php if($valid){ if($result['offer_type'] == "offertoro"){ ?>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">OfferToro Pub Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val1" placeholder="5445" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('OfferToro_PubId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">OfferToro App Id</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val2" placeholder="3084" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('OfferToro_AppId'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
							
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">OfferToro Secret Key</label>
                                        <div class="col-md-9">
                                            <input class="form-control" name="val3" placeholder="secretkey1234" type="text" value="<?php if($valid){ echo htmlspecialchars($configs->getConfig('OfferToro_SecretKey'), ENT_QUOTES, 'UTF-8'); } ?>" required=""/>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php } } ?>

                                <hr />

                                <button class="btn btn-primary mr-3 pull-right" type="submit">Save Settings</button>
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

<script type="text/javascript">

function readURL(input) {
	
	if (input.files && input.files[0]) {
		
		var reader = new FileReader();
		
		reader.readAsDataURL(input.files[0]);
		$('#image_name').val(input.files[0].name);
		$('#image_name').prop('disabled', false);
	}
}

</script>
