<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
/*
Version: 111203
Stable tag: 111203
Framework: WS-P-110523

SSL Compatible: yes
WordPress Compatible: yes
WP Multisite Compatible: yes
Multisite Blog Farm Compatible: yes

Tested up to: 3.3
Requires at least: 3.2
Requires: WordPress® 3.2+, PHP 5.2.3+

Copyright: © 2009 WebSharks, Inc.
License: GNU General Public License
Contributors: WebSharks, PriMoThemes
Author URI: http://www.primothemes.com/
Author: PriMoThemes.com / WebSharks, Inc.
Donate link: http://www.primothemes.com/donate/

Text Domain: qcache
Domain Path: /includes/translations

Plugin Name: Quick Cache
Forum URI: http://www.primothemes.com/forums/viewforum.php?f=5
Privacy URI: http://www.primothemes.com/about/privacy-policy/
Plugin URI: http://www.primothemes.com/post/product/quick-cache-plugin-for-wordpress/
Description: Dramatically improves the performance & speed of your site! Also compatible with WordPress® Multisite/Networking.
Tags: cache, quick cache, quick-cache, quickcache, speed, performance, loading, generation, execution, benchmark, benchmarking, debug, debugging, caching, cash, caching, cacheing, super cache, advanced cache, advanced-cache, wp-cache, wp cache, options panel included, websharks framework, w3c validated code, includes extensive documentation, highly extensible
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/*
Define versions.
*/
@define ("WS_PLUGIN__QCACHE_VERSION", "111203");
@define ("WS_PLUGIN__QCACHE_MIN_PHP_VERSION", "5.2.3");
@define ("WS_PLUGIN__QCACHE_MIN_WP_VERSION", "3.2");
@define ("WS_PLUGIN__QCACHE_MIN_PRO_VERSION", "111203");
/*
Several compatibility checks.
If all pass, load the Quick Cache plugin.
*/
if (version_compare (PHP_VERSION, WS_PLUGIN__QCACHE_MIN_PHP_VERSION, ">=") && version_compare (get_bloginfo ("version"), WS_PLUGIN__QCACHE_MIN_WP_VERSION, ">=") && !isset ($GLOBALS["WS_PLUGIN__"]["qcache"]))
	{
		$GLOBALS["WS_PLUGIN__"]["qcache"]["l"] = __FILE__;
		/*
		Hook before loaded.
		*/
		do_action("ws_plugin__qcache_before_loaded");
		/*
		System configuraton.
		*/
		include_once dirname (__FILE__) . "/includes/syscon.inc.php";
		/*
		Hooks and filters.
		*/
		include_once dirname (__FILE__) . "/includes/hooks.inc.php";
		/*
		Hook after system config & hooks are loaded.
		*/
		do_action("ws_plugin__qcache_config_hooks_loaded");
		/*
		Load a possible Pro module, if/when available.
		*/
		if (apply_filters ("ws_plugin__qcache_load_pro", true) && file_exists (dirname (__FILE__) . "-pro/pro-module.php"))
			include_once dirname (__FILE__) . "-pro/pro-module.php";
		/*
		Configure options and their defaults now.
		*/
		ws_plugin__qcache_configure_options_and_their_defaults ();
		/*
		Function includes.
		*/
		include_once dirname (__FILE__) . "/includes/funcs.inc.php";
		/*
		Include shortcodes.
		*/
		include_once dirname (__FILE__) . "/includes/codes.inc.php";
		/*
		Hook after loaded.
		*/
		do_action("ws_plugin__qcache_loaded");
		do_action("ws_plugin__qcache_after_loaded");
	}
/*
Else NOT compatible. Do we need admin compatibility errors now?
*/
else if (is_admin ()) /* Admin compatibility errors. */
	{
		if (!version_compare (PHP_VERSION, WS_PLUGIN__QCACHE_MIN_PHP_VERSION, ">="))
			{
				add_action ("all_admin_notices", create_function ('', 'echo \'<div class="error fade"><p>You need PHP v\' . WS_PLUGIN__QCACHE_MIN_PHP_VERSION . \'+ to use the Quick Cache plugin.</p></div>\';'));
			}
		else if (!version_compare (get_bloginfo ("version"), WS_PLUGIN__QCACHE_MIN_WP_VERSION, ">="))
			{
				add_action ("all_admin_notices", create_function ('', 'echo \'<div class="error fade"><p>You need WordPress® v\' . WS_PLUGIN__QCACHE_MIN_WP_VERSION . \'+ to use the Quick Cache plugin.</p></div>\';'));
			}
	}
?>