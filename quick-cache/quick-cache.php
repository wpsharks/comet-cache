<?php
/*
Version: 131121
Text Domain: quick-cache
Plugin Name: Quick Cache

Author: s2Member® / WebSharks, Inc.
Author URI: http://www.s2member.com

Plugin URI: http://www.websharks-inc.com/product/quick-cache/
Description: WordPress advanced cache plugin; speed without compromise!

Speed up your site ~ BIG Time! - If you care about the speed of your site, Quick Cache is a plugin that you absolutely MUST have installed :-)
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(version_compare(PHP_VERSION, '5.3', '<'))
	throw new exception('This version of Quick Cache requires PHP v5.3+.');

require_once dirname(__FILE__).'/quick-cache.inc.php';