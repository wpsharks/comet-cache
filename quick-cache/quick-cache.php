<?php
/*
Version: 131127
Text Domain: quick-cache
Plugin Name: Quick Cache

Author: s2Member® / WebSharks, Inc.
Author URI: http://www.s2member.com

Plugin URI: http://www.websharks-inc.com/product/quick-cache/
Description: WordPress advanced cache plugin; speed without compromise!

Speed up your site (BIG time!) — Quick Cache provides reliable page caching for WordPress. Easy-to-use (very simple installation).
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(version_compare(PHP_VERSION, '5.3', '<'))
	{
		function quick_cache_php53_dashboard_notice()
			{
				echo __('<div class="error"><p>Plugin NOT active. This version of Quick Cache requires PHP v5.3+.</p></div>', 'quick-cache');
			}
		add_action('all_admin_notices', 'quick_cache_php53_dashboard_notice');
	}
else require_once dirname(__FILE__).'/quick-cache.inc.php';