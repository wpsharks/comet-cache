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
if (!class_exists ("c_ws_plugin__qcache_purging_routines"))
	{
		class c_ws_plugin__qcache_purging_routines
			{
				/*
				Schedules an immediate purge of the cache directory.
				By default, this includes all sites; and leaves locks / logs.
				*/
				public static function schedule_cache_dir_purge ($qc_c_blog = FALSE, $leave_qc_l_locks = TRUE, $leave_qc_l_logs = TRUE)
					{
						static $once; /* Only schedule once. */
						/**/
						do_action ("ws_plugin__qcache_before_schedule_cache_dir_purge", get_defined_vars ());
						/**/
						if (!isset ($once)) /* No need to duplicate this. */
							{
								if (function_exists ("wp_cron") && ($once = true)) /* If available. */
									{
										$args = array ($qc_c_blog, $leave_qc_l_locks, $leave_qc_l_logs);
										/**/
										wp_schedule_single_event (time (), "ws_plugin__qcache_purge_cache_dir__schedule", $args);
										/**/
										do_action ("ws_plugin__qcache_during_schedule_cache_dir_purge", get_defined_vars ());
									}
								else /* WP-Cron is not available. */
									{
										$once = false;
									}
							}
						/**/
						return apply_filters ("ws_plugin__qcache_schedule_cache_dir_purge", $once, get_defined_vars ());
					}
				/*
				Performs an immediate purge of the cache directory.
				By default, this includes all blogs; and leaves locks / logs.
				Attach to: add_action("ws_plugin__qcache_purge_cache_dir__schedule");
				*/
				public static function purge_cache_dir ($qc_c_blog = FALSE, $leave_qc_l_locks = TRUE, $leave_qc_l_logs = TRUE)
					{
						do_action ("ws_plugin__qcache_before_purge_cache_dir", get_defined_vars ());
						/**/
						clearstatcache () . define ("QUICK_CACHE_ALLOWED", false); /* Cache NOT allowed here. */
						/**/
						@set_time_limit(900) . @ini_set ("memory_limit", apply_filters ("admin_memory_limit", WP_MAX_MEMORY_LIMIT)) . @ignore_user_abort (true);
						/**/
						do_action ("ws_plugin__qcache_before_purge_cache_dir_routines", get_defined_vars ());
						/**/
						if (is_dir (WP_CONTENT_DIR . "/cache") && is_writable (WP_CONTENT_DIR . "/cache"))
							{
								$glob = "qc-c-*"; /* Default glob; all cache files. */
								/**/
								if (is_object ($blog = $qc_c_blog) && $blog->domain && $blog->path)
									$glob = "qc-c-*-*-" . md5 ($blog->domain . $blog->path);
								/**/
								foreach ((array)glob (WP_CONTENT_DIR . "/cache/" . $glob) as $file)
									if ($file && $file !== "." && $file !== ".." && is_file ($file))
										$error = (!is_writable ($file) || !unlink ($file)) ? true : $error;
								/**/
								if (!$leave_qc_l_locks && ($glob = "qc-l-*.lock"))
									foreach ((array)glob (WP_CONTENT_DIR . "/cache/" . $glob) as $file)
										if ($file && $file !== "." && $file !== ".." && is_file ($file))
											$error = (!is_writable ($file) || !unlink ($file)) ? true : $error;
								/**/
								if (!$leave_qc_l_logs && ($glob = "qc-l-*.log"))
									foreach ((array)glob (WP_CONTENT_DIR . "/cache/" . $glob) as $file)
										if ($file && $file !== "." && $file !== ".." && is_file ($file))
											$error = (!is_writable ($file) || !unlink ($file)) ? true : $error;
								/**/
								do_action ("ws_plugin__qcache_during_purge_cache_dir", get_defined_vars ());
								/**/
								if ($error) /* Failed, the purging routine ran into a writable error somewhere. */
									{
										return apply_filters ("ws_plugin__qcache_purge_cache_dir", false, get_defined_vars ());
									}
								else /* Completed successfully. The cache directory was purged as requested. */
									{
										return apply_filters ("ws_plugin__qcache_purge_cache_dir", true, get_defined_vars ());
									}
							}
						else if (is_dir (WP_CONTENT_DIR . "/cache") && !is_writable (WP_CONTENT_DIR . "/cache"))
							{
								return apply_filters ("ws_plugin__qcache_purge_cache_dir", false, get_defined_vars ());
							}
						else /* Defaults to true for deletion. */
							{
								return apply_filters ("ws_plugin__qcache_purge_cache_dir", true, get_defined_vars ());
							}
					}
			}
	}
?>