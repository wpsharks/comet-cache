<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if(!class_exists("c_ws_plugin__qcache_admin_css_js_in"))
	{
		class c_ws_plugin__qcache_admin_css_js_in
			{
				/*
				Function that outputs the css for menu pages.
				Attach to: add_action("init");
				*/
				public static function menu_pages_css()
					{
						do_action("ws_plugin__qcache_before_menu_pages_css", get_defined_vars());
						/**/
						if($_GET["ws_plugin__qcache_menu_pages_css"] && is_user_logged_in() && current_user_can("install_plugins"))
							{
								status_header(200); /* 200 OK status header. */
								/**/
								header("Content-Type: text/css; charset=utf-8");
								header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("-1 week"))." GMT");
								header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
								header("Cache-Control: no-cache, must-revalidate, max-age=0");
								header("Pragma: no-cache");
								/**/
								eval('while (@ob_end_clean ());'); /* Clean buffers. */
								/**/
								$u = $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"];
								$i = $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]."/images";
								/**/
								ob_start("c_ws_plugin__qcache_utils_css::compress_css");
								/**/
								include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages.css";
								/**/
								echo "\n"; /* Add a line break before inclusion of this file. */
								/**/
								@include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-s.css";
								/**/
								do_action("ws_plugin__qcache_during_menu_pages_css", get_defined_vars());
								/**/
								exit(); /* Clean exit. */
							}
						/**/
						do_action("ws_plugin__qcache_after_menu_pages_css", get_defined_vars());
					}
				/*
				Function that outputs the js for menu pages.
				Attach to: add_action("init");
				*/
				public static function menu_pages_js()
					{
						do_action("ws_plugin__qcache_before_menu_pages_js", get_defined_vars());
						/**/
						if($_GET["ws_plugin__qcache_menu_pages_js"] && is_user_logged_in() && current_user_can("install_plugins"))
							{
								status_header(200); /* 200 OK status header. */
								/**/
								header("Content-Type: application/x-javascript; charset=utf-8");
								header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("-1 week"))." GMT");
								header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
								header("Cache-Control: no-cache, must-revalidate, max-age=0");
								header("Pragma: no-cache");
								/**/
								eval('while (@ob_end_clean ());'); /* Clean buffers. */
								/**/
								$u = $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"];
								$i = $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]."/images";
								/**/
								include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-min.js";
								/**/
								echo "\n"; /* Add a line break before inclusion of this file. */
								/**/
								@include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-s-min.js";
								/**/
								do_action("ws_plugin__qcache_during_menu_pages_js", get_defined_vars());
								/**/
								exit(); /* Clean exit. */
							}
						/**/
						do_action("ws_plugin__qcache_after_menu_pages_js", get_defined_vars());
					}
			}
	}
?>