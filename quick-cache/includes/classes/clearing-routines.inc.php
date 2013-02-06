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
if (!class_exists ("c_ws_plugin__qcache_clearing_routines"))
	{
		class c_ws_plugin__qcache_clearing_routines
			{
				/*
				Function clears cache on creations/deletions.
				Attach to: add_action("delete_post");
				Attach to: add_action("save_post");
				Attach to: add_action("edit_post");
				*/
				public static function clear_on_post_page_creations_deletions ($id = FALSE)
					{
						static $once = false; /* Only clear "all" once. */
						global $pagenow; /* This variable holds the current page filename. */
						global $current_site, $current_blog; /* Need these for Multisite details. */
						/**/
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__qcache_before_clear_on_post_page_creations_deletions", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if (is_blog_admin () && in_array ($pagenow, ($pages = array ("edit.php", "post.php", "post-new.php"))))
							{
								if ($id && preg_match ("/^single/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"]))
									{
										if (($url = get_permalink ($id)) && is_array ($parse = c_ws_plugin__qcache_utils_urls::parse_url ($url)) && ($host_uri = preg_replace ("/^http(s?)\:\/\//i", "", $url)))
											{
												$host_uri = preg_replace ("/^(" . preg_quote ($parse["host"], "/") . ")(\:[0-9]+)(\/)/i", "$1$3", $host_uri);
												/**/
												list ($cache) = (array)glob (WP_CONTENT_DIR . "/cache/qc-c-*-" . md5 ($host_uri) . "-*"); /* Match md5_2. */
												/**/
												if ($cache) /* If a cache file exists for this $host_uri. */
													{
														if (is_writable ($cache) && unlink ($cache))
															{
																if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
																	{
																		$notice = 'Quick Cache updated: <code>' . esc_html ($host_uri) . '</code> automatically :-)';
																		c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
																	}
															}
														else /* Notify site owners when their /cache directory files are NOT writable. */
															{
																if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
																	{
																		$notice = 'Quick Cache was unable to update: <code>' . esc_html ($host_uri) . '</code>. File not writable.';
																		c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages, true);
																	}
															}
													}
											}
										/**/
										if (preg_match ("/^single-fp$/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"]))
											{
												if (($url = site_url ("/")) && is_array ($parse = c_ws_plugin__qcache_utils_urls::parse_url ($url)) && ($host_uri = preg_replace ("/^http(s?)\:\/\//i", "", $url)))
													{
														$host_uri = preg_replace ("/^(" . preg_quote ($parse["host"], "/") . ")(\:[0-9]+)(\/)/i", "$1$3", $host_uri);
														/**/
														list ($cache) = (array)glob (WP_CONTENT_DIR . "/cache/qc-c-*-" . md5 ($host_uri) . "-*"); /* Match md5_2. */
														/**/
														if ($cache) /* If a cache file exists for this $host_uri. */
															{
																if (is_writable ($cache) && unlink ($cache))
																	{
																		if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
																			{
																				$notice = 'Quick Cache updated: <code>' . esc_html ($host_uri) . '</code> automatically :-)';
																				c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
																			}
																	}
																else /* Notify site owners when their /cache directory files are NOT writable. */
																	{
																		if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
																			{
																				$notice = 'Quick Cache was unable to update: <code>' . esc_html ($host_uri) . '</code>. File not writable.';
																				c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages, true);
																			}
																	}
															}
													}
											}
										/**/
										do_action ("ws_plugin__qcache_during_clear_on_post_page_creations_deletions", get_defined_vars ());
									}
								else if (!$once && preg_match ("/^all$/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"]) && ($once = true))
									{
										if (is_multisite () && is_object ($current_blog) && $current_blog->blog_id)
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ($current_blog);
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Blog# <code>' . esc_html ($current_blog->blog_id) . '</code> : Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										else /* Otherwise, handle this normally. We are NOT in Multisite Mode. */
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ();
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										/**/
										do_action ("ws_plugin__qcache_during_clear_on_post_page_creations_deletions", get_defined_vars ());
									}
							}
						/**/
						do_action ("ws_plugin__qcache_after_clear_on_post_page_creations_deletions", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Function clears cache on various creations/deletions.
				Attach to: add_action("create_term");
				Attach to: add_action("edit_terms");
				Attach to: add_action("delete_term");
				Attach to: add_action("add_link");
				Attach to: add_action("edit_link");
				Attach to: add_action("delete_link");
				*/
				public static function clear_on_creations_deletions ($term_or_link_id = FALSE)
					{
						static $once = false; /* Only clear once. */
						global $pagenow; /* This variable holds the current page filename. */
						global $current_site, $current_blog; /* Need these for Multisite details. */
						/**/
						eval ('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action ("ws_plugin__qcache_before_clear_on_creations_deletions", get_defined_vars ());
						unset ($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if (is_blog_admin () && in_array ($pagenow, ($pages = array ("edit-tags.php", "link-manager.php", "link.php", "link-add.php", "edit-link-categories.php", "link-category.php"))))
							{
								if (!$once && preg_match ("/^all$/", $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]["clear_on_update"]) && ($once = true))
									{
										if (is_multisite () && is_object ($current_blog) && $current_blog->blog_id)
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ($current_blog);
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Blog# <code>' . esc_html ($current_blog->blog_id) . '</code> : Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										else /* Otherwise, handle this normally. We are NOT in Multisite Mode. */
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ();
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										/**/
										do_action ("ws_plugin__qcache_during_clear_on_creations_deletions", get_defined_vars ());
									}
							}
						/**/
						do_action ("ws_plugin__qcache_after_clear_on_creations_deletions", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Function for clearing cache on theme changes.
				Attach to: add_action("switch_theme");
				
				The cache is always reset after a theme change,
				no matter what setting has been configured. A theme
				being changed will always require a cache reset!
				*/
				public static function clear_on_theme_changes ()
					{
						static $once = false; /* Only clear once. */
						global $pagenow; /* Holds the current page filename. */
						global $current_site, $current_blog; /* Need these for Multisite details. */
						/**/
						do_action ("ws_plugin__qcache_before_clear_on_theme_changes", get_defined_vars ());
						/**/
						if (is_blog_admin () && in_array ($pagenow, ($pages = array ("themes.php"))))
							{
								if (!$once && ($once = true)) /* Only clear once. */
									{
										if (is_multisite () && is_object ($current_blog) && $current_blog->blog_id)
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ($current_blog);
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Blog# <code>' . esc_html ($current_blog->blog_id) . '</code> : Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										else /* Otherwise, handle this normally. We are NOT in Multisite Mode. */
											{
												c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge ();
												/**/
												if (!is_multisite () || !c_ws_plugin__qcache_utils_conds::is_multisite_farm () || is_main_site ())
													{
														$notice = 'Quick Cache reset automatically to avoid conflicts :-)';
														c_ws_plugin__qcache_admin_notices::enqueue_admin_notice ($notice, $pages);
													}
											}
										/**/
										do_action ("ws_plugin__qcache_during_clear_on_theme_changes", get_defined_vars ());
									}
							}
						/**/
						do_action ("ws_plugin__qcache_after_clear_on_theme_changes", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Function for clearing cache via ajax.
				Attach to: add_action("wp_ajax_ws_plugin__qcache_ajax_clear");
				*/
				public static function ajax_clear ()
					{
						global $current_site, $current_blog; /* Multisite details. */
						/**/
						do_action ("ws_plugin__qcache_before_ajax_clear", get_defined_vars ());
						/**/
						status_header (200); /* Send a 200 OK status header. */
						header ("Content-Type: text/plain; charset=utf-8"); /* Content-Type text/plain with UTF-8. */
						eval ('while (@ob_end_clean ());'); /* End/clean all output buffers that may or may not exist. */
						/**/
						if (($nonce = $_POST["ws_plugin__qcache_ajax_clear"]) && wp_verify_nonce ($nonce, "ws-plugin--qcache-ajax-clear"))
							{
								if (is_multisite () && !is_main_site () && is_object ($current_blog) && $current_blog->blog_id)
									{
										c_ws_plugin__qcache_purging_routines::purge_cache_dir ($current_blog);
										/**/
										$status = 'Cleared ( this blog )'; /* Indicate "this blog" to the Super Admin. */
										/**/
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').css ('background-image', 'url(\'" . c_ws_plugin__qcache_utils_strings::esc_js_sq ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"], 3) . "/images/ajax-clear.png\')');";
										echo "setTimeout (function (){ jQuery ('input#ws-plugin--qcache-ajax-clear').val ('Clear Cache'); }, 2000);";
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').val ('" . c_ws_plugin__qcache_utils_strings::esc_js_sq ($status) . "');";
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').each (function (){ this.blur(); });";
									}
								else /* Otherwise, handle this normally. We are NOT in Multisite Mode. */
									{
										c_ws_plugin__qcache_purging_routines::purge_cache_dir ();
										/**/
										$status = (is_multisite ()) ? 'Cleared ( all blogs )' : '( Cleared )';
										/**/
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').css ('background-image', 'url(\'" . c_ws_plugin__qcache_utils_strings::esc_js_sq ($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"], 3) . "/images/ajax-clear.png\')');";
										echo "setTimeout (function (){ jQuery ('input#ws-plugin--qcache-ajax-clear').val ('Clear Cache'); }, 2000);";
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').val ('" . c_ws_plugin__qcache_utils_strings::esc_js_sq ($status) . "');";
										echo "jQuery ('input#ws-plugin--qcache-ajax-clear').each (function (){ this.blur(); });";
									}
								/**/
								do_action ("ws_plugin__qcache_during_ajax_clear", get_defined_vars ());
							}
						/**/
						do_action ("ws_plugin__qcache_after_ajax_clear", get_defined_vars ());
						/**/
						exit (); /* Clean exit. */
					}
			}
	}
?>