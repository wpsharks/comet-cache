<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

/*
 * If implemented; this file should go in this special directory.
 *    `/wp-content/ac-plugins/my-ac-plugin.php`
 */

function my_ac_plugin() // Example plugin.
{
	/**
	 * All plugins need a reference to this class object instance.
	 *
	 * @var $ac \quick_cache\advanced_cache Object instance.
	 */
	$ac = $GLOBALS['quick_cache__advanced_cache']; // See: `advanced-cache.php`.

	/*
	 * This plugin will dynamically modify the version salt.
	 */
	$ac->add_filter(get_class($ac).'__version_salt', 'my_ac_version_salt_shaker');
}

my_ac_plugin(); // Run this plugin.

/*
 * Any other function(s) that may support your plugin.
 */

function my_ac_version_salt_shaker($version_salt) // Salt shaker.
{
	if(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') !== FALSE)
		$version_salt .= 'iphones'; // Give iPhones their own variation of the cache.

	else if(stripos($_SERVER['HTTP_USER_AGENT'], 'android') !== FALSE)
		$version_salt .= 'androids'; // Give Androids their own variation of the cache.

	else $version_salt .= 'other'; // A default group for all others.

	return $version_salt;
}

