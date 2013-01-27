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
if (!class_exists ("c_ws_plugin__qcache_garbage_collection"))
	{
		class c_ws_plugin__qcache_garbage_collection
			{
				/*
				Add a scheduled task for garbage collection.
				*/
				public static function add_garbage_collector ()
					{
						do_action ("ws_plugin__qcache_before_add_garbage_collector", get_defined_vars ());
						/**/
						if (!c_ws_plugin__qcache_garbage_collection::delete_garbage_collector ())
							{
								return apply_filters ("ws_plugin__qcache_add_garbage_collector", false, get_defined_vars ());
							}
						else if (function_exists ("wp_cron")) /* Otherwise, we can schedule. */
							{
								wp_schedule_event (strtotime ("+1 hour"), "hourly", "ws_plugin__qcache_garbage_collector__schedule");
								/**/
								do_action ("ws_plugin__qcache_during_add_garbage_collector", get_defined_vars ());
								/**/
								return apply_filters ("ws_plugin__qcache_add_garbage_collector", true, get_defined_vars ());
							}
						else /* It appears that WP-Cron is not available. */
							{
								return apply_filters ("ws_plugin__qcache_add_garbage_collector", false, get_defined_vars ());
							}
					}
				/*
				Delete scheduled tasks for garbage collection.
				*/
				public static function delete_garbage_collector ()
					{
						do_action ("ws_plugin__qcache_before_delete_garbage_collector", get_defined_vars ());
						/**/
						if (function_exists ("wp_cron")) /* If WP-Cron is available. */
							{
								wp_clear_scheduled_hook("ws_plugin__qcache_garbage_collector__schedule");
								/**/
								do_action ("ws_plugin__qcache_during_delete_garbage_collector", get_defined_vars ());
								/**/
								return apply_filters ("ws_plugin__qcache_delete_garbage_collector", true, get_defined_vars ());
							}
						else /* It appears that WP-Cron is not available. */
							{
								return apply_filters ("ws_plugin__qcache_delete_garbage_collector", false, get_defined_vars ());
							}
					}
				/*
				This runs the built-in garbage collector for Quick Cache.
				Attach to: add_action("ws_plugin__qcache_garbage_collector__schedule");
				*/
				public static function garbage_collector ()
					{
						do_action ("ws_plugin__qcache_before_garbage_collector", get_defined_vars ());
						/**/
						clearstatcache () . define ("QUICK_CACHE_ALLOWED", false); /* Cache NOT allowed here. */
						/**/
						@set_time_limit(900) . @ini_set ("memory_limit", apply_filters ("admin_memory_limit", WP_MAX_MEMORY_LIMIT)) . @ignore_user_abort (true);
						/**/
						do_action ("ws_plugin__qcache_before_garbage_collector_routines", get_defined_vars ());
						/**/
						if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "sem" && function_exists ("sem_get") && ($mutex = @sem_get (1976, 1, 0644 | IPC_CREAT, 1)) && @sem_acquire ($mutex))
							$mutex_method = "sem"; /* Recommended locking method. */
						/**/
						else if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "flock" && ($mutex = @fopen (WP_CONTENT_DIR . "/cache/qc-l-mutex.lock", "w")) && @flock ($mutex, LOCK_EX))
							$mutex_method = "flock";
						/**/
						if (($mutex && $mutex_method) && is_dir (WP_CONTENT_DIR . "/cache") && is_writable (WP_CONTENT_DIR . "/cache"))
							{
								$exp = strtotime ("-" . $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["expiration"] . " seconds");
								/**/
								foreach ((array)glob (WP_CONTENT_DIR . "/cache/qc-c-*") as $file)
									if ($file && $file !== "." && $file !== ".." && is_file ($file))
										if (filemtime ($file) < $exp)
											if (is_writable ($file))
												unlink($file);
								/**/
								if ($mutex_method === "sem")
									sem_release($mutex);
								/**/
								else if ($mutex_method === "flock")
									flock ($mutex, LOCK_UN);
								/**/
								do_action ("ws_plugin__qcache_during_garbage_collector", get_defined_vars ());
							}
						/**/
						do_action ("ws_plugin__qcache_after_garbage_collector", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>