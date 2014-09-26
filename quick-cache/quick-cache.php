<?php
/*
Version: 140926
Text Domain: quick-cache
Plugin Name: Quick Cache
Network: true

Author: s2Member® / WebSharks, Inc.
Author URI: http://www.s2member.com

Plugin URI: http://www.websharks-inc.com/product/quick-cache/
Description: WordPress advanced cache plugin; speed without compromise!

Speed up your site (BIG time!) — Quick Cache provides reliable page caching for WordPress. Easy-to-use (very simple installation).
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(require(dirname(__FILE__).'/includes/wp-php53.php')) // TRUE if running PHP v5.3+.
	require_once dirname(__FILE__).'/quick-cache.inc.php';
else wp_php53_notice('Quick Cache');