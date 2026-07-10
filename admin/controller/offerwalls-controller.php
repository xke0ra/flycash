<?php

    if (!isset($configs) || !isset($user_username)) { return; }

    $offerwall_urls = array();
    $offerwall_urls['youtube'] = "videos-beta.php";
    
    $active_offerwalls = array();
    $offerwalls_list = new offerwalls($dbo);
    $offerwalls_list = $offerwalls_list->getOfferwalls(0);
    $offerwalls_list = $offerwalls_list['offerwalls'];
    
    $not_offerwalls = array("checkin", "spin", "refer", "redeem", "instructions", "transactions", "share", "rate", "about");
    
    $position = 1;
    
    foreach($offerwalls_list as $offerwall){
        
        $offerwall_type = $offerwall['offer_type'];
        $offerwall_status = $offerwall['offer_status'];
        
        if(!in_array($offerwall_type, $not_offerwalls) && $offerwall_status === 'Active'){
            
            // PUSHING TO ACTIVE OFFERWALLS
            array_push($active_offerwalls, $offerwall);
            
            // CUSTOM OFFERWALLS — URL with {user_id} placeholder
            $offerwall_url = $offerwall['offer_url'];
            $offerwall_url = str_replace("{user_id}",$user_username,$offerwall_url);
            $offerwall_urls[$offerwall_type] =  $offerwall_url;
            
            // CHECKING FOR 1ST POSTION TO MAKE IT ACTIVE
            if($position == 1){
                $offerwall_active[$offerwall_type] = 'active';
                
                // Initial Offerwall
                $initial_offerwall = $offerwall_urls[$offerwall_type];
            }
            $position = $position+1;
            
        }
    }
    
    // OFFERWALL LOADING LAYOUT
    $enable_loadingScreen = true;
    $loading_text = 'Loading ...';
    $loading_spinner = 'kt-spinner--brand';
    $loading_timer = 1000;
    
?>
<script type="text/javascript">
function show_offerwall(offerwall) {
    
    var offerwalls = <?php echo json_encode($offerwall_urls); ?>
    
    document.getElementById("offerwall").innerHTML = '';
    
    document.getElementById("offerwall").innerHTML = '<div class="loading_screen"><div class="h3"><span class="kt-spinner kt-spinner--v2 kt-spinner--lg <?php echo $loading_spinner; ?>"><span class="ml-5"><?php echo $loading_text; ?></span></span></div></div>';
    
    document.getElementById("offerwall").innerHTML += '<!-- START:: OFFER WALL --><div class="offerwall_screen" id="offerwall_screen" style="display: none;"></div><!-- END:: OFFER WALL -->';
    document.getElementById("offerwall_screen").innerHTML = "";
    document.getElementById("offerwall_screen").innerHTML = '<iframe src="'+offerwalls[offerwall]+'" class="offerwall-iframe" frameborder="0"></iframe>';
    
    setTimeout(function() {
        
        $('.loading_screen').hide();
        $('.offerwall_screen').show();
        
    }, <?php echo $loading_timer; ?>);
}
</script>
