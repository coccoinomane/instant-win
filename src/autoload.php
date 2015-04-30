<?php

// Define the directory with respect to which the namespaces are defined. This
// affects also where the 'use' function is going to look for the classes.
define('SRC_DIR', dirname( dirname( __FILE__ ) ) . '/src/');

/**
 * Where to look for classes when using the 'use' function.
 */
function __autoload($class)
{
    $parts = explode('\\', $class);
    $path = SRC_DIR . implode(DIRECTORY_SEPARATOR, $parts);
    require $path . '.php';
}

?>