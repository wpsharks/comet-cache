<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__qcache_admin_css_js"))
	{
		class c_ws_plugin__qcache_admin_css_js
			{
				public static function menu_pages_css ()
					{
						if ($_GET["ws_plugin__qcache_menu_pages_css"]) /* Call inner function? */
							{
								return c_ws_plugin__qcache_admin_css_js_in::menu_pages_css ();
							}
					}
				/**/
				public static function menu_pages_js ()
					{
						if ($_GET["ws_plugin__qcache_menu_pages_js"]) /* Call inner function? */
							{
								return c_ws_plugin__qcache_admin_css_js_in::menu_pages_js ();
							}
					}
			}
	}
?>