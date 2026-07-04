<?php

    

?>
    <!--Begin:: AdBlock Notice -->
    <div id="adblock_layout" class="alert alert-danger" role="alert" style="display: none;" >
        <div class="alert-icon"><i class="flaticon-warning kt-font-white"></i></div>
        <div class="alert-text">
            <h4 class="alert-heading"><?php echo esc_attr($configs->getConfig("NOTICE_ADBLOCK_TITLE")); ?></h4>
            <p><?php echo esc_attr($configs->getConfig("NOTICE_ADBLOCK")); ?></p>
        </div>
    </div>
    <!--End:: AdBlock Notice -->
    
    <script src="../admin/core/libraries/BlockAdBlock/blockadblock.js"></script>
    <script type="text/javascript">
    
    function adBlockDetected(){ document.getElementById("adblock_layout").style.display = "flex";}
    function adBlockNotDetected(){ $('#adblock_layout').hide(); }
    
    if(typeof blockAdBlock === 'undefined') {
        adBlockDetected();
    }else{
        blockAdBlock.onDetected(adBlockDetected).onNotDetected(adBlockNotDetected);
    }
    </script>