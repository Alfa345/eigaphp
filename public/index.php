<?php
// public/index.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define ROOT_PATH - this points to /var/www/html (the 'eigaphp' root on server)
// It should be the directory containing 'config', 'includes', 'public', etc.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Include the main configuration file
// This will set up the session, database connection, constants, etc.
// It should also now correctly find 'includes/function.php' if config.php is fixed (see Step 2)
require_once ROOT_PATH . '/config/config.php';

// Now, instead of the debug code, include the actual logic
// for your homepage, which seems to be in _legacy_source_files/old_index.php
// This file likely handles fetching movies and displaying the main page content.
require_once ROOT_PATH . '/_legacy_source_files/old_index.php';

?>