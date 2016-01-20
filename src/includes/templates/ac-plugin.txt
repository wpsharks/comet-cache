<?php
/**
 * Example AC (Advanced Cache) Plugin File.
 *
 * If implemented; this file should go in this special directory:
 *    `/wp-content/ac-plugins/my-ac-plugin.php`
 */
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
function my_ac_plugin() // Example plugin.
{
    $ac = $GLOBALS['zencache_advanced_cache']; // ZenCache instance.
    $ac->addFilter('zencache_version_salt', 'my_ac_version_salt_shaker');
}
function my_ac_version_salt_shaker($version_salt)
{
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') !== false) {
        $version_salt .= 'iphones'; // Give iPhones their own variation of the cache.
    } elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'android') !== false) {
        $version_salt .= 'androids'; // Androic variation.
    } else {
        $version_salt .= 'other'; // A default group.
    }
    return $version_salt;
}
my_ac_plugin(); // Run this plugin.
