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
if(!class_exists("c_ws_plugin__qcache_menu_pages"))
	{
		class c_ws_plugin__qcache_menu_pages
			{
				/*
				Function that saves all options from any page.
				Options can also be passed in directly.
					Can also be self-verified.
				*/
				public static function update_all_options($new_options = FALSE, $verified = FALSE, $update_other = TRUE, $display_notices = TRUE, $enqueue_notices = FALSE, $request_refresh = FALSE)
					{
						do_action("ws_plugin__qcache_before_update_all_options", get_defined_vars()); /* If you use this Hook, be sure to use `wp_verify_nonce()`. */
						/**/
						if($verified || (($nonce = $_POST["ws_plugin__qcache_options_save"]) && wp_verify_nonce($nonce, "ws-plugin--qcache-options-save")))
							{
								if(!is_multisite() || (is_main_site() && is_super_admin())) /* If Multisite, this MUST be ( Main Site / Super Admin ). */
									{
										$options = $GLOBALS["WS_PLUGIN__"]["qcache"]["o"]; /* Here we get all of the existing options. */
										$new_options = (is_array($new_options)) ? $new_options : ((!empty($_POST)) ? stripslashes_deep($_POST) : array());
										$new_options = c_ws_plugin__qcache_utils_strings::trim_deep($new_options);
										/**/
										foreach((array)$new_options as $key => $value) /* Looking for relevant keys. */
											if(preg_match("/^".preg_quote("ws_plugin__qcache_", "/")."/", $key))
												/**/
												if($key === "ws_plugin__qcache_configured") /* Configured. */
													{
														update_option("ws_plugin__qcache_configured", $value);
														$GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["configured"] = $value;
													}
												else /* Place this option into the array. Remove ws_plugin__qcache_. */
													{
														(is_array($value)) ? array_shift($value) : null; /* Arrays should be padded. */
														$key = preg_replace("/^".preg_quote("ws_plugin__qcache_", "/")."/", "", $key);
														$options[$key] = $value; /* Overriding a possible existing option. */
													}
										/**/
										$options["options_version"] = (string)($options["options_version"] + 0.001);
										$options = ws_plugin__qcache_configure_options_and_their_defaults($options);
										/**/
										eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
										do_action("ws_plugin__qcache_during_update_all_options", get_defined_vars());
										unset($__refs, $__v); /* Unset defined __refs, __v. */
										/**/
										update_option("ws_plugin__qcache_options", $options);
										/**/
										$updated_all_options = true; /* Flag/indication. */
										/**/
										if($update_other && $options["enabled"])
											{
												if(c_ws_plugin__qcache_wp_cache::add_wp_cache()) /* Add WP_CACHE to the config file. */
													if(c_ws_plugin__qcache_advanced_cache::add_advanced()) /* Add the advanced-cache.php file. */
														if(c_ws_plugin__qcache_garbage_collection::add_garbage_collector()) /* Add the garbage collector. */
															if(c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge()) /* Purge the cache. */
																{
																	$enabled = true; /* Mark this variable as enabled successfully. */
																	($options["auto_cache_enabled"]) ? c_ws_plugin__qcache_auto_cache::add_auto_cache_engine() : c_ws_plugin__qcache_auto_cache::delete_auto_cache_engine();
																	if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Options saved</strong>, and the cache was reset to avoid conflicts.'.(($request_refresh) ? ' Please <a href="'.esc_attr($_SERVER["REQUEST_URI"]).'">refresh</a>.' : '').''))
																		($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*") : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice);
																}
												/**/
												if(!$enabled) /* Otherwise, we need to throw a warning up. The site owner needs to try again. */
													{
														if(!function_exists("wp_cron")) /* Special warning for lack of the wp_cron function. */
															{
																if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Error:</strong> Could NOT enable Quick Cache. Your installation of WordPress® is missing the <code>wp_cron</code> function. Some web hosts disable this intentionally. Please contact your web host for assistance.'))
																	($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*", true) : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice, true);
															}
														else /* Otherwise, the error must have something to do with file/directory permissions. */
															{
																if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Error:</strong> Could NOT enable Quick Cache. Please check permissions on <code>/wp-config.php</code>, <code>/wp-content/</code> and <code>/wp-content/cache/</code>. Permissions need to be <code>755</code> or higher.'))
																	($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*", true) : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice, true);
															}
													}
											}
										/**/
										else if($update_other && !$options["enabled"])
											{
												if(c_ws_plugin__qcache_wp_cache::delete_wp_cache()) /* Delete WP_CACHE from the config file. */
													if(c_ws_plugin__qcache_advanced_cache::delete_advanced()) /* Delete the advanced-cache.php file. */
														if(c_ws_plugin__qcache_garbage_collection::delete_garbage_collector()) /* Delete the garbage collector. */
															if(c_ws_plugin__qcache_purging_routines::schedule_cache_dir_purge()) /* Purge the cache. */
																{
																	$disabled = true; /* Mark this variable as disabled successfully. */
																	c_ws_plugin__qcache_auto_cache::delete_auto_cache_engine(); /* Delete Auto-Cache. */
																	if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Options saved.</strong> Quick Cache disabled.'.(($request_refresh) ? ' Please <a href="'.esc_attr($_SERVER["REQUEST_URI"]).'">refresh</a>.' : '').''))
																		($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*") : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice);
																}
												/**/
												if(!$disabled) /* Otherwise, we need to throw a warning up. The site owner needs to try again. */
													{
														if(!function_exists("wp_cron")) /* Special warning for lack of the wp_cron function. */
															{
																if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Error:</strong> Could NOT disable Quick Cache. Your installation of WordPress® is missing the <code>wp_cron</code> function. Some web hosts disable this intentionally. Please contact your web host for assistance.'))
																	($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*", true) : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice, true);
															}
														else /* Otherwise, the error must have something to do with file/directory permissions. */
															{
																if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Error:</strong> Could NOT disable Quick Cache. Please check permissions on <code>/wp-config.php</code>, <code>/wp-content/</code> and <code>/wp-content/cache/</code>. Permissions need to be <code>755</code> or higher.'))
																	($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*", true) : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice, true);
															}
													}
											}
										else if(!$update_other) /* Not updating anything else. We just need to display a notice that options have been saved successfully */
											{
												if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = '<strong>Options saved.'.(($request_refresh) ? ' Please <a href="'.esc_attr($_SERVER["REQUEST_URI"]).'">refresh</a>.' : '').'</strong>'))
													($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*") : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice);
											}
									}
								else /* Else, a security warning needs to be issued. Only Super Administrators are allowed to modify Quick Cache ( operating on the Main Site ). */
									{
										if(($display_notices === true || in_array("success", (array)$display_notices)) && ($notice = 'Quick Cache can ONLY be modified by a Super Administrator, while operating on the Main Site.'))
											($enqueue_notices === true || in_array("success", (array)$enqueue_notices)) ? c_ws_plugin__qcache_admin_notices::enqueue_admin_notice($notice, "*:*", true) : c_ws_plugin__qcache_admin_notices::display_admin_notice($notice, true);
									}
							}
						/**/
						do_action("ws_plugin__qcache_after_update_all_options", get_defined_vars());
						/**/
						return $updated_all_options; /* Return status update. */
					}
				/*
				Add the options menus & sub-menus.
				Attach to: add_action("admin_menu");
				*/
				public static function add_admin_options()
					{
						do_action("ws_plugin__qcache_before_add_admin_options", get_defined_vars());
						/**/
						if(!is_multisite() || (is_multisite() && is_main_site() && is_super_admin()))
							{
								add_filter("plugin_action_links", "c_ws_plugin__qcache_menu_pages::_add_settings_link", 10, 2);
								/**/
								if(apply_filters("ws_plugin__qcache_during_add_admin_options_create_menu_items", true, get_defined_vars()))
									{
										add_menu_page("Quick Cache", "Quick Cache", "install_plugins", "ws-plugin--qcache-options", "c_ws_plugin__qcache_menu_pages::options_page");
										/**/
										if(apply_filters("ws_plugin__qcache_during_add_admin_options_add_options_page", true, get_defined_vars()))
											add_submenu_page("ws-plugin--qcache-options", "Quick Cache Config Options", "Config Options", "install_plugins", "ws-plugin--qcache-options", "c_ws_plugin__qcache_menu_pages::options_page");
										/**/
										if(apply_filters("ws_plugin__qcache_during_add_admin_options_add_info_page", true, get_defined_vars()))
											add_submenu_page("ws-plugin--qcache-options", "Quick Cache Info", "Quick Cache Info", "install_plugins", "ws-plugin--qcache-info", "c_ws_plugin__qcache_menu_pages::info_page");
									}
							}
						else /* Else we need to hide Quick Cache from the plugins menu. */
							{
								add_filter("all_plugins", "c_ws_plugin__qcache_menu_pages::_hide_from_plugins_menu");
							}
						/**/
						do_action("ws_plugin__qcache_after_add_admin_options", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Add the options menus & sub-menus.
				Attach to: add_action("network_admin_menu");
				*/
				public static function add_network_admin_options()
					{
						do_action("ws_plugin__qcache_before_add_network_admin_options", get_defined_vars());
						/**/
						if(is_multisite() && is_main_site() && is_super_admin()) /* Only for Multisite Networking. */
							{
								if(apply_filters("ws_plugin__qcache_during_add_network_admin_options_create_menu_items", true, get_defined_vars()))
									{
										add_menu_page("Quick Cache", "Quick Cache", "install_plugins", "ws-plugin--qcache-options", "c_ws_plugin__qcache_menu_pages::options_page");
										/**/
										if(apply_filters("ws_plugin__qcache_during_add_network_admin_options_add_options_page", true, get_defined_vars()))
											add_submenu_page("ws-plugin--qcache-options", "Quick Cache Config Options", "Config Options", "install_plugins", "ws-plugin--qcache-options", "c_ws_plugin__qcache_menu_pages::options_page");
										/**/
										if(apply_filters("ws_plugin__qcache_during_add_network_admin_options_add_info_page", true, get_defined_vars()))
											add_submenu_page("ws-plugin--qcache-options", "Quick Cache Info", "Quick Cache Info", "install_plugins", "ws-plugin--qcache-info", "c_ws_plugin__qcache_menu_pages::info_page");
									}
							}
						/**/
						do_action("ws_plugin__qcache_after_add_network_admin_options", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				A sort of callback function to add the settings link.
				Attach to: add_filter("plugin_action_links");
				*/
				public static function _add_settings_link($links = array(), $file = "")
					{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action("_ws_plugin__qcache_before_add_settings_link", get_defined_vars());
						unset($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						if(preg_match("/".preg_quote($file, "/")."$/", $GLOBALS["WS_PLUGIN__"]["qcache"]["l"]) && is_array($links))
							{
								$settings = '<a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--qcache-options")).'">Settings</a>';
								array_unshift($links, $settings);
								/**/
								eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
								do_action("_ws_plugin__qcache_during_add_settings_link", get_defined_vars());
								unset($__refs, $__v); /* Unset defined __refs, __v. */
							}
						/**/
						return apply_filters("_ws_plugin__qcache_add_settings_link", $links, get_defined_vars());
					}
				/*
				A sort of callback function to hide Quick Cache from plugins menu.
				Attach to: add_filter("all_plugins");
				*/
				public static function _hide_from_plugins_menu($plugins = FALSE)
					{
						eval('foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;');
						do_action("_ws_plugin__qcache_before_hide_from_plugins_menu", get_defined_vars());
						unset($__refs, $__v); /* Unset defined __refs, __v. */
						/**/
						foreach($plugins as $file => $plugin)
							if(preg_match("/".preg_quote($file, "/")."$/", $GLOBALS["WS_PLUGIN__"]["qcache"]["l"]))
								unset($plugins[$file]);
						/**/
						return apply_filters("_ws_plugin__qcache_hide_from_plugins_menu", $plugins, get_defined_vars());
					}
				/*
				Adds nodes to the Admin Bar.
				Attach to: add_action("admin_bar_menu");
				*/
				public static function add_admin_bar_nodes(&$wp_admin_bar = FALSE)
					{
						do_action("ws_plugin__qcache_before_add_admin_bar_nodes", get_defined_vars());
						/**/
						if((!is_multisite() && current_user_can("install_plugins")) || (is_multisite() && (is_super_admin() || apply_filters("ws_plugin__qcache_ms_user_can_see_admin_header_controls", false))))
							{
								if(is_object /* Using the Admin Bar for WordPress® v3.3+. */($wp_admin_bar) && version_compare(get_bloginfo("version"), "3.3-RC1", ">="))
									{
										$clear = '<form style="width:auto; margin:0; padding:0;" onsubmit="return false;"><input type="button" id="ws-plugin--qcache-ajax-clear" style="outline:none; border:0; margin:0; padding:0 5px 0 26px; width:auto; overflow:visible; '.((empty($GLOBALS["is_IE"])) ? 'min-width:115px;' : '').' height:20px; font-size:12px; line-height:12px; color:#000000; text-shadow:none; border-radius:3px; background:#FFFFFF url(\''.c_ws_plugin__qcache_utils_strings::esc_js_sq(esc_attr($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"])).'/images/ajax-clear.png\') no-repeat 5px center;" value="Clear Cache" title="Clear Cache Manually" onclick="jQuery (this).css (\'background-image\', \'url(\\\''.c_ws_plugin__qcache_utils_strings::esc_js_sq(esc_attr($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]), 3).'/images/ajax-loader.gif\\\')\'); jQuery.post (\''.c_ws_plugin__qcache_utils_strings::esc_js_sq(admin_url("/admin-ajax.php")).'\', {action: \'ws_plugin__qcache_ajax_clear\', ws_plugin__qcache_ajax_clear: \''.c_ws_plugin__qcache_utils_strings::esc_js_sq(esc_attr(wp_create_nonce("ws-plugin--qcache-ajax-clear"))).'\'}, function (response){ eval (response); });" /></form>';
										$wp_admin_bar->add_node(array("parent" => "top-secondary", "id" => "ws-plugin--qcache-ajax-clear-menu", "title" => $clear, "meta" => array("class" => "ws-plugin--qcache-ajax-clear-menu", "tabindex" => -1)));
									}
								do_action("ws_plugin__qcache_during_add_admin_bar_nodes", get_defined_vars());
							}
						do_action("ws_plugin__qcache_after_add_admin_bar_nodes", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Adds old items to the admin header.
				Attach to: add_action("in_admin_header");
				*/
				public static function old_admin_header_items /* Backward compatibility. */()
					{
						do_action("ws_plugin__qcache_before_old_admin_header_items", get_defined_vars());
						/**/
						if(version_compare /* Backward compatibility with WordPress® v3.2. */(get_bloginfo("version"), "3.3-RC1", "<"))
							/**/
							if((!is_multisite() && current_user_can("install_plugins")) || (is_multisite() && (is_super_admin() || apply_filters("ws_plugin__qcache_ms_user_can_see_admin_header_controls", false))))
								{
									echo '<form style="float:right; margin:2px 0 0 10px;" onsubmit="return false;"><input type="button" id="ws-plugin--qcache-ajax-clear" style="outline:none; min-width:115px; padding-left:22px; padding-right:5px; background-repeat:no-repeat; background-position:5px center; background-image:url(\''.c_ws_plugin__qcache_utils_strings::esc_sq(esc_attr($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"])).'/images/ajax-clear.png\');" value="Clear Cache" title="Clear Cache Manually" onclick="jQuery (this).css (\'background-image\', \'url(\\\''.c_ws_plugin__qcache_utils_strings::esc_sq(esc_attr($GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"])).'/images/ajax-loader.gif\\\')\'); jQuery.post (ajaxurl, {action: \'ws_plugin__qcache_ajax_clear\', ws_plugin__qcache_ajax_clear: \''.c_ws_plugin__qcache_utils_strings::esc_sq(esc_attr(wp_create_nonce("ws-plugin--qcache-ajax-clear"))).'\'}, function (response){ eval (response); });" /></form>';
									/**/
									do_action("ws_plugin__qcache_during_old_admin_header_items", get_defined_vars());
								}
						do_action("ws_plugin__qcache_after_old_admin_header_items", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Add scripts to admin panels.
				Attach to: add_action("admin_print_scripts");
				*/
				public static function add_admin_scripts()
					{
						do_action("ws_plugin__qcache_before_add_admin_scripts", get_defined_vars());
						/**/
						if($_GET["page"] && preg_match("/ws-plugin--qcache-/", $_GET["page"]))
							{
								wp_enqueue_script("jquery");
								wp_enqueue_script("thickbox");
								wp_enqueue_script("media-upload");
								wp_enqueue_script("jquery-ui-core");
								wp_enqueue_script("jquery-sprintf", $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]."/includes/jquery/jquery.sprintf/jquery.sprintf-min.js", array("jquery"), c_ws_plugin__qcache_utilities::ver_checksum());
								wp_enqueue_script("jquery-json-ps", $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]."/includes/jquery/jquery.json-ps/jquery.json-ps-min.js", array("jquery"), c_ws_plugin__qcache_utilities::ver_checksum());
								wp_enqueue_script("jquery-ui-effects", $GLOBALS["WS_PLUGIN__"]["qcache"]["c"]["dir_url"]."/includes/jquery/jquery.ui-effects/jquery.ui-effects-min.js", array("jquery", "jquery-ui-core"), c_ws_plugin__qcache_utilities::ver_checksum());
								wp_enqueue_script("ws-plugin--qcache-menu-pages", site_url("/?ws_plugin__qcache_menu_pages_js=".urlencode(mt_rand())), array("jquery", "thickbox", "media-upload", "jquery-sprintf", "jquery-json-ps", "jquery-ui-core", "jquery-ui-effects"), c_ws_plugin__qcache_utilities::ver_checksum());
								/**/
								do_action("ws_plugin__qcache_during_add_admin_scripts", get_defined_vars());
							}
						/**/
						do_action("ws_plugin__qcache_after_add_admin_scripts", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Add styles to admin panels.
				Attach to: add_action("admin_print_styles");
				*/
				public static function add_admin_styles()
					{
						do_action("ws_plugin__qcache_before_add_admin_styles", get_defined_vars());
						/**/
						if($_GET["page"] && preg_match("/ws-plugin--qcache-/", $_GET["page"]))
							{
								wp_enqueue_style("thickbox");
								wp_enqueue_style("ws-plugin--qcache-menu-pages", site_url("/?ws_plugin__qcache_menu_pages_css=".urlencode(mt_rand())), array("thickbox"), c_ws_plugin__qcache_utilities::ver_checksum(), "all");
								/**/
								do_action("ws_plugin__qcache_during_add_admin_styles", get_defined_vars());
							}
						/**/
						do_action("ws_plugin__qcache_after_add_admin_styles", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Function for building and handling the options page.
				*/
				public static function options_page()
					{
						do_action("ws_plugin__qcache_before_options_page", get_defined_vars());
						/**/
						c_ws_plugin__qcache_menu_pages::update_all_options().clearstatcache();
						/**/
						if(file_exists(ABSPATH."wp-config.php") && !is_writable(ABSPATH."wp-config.php"))
							{
								c_ws_plugin__qcache_admin_notices::display_admin_notice('<strong>Permissions:</strong> Please check permissions on <code>'.esc_html(preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"], "/")."/", "", ABSPATH."wp-config.php")).'</code>. Quick Cache needs write-access to this file. Permissions need to be <code>755</code> or higher.', true);
							}
						else if(file_exists(dirname(ABSPATH)."/wp-config.php") && !is_writable(dirname(ABSPATH)."/wp-config.php"))
							{
								c_ws_plugin__qcache_admin_notices::display_admin_notice('<strong>Permissions:</strong> Please check permissions on <code>'.esc_html(preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"], "/")."/", "", dirname(ABSPATH)."/wp-config.php")).'</code>. Quick Cache needs write-access to this file. Permissions need to be <code>755</code> or higher.', true);
							}
						if(is_dir(WP_CONTENT_DIR) && !is_writable(WP_CONTENT_DIR))
							{
								c_ws_plugin__qcache_admin_notices::display_admin_notice('<strong>Permissions:</strong> Please check permissions on <code>'.esc_html(preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"], "/")."/", "", WP_CONTENT_DIR)).'</code>. Quick Cache needs write-access to this directory. Permissions need to be <code>755</code> or higher.', true);
							}
						if(is_dir(WP_CONTENT_DIR."/cache") && !is_writable(WP_CONTENT_DIR."/cache"))
							{
								c_ws_plugin__qcache_admin_notices::display_admin_notice('<strong>Permissions:</strong> Please check permissions on <code>'.esc_html(preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"], "/")."/", "", WP_CONTENT_DIR."/cache")).'</code>. Quick Cache needs write-access to this directory. Permissions need to be <code>755</code> or higher.', true);
							}
						/**/
						include_once dirname(dirname(__FILE__))."/menu-pages/options.inc.php";
						/**/
						do_action("ws_plugin__qcache_after_options_page", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Function for building and handling the info page.
				*/
				public static function info_page()
					{
						do_action("ws_plugin__qcache_before_info_page", get_defined_vars());
						/**/
						include_once dirname(dirname(__FILE__))."/menu-pages/info.inc.php";
						/**/
						do_action("ws_plugin__qcache_after_info_page", get_defined_vars());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>