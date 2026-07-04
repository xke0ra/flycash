<?php

    $pagename = isset($pagename) ? $pagename : 'none';


    if($configs->getConfig("ADBLOCK_WEB")){
        
        include_once("../admin/controller/adblock.php");
    
    }
    
    if($pagename === 'dashboard'){
        
    }elseif($pagename === 'redeem'){
    
        echo '<!--Begin:: Custom Redeem Notice -->';
        echo '<div class="alert alert-light alert-elevate" role="alert">';
            echo '<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>';
                echo '<div class="alert-text">';
                echo $configs->getConfig("NOTICE_REDEEM");
            echo '</div>';
        echo '</div>';
        echo '<!--End:: Custom Redeem Notice -->';
        
        
    }elseif($pagename === 'refer'){
    
        echo '<!--Begin:: Custom Redeem Notice -->';
        echo '<div class="alert alert-light alert-elevate" role="alert">';
            echo '<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>';
                echo '<div class="alert-text">';
                echo $configs->getConfig("NOTICE_REFER_AND_EARN");
            echo '</div>';
        echo '</div>';
        echo '<!--End:: Custom Redeem Notice -->';
        
        
    }elseif($pagename === 'transactions'){
    
        echo '<!--Begin:: Custom Redeem Notice -->';
        echo '<div class="alert alert-light alert-elevate" role="alert">';
            echo '<div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>';
                echo '<div class="alert-text">';
                echo $configs->getConfig("NOTICE_TRANSACTIONS");
            echo '</div>';
        echo '</div>';
        echo '<!--End:: Custom Redeem Notice -->';
        
        
    }
    
    
    
    

?>