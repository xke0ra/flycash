<?php
	 /*
	 *  AddOn Name      :   AdGem Offerwall
	 *  AddOn URL       :   https://www.aym.com/item/adgem-offerwall/
	 *  AddOn License   :   https://www.aym.com/licenses/
	 *
	 *  This Code is a part of Premium AddOn. Do not Share this code.
	 * 
	 *  ALL RIGHTS RESERVED
	 *
	 *  http://www.aym.com
	 *  support@aym.com
	 *
	 *  Copyright 2020 AYM ( http://www.aym.com )
	 *
	 */
    
    // URL : https://your-domain.com/postbacks/adgem.php?user_id={player_id}&amount={amount}
    
    include_once("../admin/core/init.inc.php");

    $handler = new FlyCash\Postback\Handlers\AdGemHandler($dbo);
    $handler->handle();
    
?>
