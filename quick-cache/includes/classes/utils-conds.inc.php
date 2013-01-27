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
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__qcache_utils_conds"))
	{
		class c_ws_plugin__qcache_utils_conds
			{
				public static function is_network_admin ()
					{
						return is_network_admin ();
					}
				/**/
				public static function is_blog_admin ()
					{
						return is_blog_admin ();
					}
				/**/
				public static function is_user_admin ()
					{
						return is_user_admin ();
					}
				/**/
				public static function is_multisite_farm ()
					{
						return (is_multisite () && defined ("MULTISITE_FARM") && MULTISITE_FARM);
					}
			}
	}
?>