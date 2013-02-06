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
if (!class_exists ("c_ws_plugin__qcache_extend_cron"))
	{
		class c_ws_plugin__qcache_extend_cron
			{
				/*
				Extends the WP_Cron schedules to support 5 minute intervals.
				Attach to: add_filter("cron_schedules");
				*/
				public static function extend_cron_schedules ($schedules = array ())
					{
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__qcache_before_extend_cron_schedules", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						$array = array ("every5m" => array ("interval" => 300, "display" => "Every 5 Minutes"));
						/**/
						return apply_filters ("ws_plugin__qcache_extend_cron_schedules", array_merge ($array, $schedules), get_defined_vars ());
					}
			}
	}
?>