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
if (!class_exists ("c_ws_plugin__qcache_check_activation"))
	{
		class c_ws_plugin__qcache_check_activation
			{
				/*
				Check existing installations that have not been re-activated.
				Attach to: add_action("admin_init");
				*/
				public static function check () /* Re-activated? */
					{
						$v = get_option ("ws_plugin__qcache_activated_version");
						/**/
						if (!$v || !version_compare ($v, WS_PLUGIN__QCACHE_VERSION, ">="))
							c_ws_plugin__qcache_installation::activate ();
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>