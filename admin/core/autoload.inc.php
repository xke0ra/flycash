<?php
/**
 * The SPL __autoload() method is one of the Magic Methods supplied in PHP. It is used to autoload
 * classes so that you do not need to 'include' them in your scripts.
 */
function autoload($class) {
	
    $path = __DIR__ . "/class." . $class . ".inc.php";
    if (file_exists($path)) {
        require $path;
    } else {
        if (isset($GLOBALS['logger'])) {
            $GLOBALS['logger']->error('Class not found', ['class' => $class, 'path' => $path]);
        }
        exit('{"error":true,"error_code":0,"error_description":"Error License Authorization"}');
    }
}

// spl_autoload_register
spl_autoload_register("autoload");
