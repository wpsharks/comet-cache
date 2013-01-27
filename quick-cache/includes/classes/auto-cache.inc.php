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
if (!class_exists ("c_ws_plugin__qcache_auto_cache"))
	{
		class c_ws_plugin__qcache_auto_cache
			{
				/*
				Add a scheduled task for the auto-cache engine.
				*/
				public static function add_auto_cache_engine ()
					{
						do_action ("ws_plugin__qcache_before_add_auto_cache_engine", get_defined_vars ());
						/**/
						if (!c_ws_plugin__qcache_auto_cache::delete_auto_cache_engine ())
							{
								return apply_filters ("ws_plugin__qcache_add_auto_cache_engine", false, get_defined_vars ());
							}
						else if (function_exists ("wp_cron")) /* Otherwise, we can schedule. */
							{
								wp_schedule_event (time (), "every5m", "ws_plugin__qcache_auto_cache_engine__schedule");
								/**/
								do_action ("ws_plugin__qcache_during_add_auto_cache_engine", get_defined_vars ());
								/**/
								return apply_filters ("ws_plugin__qcache_add_auto_cache_engine", true, get_defined_vars ());
							}
						else /* It would appear that WP-Cron is not available. */
							{
								return apply_filters ("ws_plugin__qcache_add_auto_cache_engine", false, get_defined_vars ());
							}
					}
				/*
				Delete scheduled tasks for the auto-cache engine.
				*/
				public static function delete_auto_cache_engine ()
					{
						do_action ("ws_plugin__qcache_before_delete_auto_cache_engine", get_defined_vars ());
						/**/
						if (function_exists ("wp_cron")) /* If WP-Cron is available. */
							{
								wp_clear_scheduled_hook("ws_plugin__qcache_auto_cache_engine__schedule");
								/**/
								do_action ("ws_plugin__qcache_during_delete_auto_cache_engine", get_defined_vars ());
								/**/
								return apply_filters ("ws_plugin__qcache_delete_auto_cache_engine", true, get_defined_vars ());
							}
						else /* It would appear that WP-Cron is not available. */
							{
								return apply_filters ("ws_plugin__qcache_delete_auto_cache_engine", false, get_defined_vars ());
							}
					}
				/*
				Runs the Auto-Cache Engine. ( this must be enabled first )
				The Auto-Cache Engine keeps an entire site cached automatically.
				Attach to: add_action("ws_plugin__qcache_auto_cache_engine__schedule");
				*/
				public static function auto_cache_engine ()
					{
						do_action ("ws_plugin__qcache_before_auto_cache_engine", get_defined_vars ());
						/**/
						if ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["configured"] && $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["enabled"])
							if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_enabled"] && $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_agent"])
								if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_sitemap_url"] || $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_additional_urls"])
									if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["expiration"] >= 3600)
										{
											$log = ""; /* Initialize log to an empty string value here. */
											/**/
											clearstatcache () . define ("QUICK_CACHE_ALLOWED", false); /* Cache NOT allowed here. */
											/**/
											@set_time_limit(900) . @ini_set ("memory_limit", apply_filters ("admin_memory_limit", WP_MAX_MEMORY_LIMIT)) . @ignore_user_abort (true);
											/**/
											do_action ("ws_plugin__qcache_before_auto_cache_engine_routines", get_defined_vars ());
											/**/
											if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "sem" && function_exists ("sem_get") && ($mutex = @sem_get (1977, 1, 0644 | IPC_CREAT, 1)) && @sem_acquire ($mutex))
												$mutex_method = "sem"; /* Recommended locking method. */
											/**/
											else if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["use_flock_or_sem"] === "flock" && ($mutex = @fopen (WP_CONTENT_DIR . "/cache/qc-l-ac.mutex.lock", "w")) && @flock ($mutex, LOCK_EX))
												$mutex_method = "flock";
											/**/
											if ($mutex && $mutex_method && is_array ($urls = array ())) /* Initializes the array of URLs. */
												{
													eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
													do_action ("ws_plugin__qcache_during_auto_cache_engine_before", get_defined_vars ());
													unset ($__refs, $__v); /* Unset defined __refs, __v. */
													/**/
													if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_sitemap_url"]) /* Sitemap. */
														{
															if ($sitemap = c_ws_plugin__qcache_utils_urls::remote ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_sitemap_url"]))
																{
																	preg_match_all ("/\<loc\>(.+?)\<\/loc\>/i", $sitemap, $sitemap_matches);
																	if (is_array ($sitemap_matches[1]) && !empty ($sitemap_matches[1]))
																		foreach ($sitemap_matches[1] as $sitemap_match)
																			if ($url = trim ($sitemap_match))
																				$urls[] = $url;
																}
														}
													/**/
													if ($GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_additional_urls"]) /* Additional URLs entered manually. */
														{
															foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_additional_urls"]) as $additional)
																if ($url = trim ($additional))
																	$urls[] = $url;
														}
													/**/
													eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
													do_action ("ws_plugin__qcache_during_auto_cache_engine_before_urls", get_defined_vars ());
													unset ($__refs, $__v); /* Unset defined __refs, __v. */
													/**/
													if (($urls = array_unique ($urls)) && !empty ($urls) && shuffle ($urls))
														{
															foreach ($urls as $url) /* Go through URLs now, and attempt to visit each of them; forcing an auto cache. */
																{
																	if (is_array ($parse = c_ws_plugin__qcache_utils_urls::parse_url ($url)) && ($host_uri = preg_replace ("/^http(s?)\:\/\//i", "", $url)))
																		{
																			$host_uri = preg_replace ("/^(" . preg_quote ($parse["host"], "/") . ")(\:[0-9]+)(\/)/i", "$1$3", $host_uri);
																			/**/
																			list ($cache) = (array)glob (WP_CONTENT_DIR . "/cache/qc-c-*-" . md5 ($host_uri) . "-*"); /* Match md5_2. */
																			/**/
																			if (!$cache || filemtime ($cache) < strtotime ("-" . $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["expiration"] . " seconds"))
																				{
																					c_ws_plugin__qcache_utils_urls::remote ($url, false, array ("timeout" => 0.01, "blocking" => false, "user-agent" => $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_agent"] . " + Quick Cache ( Auto-Cache Engine )"));
																					/**/
																					$log .= date ("M j, Y, g:i a T") . " / Auto-Cached: " . $url . "\n"; /* Keeps a running log of each URL being processed. */
																					/**/
																					if (($processed = (int)$processed + 1) >= $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["auto_cache_max_processes"])
																						break;
																					/**/
																					else if ($processed >= 25) /* Hard-coded maximum; for security. */
																						break;
																				}
																		}
																}
														}
													/**/
													eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
													do_action ("ws_plugin__qcache_during_auto_cache_engine_before_log", get_defined_vars ());
													unset ($__refs, $__v); /* Unset defined __refs, __v. */
													/**/
													if ($log && (is_dir (WP_CONTENT_DIR . "/cache") || is_writable (WP_CONTENT_DIR)))
														{
															if (!is_dir (WP_CONTENT_DIR . "/cache"))
																mkdir (WP_CONTENT_DIR . "/cache", 0777, true);
															/**/
															clearstatcache (); /* Clear stat cache before next routine. */
															/**/
															if (is_dir (WP_CONTENT_DIR . "/cache") && is_writable (WP_CONTENT_DIR . "/cache"))
																{
																	$auto_cache_log = WP_CONTENT_DIR . "/cache/qc-l-auto-cache.log";
																	/**/
																	if (file_exists ($auto_cache_log) && filesize ($auto_cache_log) > 2097152)
																		if (is_writable ($auto_cache_log)) /* This is a 2MB log rotation ^. */
																			unlink($auto_cache_log); /* Resets the log. */
																	/**/
																	clearstatcache (); /* Clear stat cache before next routine. */
																	/**/
																	if (!file_exists ($auto_cache_log) || is_writable ($auto_cache_log))
																		file_put_contents ($auto_cache_log, $log, FILE_APPEND);
																}
														}
													/**/
													if ($mutex_method === "sem")
														sem_release($mutex);
													/**/
													else if ($mutex_method === "flock")
														flock ($mutex, LOCK_UN);
													/**/
													do_action ("ws_plugin__qcache_during_auto_cache_engine_after", get_defined_vars ());
												}
											/**/
											do_action ("ws_plugin__qcache_during_auto_cache_engine", get_defined_vars ());
										}
						/**/
						do_action ("ws_plugin__qcache_after_auto_cache_engine", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>