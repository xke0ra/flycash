<?php
// READ ME
	 // Wondering How to add new or custom offerwall ? Well, You can Add Custom Offerwall from Admin Panel itself <3

	 include_once(__DIR__."/../../admin/controller/offerwalls-controller.php");

?>
<div class="card-modern">
    <div class="card-modern-header">
        <h3>Offerwalls</h3>
    </div>
    <?php if (isset($active_offerwalls) && is_array($active_offerwalls) && count($active_offerwalls) > 0): ?>
    <div>
        <ul class="tabs-modern">
            <?php foreach($active_offerwalls as $offerwall){
                $offerwall_type = $offerwall['offer_type'];
                $offerwall_name = $offerwall['offer_title'];
                $offerwall_show = "show_offerwall('".$offerwall_type."')";
                $offerwall_active = isset($offerwall_active[$offerwall_type]) ? $offerwall_active[$offerwall_type] : '';
                echo '<li>';
                echo '<button class="tab-link '.$offerwall_active.'" onclick="'.$offerwall_show.'">'.$offerwall_name.'</button>';
                echo '</li>';
            } ?>
        </ul>
        <div class="card-modern-body" style="padding:0;">
            <div class="tab-content">
                <div class="tab-pane active" id="offerwall" role="tabpanel">
                    <iframe src="<?php echo isset($initial_offerwall) ? $initial_offerwall : ''; ?>" frameborder="0" class="offerwall-iframe"></iframe>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card-modern-body">
        <div class="notif-empty" style="padding:48px 16px;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--gray-300);margin-bottom:12px;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
            <div style="font-size:15px;color:var(--gray-500);margin-bottom:4px;">No offerwalls available</div>
            <div style="font-size:13px;color:var(--gray-400);">Check back soon for new offers.</div>
        </div>
    </div>
    <?php endif; ?>
</div>
