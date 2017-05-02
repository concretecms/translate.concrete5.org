<?php
defined('C5_EXECUTE') or die('Access Denied.');

# Load in the composer vendor files
require_once dirname(__DIR__, 3) . "/vendor/autoload.php";

# Add the vendor directory to the include path
ini_set('include_path', dirname(__DIR__, 3) . "/vendor" . PATH_SEPARATOR . get_include_path());

# Include the environment variables
try {
    $dotenv = new Dotenv\Dotenv(dirname(__DIR__, 3));
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // .env file doesn't exist.
}
