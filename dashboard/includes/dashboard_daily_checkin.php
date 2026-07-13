<?php
$daily_points = esc_attr($configs->getConfig('DAILY_REWARD'));  // change the points in Admin Panel
	 
	 /*
	 *
	 *
	 *  
	 *     START EDITING - EDIT BELOW TEXT ONLY
	 *
	 *
	 */
	 
	 $daily_chekin = "Daily Checkin";
	 
	 $daily_chekin_title = "Checkin Daily to get ".$daily_points." Points.";
	 
	 $daily_reward_taken = "You've taken checkin Reward Today !";
	 
	 $daily_checkin_try_again = "Try again after";
	 
	 $daily_checkin_dialog_decsription ="You'll get this reward daily. So, please login daily to get this reward.";
	 
	 $daily_checkin_dialog_button_text = "Proceed";
	 
	 /*
	 *
	 *
	 *  
	 *     STOP EDITING - DO NOT EDIT BELOW TEXT OR CODE
	 *
	 *
	 */
	 
	 $timeLeftForDailyCheckin = $configs->getDailyCheckinTimeLeft($req_user_info['id']);
	 
	  
?>
                                	    <script>
                                	    
                                	    var dailycheckintime = <?php echo esc_attr($timeLeftForDailyCheckin); ?>;
                                	    var daily_checkin_title = "<?php echo esc_attr($daily_chekin); ?>";
                                	    var daily_checkin_description = "<?php echo esc_attr($daily_checkin_dialog_decsription); ?>";
                                	    var daily_checkin_button_text = "<?php echo esc_attr($daily_checkin_dialog_button_text); ?>";
                                	    
                                	    </script>
                                	    
                                        <div class="kt-portlet kt-bg-dark kt-portlet--skin-solid">
                                        	<div class="kt-portlet__head ">
                                        		<div class="kt-portlet__head-label">
                                        			<h3 class="kt-portlet__head-title"><?php echo esc_attr($daily_chekin); ?></h3>
                                        		</div>
                                        	</div>
                                        	<div class="kt-portlet__body">
                                        		<!--begin::Widget 7-->
                                        		<div class="kt-widget7 kt-widget7--skin-light">
                                        			<div class="kt-widget7__desc mt-0">
                                        			   <?php echo esc_attr($daily_chekin_title); ?>
                                        			</div>
                                        			
                                        			<div class="kt-widget7__desc mt-5 mb-7">
                                        			    
                                        			    <p class="daily-checkin-taken-title"><?php echo esc_attr($daily_reward_taken); ?></p>
                                        			    <small class="daily-checkin-try-again"><?php echo esc_attr($daily_checkin_try_again); ?></small>
                                        			    
                                        			    <div class="mb-2">
                                        			        <daily-checkin-timer class="checkin-timer"></daily-checkin-timer>
                                        			    </div>
                                        			    
                                        			</div>
                                        		</div>
                                        		<!--end::Widget 7--> 
                                        	</div>
                                        </div>