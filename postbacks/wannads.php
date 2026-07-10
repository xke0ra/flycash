<?php
include_once("../admin/core/init.inc.php");

    $handler = new FlyCash\Postback\Handlers\WannadsHandler($dbo);
    $handler->handle();
        

?>