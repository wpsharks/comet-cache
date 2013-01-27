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
if (!class_exists ("c_ws_plugin__qcache_installation"))
	{
		class c_ws_plugin__qcache_installation
			{
				/*
				Handles activation routines.
				*/
				public static function activate ()
					{
						do_action ("ws_plugin__qcache_before_activation", get_defined_vars ());
						/**/
						(!is_numeric (get_option ("ws_plugin__qcache_configured"))) ? update_option ("ws_plugin__qcache_configured", "0") : null;
						(!is_array (get_option ("ws_plugin__qcache_notices"))) ? update_option ("ws_plugin__qcache_notices", array ()) : null;
						(!is_array (get_option ("ws_plugin__qcache_options"))) ? update_option ("ws_plugin__qcache_options", array ()) : null;
						/**/
						if ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["configured"] && $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"])
							{
								if (c_ws_plugin__qcache_wp_cache::add_wp_cache ()) /* Add WP_CACHE to the config file. */
									if (c_ws_plugin__qcache_advanced_cache::add_advanced ()) /* Add the advanced-cache.php file. */
										if (c_ws_plugin__qcache_garbage_collection::add_garbage_collector ()) /* Add the garbage collector. */
											if (c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge (false, false, false)) /* Purge. */
												{
													$re_activated = true; /* Set this variable has having been re-activated successfully. */
													($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_enabled"]) ? c_ws_plugin__qcache_auto_cache::add_auto_cache_engine () : c_ws_plugin__qcache_auto_cache::delete_auto_cache_engine ();
													c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ('<strong>Quick Cache</strong> has been <strong>re-activated</strong> with the latest version. The cache has been reset automatically to avoid conflicts :-)', array ("blog|network:plugins.php", "blog|network:ws-plugin--qcache-options"));
												}
								/**/
								if (!$re_activated) /* Otherwise, we need to throw a warning up. The site owner needs to disable, and re-enable. */
									{
										c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ('<strong>Quick Cache</strong> Please go to <code>Quick Cache -> Config Options</code>. You\'ll need to disable, and then re-enable Quick Cache, to complete the upgrade process.', array ("blog|network:plugins.php", "blog|network:ws-plugin--qcache-options"), true);
									}
							}
						/**/
						update_option ("ws_plugin__qcache_activated_version", WS_PLUGIN__QCACHE_VERSION);
						/**/
						do_action ("ws_plugin__qcache_after_activation", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Handles de-activation / cleanup routines.
				*/
				public static function deactivate ()
					{
						do_action ("ws_plugin__qcache_before_deactivation", get_defined_vars ());
						/**/
						c_ws_plugin__qcache_wp_cache::delete_wp_cache ();
						c_ws_plugin__qcache_advanced_cache::delete_advanced ();
						c_ws_plugin__qcache_garbage_collection::delete_garbage_collector ();
						c_ws_plugin__qcache_auto_cache::delete_auto_cache_engine ();
						/**/
						if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["run_deactivation_routines"])
							{
								delete_option ("ws_plugin__qcache_activated_version");
								delete_option ("ws_plugin__qcache_configured");
								delete_option ("ws_plugin__qcache_notices");
								delete_option ("ws_plugin__qcache_options");
							}
						/**/
						c_ws_plugin__qcache_purging_routines::purge_cache_dir (false, false, false);
						/**/
						do_action ("ws_plugin__qcache_after_deactivation", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>