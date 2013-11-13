<?php
/*
Version: 131108
Text Domain: quick-cache
Plugin Name: Quick Cache

Author: s2Member® / WebSharks, Inc.
Author URI: http://www.s2member.com

Plugin URI: http://www.websharks-inc.com/r/quick-cache/
Description: WordPress advanced cache plugin; speed without compromise!

Speed up your site ~ BIG Time! - If you care about the speed of your site, Quick Cache is a plugin that you absolutely MUST have installed :-)
*/
namespace quick_cache // Root namespace.
	{
		if(!defined('WPINC')) // MUST have WordPress.
			exit('Do NOT access this file directly: '.basename(__FILE__));

		if(!class_exists('\\'.__NAMESPACE__.'\\plugin'))
			{
				class plugin // Base plugin class.
				{
					public $version = '131108'; // See: `readme.txt` file.
					public $text_domain = ''; // Defined by class constructor.
					public $default_options = array(); // Defined @ setup.
					public $options = array(); // Defined @ setup.
					public $cap = ''; // Defined @ setup.

					public function __construct() // Constructor.
						{
							if(strpos(__NAMESPACE__, '\\') !== FALSE) // Sanity check.
								throw new \exception('Not a root namespace: `'.__NAMESPACE__.'`.');

							$this->text_domain = str_replace('_', '-', __NAMESPACE__);

							add_action('after_setup_theme', array($this, 'setup'));
						}

					public function setup()
						{
							do_action('before__'.__METHOD__, get_defined_vars());

							load_plugin_textdomain($this->text_domain);

							$this->default_options = array( // Default options.
								'crons_setup'               => '0', // `0` or timestamp.

								'enable'                    => '0', // `0|1`.
								'debugging_enable'          => '1', // `0|1`.
								'allow_browser_cache'       => '0', // `0|1`.

								'cache_dir'                 => '/wp-content/cache', // Relative to `ABSPATH`.
								'cache_max_age'             => '7 days', // `strtotime()` compatible.

								'get_requests'              => '0', // `0|1`.

								'uninstall_on_deactivation' => '0' // `0|1`.
							); // Default options are merged with those defined by the site owner.
							$options               = (is_array($options = get_option(__NAMESPACE__.'_options'))) ? $options : array();

							if(!isset($options['enable']) && is_array($old_options = get_option('ws_plugin__qcache_options')) && $old_options)
								{
									if(!isset($options['enable']) && isset($old_options['enabled']))
										$options['enable'] = (string)(integer)$old_options['enabled'];

									if(!isset($options['debugging_enable']) && isset($old_options['enable_debugging']))
										$options['debugging_enable'] = (string)(integer)$old_options['enable_debugging'];

									if(!isset($options['allow_browser_cache']) && isset($old_options['allow_browser_cache']))
										$options['allow_browser_cache'] = (string)(integer)$old_options['allow_browser_cache'];

									if(!isset($options['when_logged_in']) && isset($old_options['dont_cache_when_logged_in']))
										$options['when_logged_in'] = ((string)(integer)$old_options['dont_cache_when_logged_in']) ? '0' : '1';

									if(!isset($options['get_requests']) && isset($old_options['dont_cache_query_string_requests']))
										$options['get_requests'] = ((string)(integer)$old_options['dont_cache_query_string_requests']) ? '0' : '1';

									if(!isset($options['exclude_uris']) && isset($old_options['dont_cache_these_uris']))
										$options['exclude_uris'] = (string)$old_options['dont_cache_these_uris'];

									if(!isset($options['exclude_refs']) && isset($old_options['dont_cache_these_refs']))
										$options['exclude_refs'] = (string)$old_options['dont_cache_these_refs'];

									if(!isset($options['exclude_agents']) && isset($old_options['dont_cache_these_agents']))
										$options['exclude_agents'] = (string)$old_options['dont_cache_these_agents'];

									if(!isset($options['version_salt']) && isset($old_options['version_salt']))
										$options['version_salt'] = (string)$old_options['version_salt'];
								}
							$this->default_options = apply_filters(__METHOD__.'__default_options', $this->default_options, get_defined_vars());
							$this->options         = array_merge($this->default_options, $options); // This considers old options also.
							$this->options         = apply_filters(__METHOD__.'__options', $this->options, get_defined_vars());

							$this->cap = apply_filters(__METHOD__.'__cap', 'activate_plugins');

							add_action('init', array($this, 'check_advanced_cache'));
							add_action('wp_loaded', array($this, 'actions'));

							add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
							add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

							add_action('all_admin_notices', array($this, 'all_admin_notices'));
							add_action('all_admin_notices', array($this, 'all_admin_errors'));
							add_action('network_admin_menu', array($this, 'add_network_menu_pages'));
							add_action('admin_menu', array($this, 'add_menu_pages'));

							add_action('switch_theme', array($this, 'auto_clear_cache'));
							add_action('wp_create_nav_menu', array($this, 'auto_clear_cache'));
							add_action('wp_update_nav_menu', array($this, 'auto_clear_cache'));
							add_action('wp_delete_nav_menu', array($this, 'auto_clear_cache'));

							add_action('save_post', array($this, 'auto_purge_post_cache'));
							add_action('delete_post', array($this, 'auto_purge_post_cache'));
							add_action('clean_post_cache', array($this, 'auto_purge_post_cache'));

							add_action('trackback_post', array($this, 'auto_purge_comment_post_cache'));
							add_action('pingback_post', array($this, 'auto_purge_comment_post_cache'));
							add_action('comment_post', array($this, 'auto_purge_comment_post_cache'));
							add_action('edit_comment', array($this, 'auto_purge_comment_post_cache'));
							add_action('delete_comment', array($this, 'auto_purge_comment_post_cache'));

							add_action('create_term', array($this, 'auto_clear_cache'));
							add_action('edit_terms', array($this, 'auto_clear_cache'));
							add_action('delete_term', array($this, 'auto_clear_cache'));

							add_action('add_link', array($this, 'auto_clear_cache'));
							add_action('edit_link', array($this, 'auto_clear_cache'));
							add_action('delete_link', array($this, 'auto_clear_cache'));

							if((integer)$this->options['crons_setup'] < 1382523750)
								{
									wp_clear_scheduled_hook('_cron_'.__NAMESPACE__.'_cleanup');

									wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule');
									wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule');

									wp_schedule_event(time() + 60, 'daily', '_cron_'.__NAMESPACE__.'_cleanup');

									$this->options['crons_setup'] = (string)time();
									update_option(__NAMESPACE__.'_options', $this->options);
								}
							add_action('_cron_'.__NAMESPACE__.'_cleanup', array($this, 'purge_cache'));

							register_activation_hook(__FILE__, array($this, 'activate'));
							register_deactivation_hook(__FILE__, array($this, 'deactivate'));

							do_action('after__'.__METHOD__, get_defined_vars());
							do_action(__METHOD__.'_complete', get_defined_vars());
						}

					public function activate()
						{
							if(!$this->options['enable'])
								return; // Nothing to do.

							$this->add_wp_cache_to_wp_config();
							$this->add_advanced_cache();
							$this->auto_clear_cache();
						}

					public function deactivate()
						{
							if(!$this->options['uninstall_on_deactivation'])
								return; // Nothing to do here.

							delete_option(__NAMESPACE__.'_options');
							delete_option(__NAMESPACE__.'_notices');
							delete_option(__NAMESPACE__.'_errors');

							delete_option('ws_plugin__qcache_options');
							delete_option('ws_plugin__qcache_notices');
							delete_option('ws_plugin__qcache_configured');

							wp_clear_scheduled_hook('_cron_'.__NAMESPACE__.'_cleanup');
							$this->remove_wp_cache_from_wp_config();
							$this->delete_advanced_cache();
							$this->clear_cache();
						}

					public function is_pro_preview()
						{
							static $is;
							if(isset($is)) return $is;

							if(!empty($_REQUEST[__NAMESPACE__.'_pro_preview']))
								return ($is = TRUE);

							return ($is = FALSE);
						}

					public function url($file = '', $scheme = '')
						{
							static $plugin_directory; // Static cache.

							if(!isset($plugin_directory)) // Not cached yet?
								$plugin_directory = rtrim(plugin_dir_url(__FILE__), '/');

							$url = $plugin_directory.(string)$file;

							if($scheme) // A specific URL scheme?
								$url = set_url_scheme($url, (string)$scheme);

							return apply_filters(__METHOD__, $url, get_defined_vars());
						}

					public function esc_sq($string, $times = 1)
						{
							return str_replace("'", str_repeat('\\', abs($times))."'", (string)$string);
						}

					public function actions()
						{
							if(empty($_REQUEST[__NAMESPACE__])) return;

							require_once dirname(__FILE__).'/includes/actions.php';
						}

					public function enqueue_admin_styles()
						{
							if(empty($_GET['page']) || strpos($_GET['page'], __NAMESPACE__) !== 0)
								return; // Nothing to do; NOT a plugin page in the administrative area.

							$deps = array(); // Plugin dependencies.

							wp_enqueue_style(__NAMESPACE__, $this->url('/client-s/css/menu-pages.min.css'), $deps, $this->version, 'all');
						}

					public function enqueue_admin_scripts()
						{
							if(empty($_GET['page']) || strpos($_GET['page'], __NAMESPACE__) !== 0)
								return; // Nothing to do; NOT a plugin page in the administrative area.

							$deps = array('jquery'); // Plugin dependencies.

							wp_enqueue_script(__NAMESPACE__, $this->url('/client-s/js/menu-pages.min.js'), $deps, $this->version, TRUE);
						}

					public function add_network_menu_pages()
						{
							add_menu_page(__('Quick Cache', $this->text_domain), // Menu page for plugin options/config.
							              __('Quick Cache', $this->text_domain), $this->cap, __NAMESPACE__, array($this, 'menu_page_options'),
							              $this->url('/client-s/images/menu-icon.png'));
						}

					public function add_menu_pages()
						{
							if(!is_multisite()) // Multisite networks must use the network administrative area.
								add_menu_page(__('Quick Cache', $this->text_domain), // Menu page for plugin options/config.
								              __('Quick Cache', $this->text_domain), $this->cap, __NAMESPACE__, array($this, 'menu_page_options'),
								              $this->url('/client-s/images/menu-icon.png'));
						}

					public function menu_page_options()
						{
							require_once dirname(__FILE__).'/includes/menu-pages.php';
							$menu_pages = new menu_pages();
							$menu_pages->options();
						}

					public function all_admin_notices()
						{
							$notices = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
							if($notices) delete_option(__NAMESPACE__.'_notices'); // Process one-time only.

							$notices = array_unique($notices); // Don't show dupes.

							if(current_user_can($this->cap)) foreach($notices as $_notice)
								echo apply_filters(__METHOD__.'__notice', '<div class="updated"><p>'.$_notice.'</p></div>', get_defined_vars());
							unset($_notice); // Housekeeping.
						}

					public function all_admin_errors()
						{
							$errors = (is_array($errors = get_option(__NAMESPACE__.'_errors'))) ? $errors : array();
							if($errors) delete_option(__NAMESPACE__.'_errors'); // Process one-time only.

							$errors = array_unique($errors); // Don't show dupes.

							if(current_user_can($this->cap)) foreach($errors as $_error)
								echo apply_filters(__METHOD__.'__error', '<div class="error"><p>'.$_error.'</p></div>', get_defined_vars());
							unset($_error); // Housekeeping.
						}

					public function clear_cache()
						{
							$counter = 0; // Initialize.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							if(!is_dir($cache_dir) || !($opendir = opendir($cache_dir)))
								return $counter; // Nothing we can do.

							$is_multisite = is_multisite(); // Cache this here.

							if($is_multisite && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL))
								{ // Multisite w/ sub-directories; need first sub-directory.
									list($host_dir_token) = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
									$host_dir_token = (strlen($host_dir_token)) ? '/'.$host_dir_token.'/' : '/';
								}
							else $host_dir_token = '/'; // Not multisite; or running it's own domain.

							$http_host_nps = preg_replace('/\:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
							$md5_3         = md5($http_host_nps.$host_dir_token);

							set_time_limit(1800); // In case of HUGE sites w/ a very large directory.

							while(($_file = $_basename = readdir($opendir)) !== FALSE && ($_file = $cache_dir.'/'.$_file))
								if(is_file($_file) && strpos($_basename, 'qc-c-') === 0 && (!$is_multisite || strpos($_file, $md5_3) !== FALSE))
									if(!unlink($_file)) throw new \exception(sprintf(__('Unable to clear: `%1$s`.', $this->text_domain), $_file));
									else $counter++; // Increment counter for each file we clear.

							unset($_file, $_basename); // Just a little housekeeping.
							closedir($opendir); // Housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function purge_cache()
						{
							$counter = 0; // Initialize.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							$max_age   = strtotime('-'.$this->options['cache_max_age']);

							if(!is_dir($cache_dir) || !($opendir = opendir($cache_dir)))
								return $counter; // Nothing we can do.

							set_time_limit(1800); // In case of HUGE sites w/ a very large directory.

							while(($_file = $_basename = readdir($opendir)) !== FALSE && ($_file = $cache_dir.'/'.$_file))
								if(is_file($_file) && strpos($_basename, 'qc-c-') === 0 && filemtime($_file) < $max_age)
									if(!unlink($_file)) throw new \exception(sprintf(__('Unable to purge: `%1$s`.', $this->text_domain), $_file));
									else $counter++; // Increment counter for each file we purge.

							unset($_file, $_basename); // Just a little housekeeping.
							closedir($opendir); // Housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_clear_cache()
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							$counter = $this->clear_cache();

							if($counter && is_admin()) // Change notifications cannot be turned off in the lite version.
								{
									$notices   = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
									$notices[] = '<img src="'.esc_attr($this->url('/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									             __('<strong>Quick Cache:</strong> detected changes. Found cache files for this site (auto-clearing).', $this->text_domain);
									update_option(__NAMESPACE__.'_notices', $notices);
								}
							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_purge_post_cache($id)
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
								return $counter; // Nothing to do.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							if(!($permalink = get_permalink($id))) return $counter; // Nothing we can do.

							if(!($parts = parse_url($permalink)) || empty($parts['path']))
								return $counter; // Nothing we can do.

							$http_host_nps = preg_replace('/\:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
							$md5_2         = md5($http_host_nps.$parts['path'].((!empty($parts['query'])) ? '?'.$parts['query'] : ''));

							if(($type = get_post_type($id)) && ($type = get_post_type_object($type)) && !empty($type->labels->singular_name))
								$type_singular_name = $type->labels->singular_name; // Singular name for the post type.
							else $type_singular_name = __('Post', $this->text_domain); // Default value.

							foreach((array)glob($cache_dir.'/qc-c-*-'.$md5_2.'-*', GLOB_NOSORT) as $_file) if($_file && is_file($_file))
								{
									if(!unlink($_file)) // If file deletion fails; stop here w/ exception.
										throw new \exception(sprintf(__('Unable to auto-purge: `%1$s`.', $this->text_domain), $_file));
									$counter++; // Increment counter for each file purge.

									if(!empty($_notices) || !is_admin()) // Change notifications cannot be turned off in the lite version.
										continue; // Stop here; we already issued a notice, or this notice is N/A.

									$_notices   = (is_array($_notices = get_option(__NAMESPACE__.'_notices'))) ? $_notices : array();
									$_notices[] = '<img src="'.esc_attr($this->url('/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									              sprintf(__('<strong>Quick Cache:</strong> detected changes. Found cache files for %1$s ID: <code>%2$s</code> (auto-purging).', $this->text_domain), $type_singular_name, $id);
									update_option(__NAMESPACE__.'_notices', $_notices);
								}
							unset($_file, $_notices); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_purge_comment_post_cache($id)
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							if(!is_object($comment = get_comment($id)))
								return $counter; // Nothing we can do.

							if(empty($comment->comment_post_ID))
								return $counter; // Nothing we can do.

							if($comment->comment_approved === 'spam')
								return $counter; // Don't allow spam to clear cache.

							$counter = $this->auto_purge_post_cache($comment->comment_post_ID);

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function find_wp_config_file()
						{
							if(is_file($abspath_wp_config = ABSPATH.'wp-config.php'))
								$wp_config_file = $abspath_wp_config;

							else if(is_file($dirname_abspath_wp_config = dirname(ABSPATH).'wp-config.php'))
								$wp_config_file = $dirname_abspath_wp_config;

							else $wp_config_file = ''; // Unable to find `/wp-config.php` file.

							return apply_filters(__METHOD__, $wp_config_file, get_defined_vars());
						}

					public function add_wp_cache_to_wp_config()
						{
							if(!$this->options['enable'])
								return ''; // Nothing to do.

							if(!($wp_config_file = $this->find_wp_config_file()))
								return ''; // Unable to find `/wp-config.php`.

							if(!is_readable($wp_config_file)) return ''; // Not possible.
							if(!($wp_config_file_contents = file_get_contents($wp_config_file)))
								return ''; // Failure; could not read file.

							if(preg_match('/['."\t ".']*define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[1-9][0-9\.]*|TRUE|([\'"])(?:[^0\'"]|[^\'"]{2,})\\2)\s*\)\s*;/i', $wp_config_file_contents))
								return $wp_config_file_contents; // It's already in there; no need to modify this file.

							if(!($wp_config_file_contents = $this->remove_wp_cache_from_wp_config()))
								return ''; // Unable to remove previous value.

							if(!($wp_config_file_contents = preg_replace('/^\s*(?:\<\?php|\<\?)\s+/i', '${0}'."\n"."define('WP_CACHE', TRUE);", $wp_config_file_contents, 1)))
								return ''; // Failure; something went terribly wrong here.

							if(strpos($wp_config_file_contents, "define('WP_CACHE', TRUE);") === FALSE)
								return ''; // Failure; unable to add; unexpected PHP code.

							if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
								return ''; // We may NOT edit any files.

							if(!is_writable($wp_config_file)) return ''; // Not possible.
							if(!file_put_contents($wp_config_file, $wp_config_file_contents))
								return ''; // Failure; could not write changes.

							return apply_filters(__METHOD__, $wp_config_file_contents, get_defined_vars());
						}

					public function remove_wp_cache_from_wp_config()
						{
							if(!($wp_config_file = $this->find_wp_config_file()))
								return ''; // Unable to find `/wp-config.php`.

							if(!is_readable($wp_config_file)) return ''; // Not possible.
							if(!($wp_config_file_contents = file_get_contents($wp_config_file)))
								return ''; // Failure; could not read file.

							if(!preg_match('/([\'"])WP_CACHE\\1/i', $wp_config_file_contents))
								return $wp_config_file_contents; // Already gone.

							if(preg_match('/['."\t ".']*define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:0|FALSE|NULL|([\'"])0?\\2)\s*\)\s*;/i', $wp_config_file_contents))
								return $wp_config_file_contents; // It's already disabled; no need to modify this file.

							if(!($wp_config_file_contents = preg_replace('/['."\t ".']*define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[0-9\.]+|TRUE|FALSE|NULL|([\'"])[^\'"]*\\2)\s*\)\s*;(?:'."\r\n".'|'."\r".'|'."\n".')?/i', '', $wp_config_file_contents)))
								return ''; // Failure; something went terribly wrong here.

							if(preg_match('/([\'"])WP_CACHE\\1/i', $wp_config_file_contents))
								return ''; // Failure; perhaps the `/wp-config.php` file contains syntax we cannot remove safely.

							if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
								return ''; // We may NOT edit any files.

							if(!is_writable($wp_config_file)) return ''; // Not possible.
							if(!file_put_contents($wp_config_file, $wp_config_file_contents))
								return ''; // Failure; could not write changes.

							return apply_filters(__METHOD__, $wp_config_file_contents, get_defined_vars());
						}

					public function check_advanced_cache()
						{
							if(!$this->options['enable'])
								return; // Nothing to do.

							if(!empty($_REQUEST[__NAMESPACE__]))
								return; // Skip on plugin actions.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							if(!is_file($cache_dir.'/qc-advanced-cache.time'))
								$this->add_advanced_cache();
						}

					public function add_advanced_cache()
						{
							if(!$this->remove_advanced_cache())
								return FALSE; // Still exists.

							$cache_dir               = ABSPATH.$this->options['cache_dir'];
							$advanced_cache_file     = WP_CONTENT_DIR.'/advanced-cache.php';
							$advanced_cache_template = dirname(__FILE__).'/includes/advanced-cache.tpl.php';

							if(is_file($advanced_cache_file) && !is_writable($advanced_cache_file))
								return FALSE; // Not possible to create.

							if(!is_file($advanced_cache_file) && !is_writable(dirname($advanced_cache_file)))
								return FALSE; // Not possible to create.

							if(!is_file($advanced_cache_template) || !is_readable($advanced_cache_template))
								return FALSE; // Template file is missing; or not readable.

							if(!($advanced_cache_contents = file_get_contents($advanced_cache_template)))
								return FALSE; // Template file is missing; or is not readable.

							foreach($this->options as $_option => $_value) // Iterates options.
								{
									$_value = (string)$_value; // Force string.

									switch($_option) // Some values need tranformations.
									{
										default: // Default case handler.

											$_value = "'".$this->esc_sq($_value)."'";

											break; // Break switch handler.
									}
									$advanced_cache_contents = // Fill replacement codes.
										str_ireplace(array("'%%".__NAMESPACE__.'_'.$_option."%%'",
										                   "'%%".str_ireplace('_cache', '', __NAMESPACE__).'_'.$_option."%%'"),
										             $_value, $advanced_cache_contents);
								}
							unset($_option, $_value, $_values, $_response, $_errors); // Housekeeping.

							// Ignore; this is created by Quick Cache; and we don't need to obey in this case.
							#if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
							#	return FALSE; // We may NOT edit any files.

							if(!file_put_contents($advanced_cache_file, $advanced_cache_contents))
								return FALSE; // Failure; could not write file.

							if(!file_put_contents($cache_dir.'/qc-advanced-cache.time', time()))
								return FALSE; // Failure; could not write cache entry.

							return TRUE; // All done :-)
						}

					public function remove_advanced_cache()
						{
							$advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

							if(!is_file($advanced_cache_file)) return TRUE; // Already gone.

							if(is_readable($advanced_cache_file) && filesize($advanced_cache_file) === 0)
								return TRUE; // Already gone; e.g. it's empty already.

							if(!is_writable($advanced_cache_file)) return FALSE; // Not possible.

							// Ignore; this is created by Quick Cache; and we don't need to obey in this case.
							#if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
							#	return FALSE; // We may NOT edit any files.

							/* Empty the file only. This way permissions are NOT lost in cases where
								a site owner makes this specific file writable for Quick Cache. */
							if(file_put_contents($advanced_cache_file, '') !== 0)
								return FALSE; // Failure.

							return TRUE; // Removal success.
						}

					public function delete_advanced_cache()
						{
							$advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

							if(!is_file($advanced_cache_file)) return TRUE; // Already gone.

							// Ignore; this is created by Quick Cache; and we don't need to obey in this case.
							#if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
							#	return FALSE; // We may NOT edit any files.

							if(!is_writable($advanced_cache_file) || !unlink($advanced_cache_file))
								return FALSE; // Not possible; or outright failure.

							return TRUE; // Deletion success.
						}
				}

				/**
				 * @return plugin Class instance.
				 */
				function plugin() // Easy reference.
					{
						return $GLOBALS[__NAMESPACE__];
					}

				$GLOBALS[__NAMESPACE__] = new plugin(); // New plugin instance.
			}
		else add_action('all_admin_notices', function () // Do NOT load in this case.
			{
				echo '<div class="error"><p>'. // Running multiple versions of this plugin at same time.
				     __('Please disable the LITE version of Quick Cache before you activate the PRO version.',
				        str_replace('_', '-', __NAMESPACE__)).'</p></div>';
			});
	}
