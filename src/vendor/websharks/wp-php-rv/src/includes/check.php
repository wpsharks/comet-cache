<?php
/**
 * PHP vX.x Handlers.
 *
 * @since 141004 First documented version.
 *
 * @copyright WebSharks, Inc. <http://websharks-inc.com>
 * @license GNU General Public License, version 3
 */
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
if (!function_exists('wp_php_rv')) {
    require_once dirname(__FILE__).'/functions/.load.php';
}
___wp_php_rv_initialize(); // Run initilization routines.
return wp_php_rv(); // True if running PHP vX.x+ w/ required extensions.
