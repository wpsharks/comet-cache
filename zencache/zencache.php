<?php
/*
Version: 150218
Text Domain: zencache
Plugin Name: ZenCache
Network: true

Author: s2Member® / WebSharks, Inc.
Author URI: http://www.s2member.com

Plugin URI: http://www.websharks-inc.com/product/zencache/
Description: WordPress advanced cache plugin; speed without compromise!

Speed up your site (BIG time!) — ZenCache provides reliable page caching for WordPress. Easy-to-use (very simple installation).
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

$GLOBALS['wp_php_rv'] = '5.3'; // Require PHP v5.3+.
if(require(dirname(__FILE__).'/submodules/wp-php-rv/wp-php-rv.php'))
	require_once dirname(__FILE__).'/zencache.inc.php';
else wp_php_rv_notice('ZenCache');
