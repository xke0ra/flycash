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


	 include_once(__DIR__."/../../admin/controller/offerwalls-controller.php");

?>
<div class="card-modern">
    <div class="card-modern-header">
        <h3>Offerwalls</h3>
    </div>
    <div>
        <ul class="tabs-modern">
            <?php
            if (isset($active_offerwalls) && is_array($active_offerwalls)) {
                foreach($active_offerwalls as $offerwall){
                    $offerwall_type = $offerwall['offer_type'];
                    $offerwall_name = $offerwall['offer_title'];
                    $offerwall_show = "show_offerwall('".$offerwall_type."')";
                    $offerwall_active = isset($offerwall_active[$offerwall_type]) ? $offerwall_active[$offerwall_type] : '';
                    echo '<li>';
                    echo '<button class="tab-link '.$offerwall_active.'" onclick="'.$offerwall_show.'">'.$offerwall_name.'</button>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
        <div class="card-modern-body" style="padding:0;">
            <div class="tab-content">
                <div class="tab-pane active" id="offerwall" role="tabpanel">
                    <iframe src="<?php echo isset($initial_offerwall) ? $initial_offerwall : ''; ?>" frameborder="0" class="offerwall-iframe"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
