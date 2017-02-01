<?php
if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
$GLOBALS['wp_php_rv'] = '5.4'; //php-required-version//
if (require(__DIR__.'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once __DIR__.'/src/includes/uninstall.php';
}
