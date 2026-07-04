<?php

    /*!
	 * POCKET v3.4
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2019 AYM ( http://www.aym.com )
	 */

/**
 * The SPL __autoload() method is one of the Magic Methods supplied in PHP. It is used to autoload
 * classes so that you do not need to 'include' them in your scripts.
 */
function autoload($class) {
	
    $path = __DIR__ . "/class." . $class . ".inc.php";
    if (file_exists($path)) {
        require $path;
    } else {
        exit('{"error":true,"error_code":0,"error_description":"Error License Authorization"}');
    }
}

// spl_autoload_register
spl_autoload_register("autoload");
