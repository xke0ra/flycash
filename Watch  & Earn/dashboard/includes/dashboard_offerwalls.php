<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */
	 
	 // READ ME
	 // Wondering How to add new or custom offerwall ? Well, You can Add Custom Offerwall from Admin Panel itself <3
	 
	 
	 include_once("../admin/controller/offerwalls-controller.php");
	 
?>
                                        <div class="kt-portlet kt-portlet--tabs">
                                            <div class="kt-portlet__head">
                                                <div class="kt-portlet__head-toolbar">
                                                    <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right" role="tablist">
                                                        
                                                     
                                                        <?php
                                                        foreach($active_offerwalls as $offerwall){
                                                            
                                                            $offerwall_type = $offerwall['offer_type'];
                                                            $offerwall_name = $offerwall['offer_title'];
                                                            $offerwall_show = "show_offerwall('".$offerwall_type."')";
                                                            $offerwall_active = isset($offerwall_active[$offerwall_type]) ? $offerwall_active[$offerwall_type] : '';
                                                            
                                                            echo '<li class="nav-item">';
                                                            echo '<a class="nav-link '.$offerwall_active.'" onclick="'.$offerwall_show.'" data-toggle="tab" href="#" role="tab" aria-selected="true">';
                                                            echo '<i class="fa fa-dollar-sign" aria-hidden="true"></i>'.$offerwall_name.'</a></li>';
                                                            
                                                            
                                                        } ?>
                                                        <li class="nav-item"><a class="nav-link " onclick="show_offerwall('youtube')" data-toggle="tab" href="#" role="tab" aria-selected="true"><i class="fa fa-dollar-sign" aria-hidden="true"></i>Watch  & Earn <img alt="Logo" src="/admin/images/new.gif" class="kt-header__brand-logo-sticky" style="
    width: 47px;
"></a></li>                                                    
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <div class="kt-portlet__body">
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="offerwall" role="tabpanel">
                                                        
                                                        <iframe src="<?php echo isset($initial_offerwall) ? $initial_offerwall : ''; ?>" frameborder="0" width="100%" height="2400" ></iframe>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>