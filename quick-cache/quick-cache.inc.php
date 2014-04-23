<?php
/**
 * Quick Cache Plugin
 *
 * @package quick_cache\plugin
 * @since 140422 First documented version.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace quick_cache
	{
		if(!defined('WPINC')) // MUST have WordPress.
			exit('Do NOT access this file directly: '.basename(__FILE__));

		if(!class_exists('\\'.__NAMESPACE__.'\\plugin'))
			{
				/**
				 * Quick Cache Plugin
				 *
				 * @package quick_cache\plugin
				 * @since 140422 First documented version.
				 */
				class plugin
				{
					/**
					 * Pro version flag.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var boolean TRUE for pro version; FALSE for lite version.
					 */
					public $is_pro = FALSE;

					/**
					 * Stub `__FILE__` location.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var string Current `__FILE__` from the stub; NOT from this file.
					 *    Note that Quick Cache has a stub loader that checks for PHP v5.3 compat;
					 *    which is why we have this property. This is the stub `__FILE__`.
					 */
					public $file = '';

					/**
					 * Version string in YYMMDD[+build] format.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var string Current version of the software.
					 */
					public $version = '140104';

					/**
					 * Plugin text domain for translations.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var string Plugin text domain; set by constructor.
					 */
					public $text_domain = '';

					/**
					 * An array of all default option values.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var array Default options array; set by constructor.
					 */
					public $default_options = array();

					/**
					 * Configured option values.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var array Options configured by site owner; set by constructor.
					 */
					public $options = array();

					/**
					 * Network capability requirement.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var string WordPress capability required to
					 *    administer QC in a multisite network.
					 */
					public $network_cap = '';

					/**
					 * General capability requirement.
					 *
					 * @since 140422 First documented version.
					 *
					 * @var string WordPress capability required to
					 *    administer QC in any environment; i.e. in multisite or otherwise.
					 */
					public $cap = '';

					/**
					 * Quick Cache plugin constructor.
					 *
					 * @since 140422 First documented version.
					 */
					public function __construct()
						{
							if(strpos(__NAMESPACE__, '\\') !== FALSE) // Sanity check.
								throw new \exception('Not a root namespace: `'.__NAMESPACE__.'`.');

							$this->file        = preg_replace('/\.inc\.php$/', '.php', __FILE__);
							$this->text_domain = str_replace('_', '-', __NAMESPACE__);

							add_action('after_setup_theme', array($this, 'setup'));
							register_activation_hook($this->file, array($this, 'activate'));
							register_deactivation_hook($this->file, array($this, 'deactivate'));
						}

					/**
					 * Setup the Quick Cache plugin.
					 *
					 * @since 140422 First documented version.
					 */
					public function setup()
						{
							do_action('before__'.__METHOD__, get_defined_vars());

							load_plugin_textdomain($this->text_domain);

							$wp_content_dir_relative = // Considers custom `WP_CONTENT_DIR` locations.
								rtrim(str_replace(ABSPATH, '', WP_CONTENT_DIR), '/'); // No trailing slash.

							$this->default_options = array( // Default options.
							                                'version'                       => $this->version,

							                                'crons_setup'                   => '0', // `0` or timestamp.

							                                'enable'                        => '0', // `0|1`.
							                                'debugging_enable'              => '1', // `0|1`.
							                                'cache_purge_home_page_enable'  => '1', // `0|1`.
							                                'cache_purge_posts_page_enable' => '1', // `0|1`.
							                                'allow_browser_cache'           => '0', // `0|1`.

							                                'cache_dir'                     => $wp_content_dir_relative.'/cache',
							                                'cache_max_age'                 => '7 days', // `strtotime()` compatible.

							                                'get_requests'                  => '0', // `0|1`.
							                                'feeds_enable'                  => '0', // `0|1`.
							                                'cache_404_requests'            => '0', // `0|1`.

							                                'uninstall_on_deactivation'     => '0' // `0|1`.
							); // Default options are merged with those defined by the site owner.
							$options               = (is_array($options = get_option(__NAMESPACE__.'_options'))) ? $options : array();
							if(is_multisite() && is_array($site_options = get_site_option(__NAMESPACE__.'_options')))
								$options = array_merge($options, $site_options); // Multisite network options.

							if(!$options && is_array($old_options = get_option('ws_plugin__qcache_options')) && $old_options)
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

							$this->options['cache_dir'] = trim($this->options['cache_dir'], '\\/'." \t\n\r\0\x0B");

							$this->network_cap = apply_filters(__METHOD__.'__network_cap', 'manage_network_plugins');
							$this->cap         = apply_filters(__METHOD__.'__cap', 'activate_plugins');

							add_action('init', array($this, 'check_advanced_cache'));
							add_action('init', array($this, 'check_blog_paths'));
							add_action('wp_loaded', array($this, 'actions'));

							add_action('admin_init', array($this, 'check_version'));
							add_action('admin_init', array($this, 'rewrite_notice'));

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
							add_action('wp_set_comment_status', array($this, 'auto_purge_comment_post_cache'));

							add_action('create_term', array($this, 'auto_clear_cache'));
							add_action('edit_terms', array($this, 'auto_clear_cache'));
							add_action('delete_term', array($this, 'auto_clear_cache'));

							add_action('add_link', array($this, 'auto_clear_cache'));
							add_action('edit_link', array($this, 'auto_clear_cache'));
							add_action('delete_link', array($this, 'auto_clear_cache'));

							add_filter('enable_live_network_counts', array($this, 'update_blog_paths'));

							add_filter('plugin_action_links_'.plugin_basename($this->file), array($this, 'add_settings_link'));

							add_filter('cron_schedules', array($this, 'extend_cron_schedules'));
							if((integer)$this->options['crons_setup'] < 1382523750)
								{
									wp_clear_scheduled_hook('_cron_'.__NAMESPACE__.'_cleanup');

									wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule');
									wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule');

									wp_schedule_event(time() + 60, 'daily', '_cron_'.__NAMESPACE__.'_cleanup');

									$this->options['crons_setup'] = (string)time();
									update_option(__NAMESPACE__.'_options', $this->options); // Blog-specific.
									if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->options);
								}
							add_action('_cron_'.__NAMESPACE__.'_cleanup', array($this, 'purge_cache'));

							do_action('after__'.__METHOD__, get_defined_vars());
							do_action(__METHOD__.'_complete', get_defined_vars());
						}

					/**
					 * WordPress database instance.
					 *
					 * @since 140422 First documented version.
					 *
					 * @return \wpdb Reference for IDEs.
					 */
					public function wpdb() // Shortcut for other routines.
						{
							return $GLOBALS['wpdb'];
						}

					/**
					 * Plugin activation hook.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to {@link \register_activation_hook()}
					 */
					public function activate()
						{
							$this->setup(); // Setup routines.

							if(!$this->options['enable'])
								return; // Nothing to do.

							$this->add_wp_cache_to_wp_config();
							$this->add_advanced_cache();
							$this->update_blog_paths();
							$this->auto_clear_cache();
						}

					/**
					 * Check current plugin version that installed in WP.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `admin_init` hook.
					 */
					public function check_version()
						{
							if(version_compare($this->options['version'], $this->version, '>='))
								return; // Nothing to do in this case.

							$this->options['version'] = $this->version;
							update_option(__NAMESPACE__.'_options', $this->options);
							if(is_multisite()) update_site_option(__NAMESPACE__.'_options', $this->options);

							if($this->options['enable']) // Only if enabled.
								{
									$this->add_wp_cache_to_wp_config();
									$this->add_advanced_cache();
									$this->update_blog_paths();
								}
							$this->wipe_cache(); // Always wipe the cache in this scenario.

							$notices   = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
							$notices[] = __('<strong>Quick Cache:</strong> detected a new version of itself. Recompiling w/ latest version... wiping the cache... all done :-)', $this->text_domain);
							$notices[] = __('<strong>Quick Cache Feature Notice:</strong> This version of Quick Cache adds new options for Feed caching. Feed caching is now disabled by default. If you wish to enable feed caching, please visit the Quick Cache options panel.', $this->text_domain);
							update_option(__NAMESPACE__.'_notices', $notices);
						}

					/**
					 * Display notice to site owners using a really old version of Quick Cache.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `admin_init` hook.
					 */
					public function rewrite_notice()
						{
							if(!get_option('ws_plugin__qcache_configured'))
								return; // Nothing to do in this case.

							delete_option('ws_plugin__qcache_configured'); // One-time only.

							$notices   = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
							$notices[] = __('<strong>Quick Cache:</strong> this version is a <strong>complete rewrite</strong> :-) Please review your Quick Cache options carefully!', $this->text_domain);
							update_option(__NAMESPACE__.'_notices', $notices);
						}

					/**
					 * Plugin deactivation hook.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to {@link \register_deactivation_hook()}
					 */
					public function deactivate()
						{
							$this->remove_wp_cache_from_wp_config();
							$this->remove_advanced_cache();
							$this->clear_cache();

							if(!$this->options['uninstall_on_deactivation'])
								return; // Nothing to do here.

							$this->delete_advanced_cache();

							delete_option(__NAMESPACE__.'_options');
							delete_option(__NAMESPACE__.'_notices');
							delete_option(__NAMESPACE__.'_errors');

							delete_option('ws_plugin__qcache_options');
							delete_option('ws_plugin__qcache_notices');
							delete_option('ws_plugin__qcache_configured');

							wp_clear_scheduled_hook('_cron_'.__NAMESPACE__.'_cleanup');
						}

					/**
					 * Current request is for a pro version preview?
					 *
					 * @since 140422 First documented version.
					 *
					 * @return boolean TRUE if the current request is for a pro preview.
					 */
					public function is_pro_preview()
						{
							static $is;
							if(isset($is)) return $is;

							if(!empty($_REQUEST[__NAMESPACE__.'_pro_preview']))
								return ($is = TRUE);

							return ($is = FALSE);
						}

					/**
					 * URL to a Quick Cache plugin file.
					 *
					 * @since 140422 First documented version.
					 *
					 * @param string $file Optional file path; relative to plugin directory.
					 * @param string $scheme Optional URL scheme; defaults to the current scheme.
					 *
					 * @return string URL to plugin directory; or to the specified `$file` if applicable.
					 */
					public function url($file = '', $scheme = '')
						{
							static $plugin_directory; // Static cache.

							if(!isset($plugin_directory)) // Not cached yet?
								$plugin_directory = rtrim(plugin_dir_url($this->file), '/');

							$url = $plugin_directory.(string)$file;

							if($scheme) // A specific URL scheme?
								$url = set_url_scheme($url, (string)$scheme);

							return apply_filters(__METHOD__, $url, get_defined_vars());
						}

					/**
					 * Escape single quotes.
					 *
					 * @since 140422 First documented version.
					 *
					 * @param string  $string Input string to escape.
					 * @param integer $times Optional. Defaults to one escape char; e.g. `\'`.
					 *    If you need to escape more than once, set this to something > `1`.
					 *
					 * @return string Escaped string; e.g. `Raam\'s the lead developer`.
					 */
					public function esc_sq($string, $times = 1)
						{
							return str_replace("'", str_repeat('\\', abs($times))."'", (string)$string);
						}

					/**
					 * Plugin action handler.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `wp_loaded` hook.
					 */
					public function actions()
						{
							if(empty($_REQUEST[__NAMESPACE__])) return;

							require_once dirname(__FILE__).'/includes/actions.php';
						}

					/**
					 * Adds CSS for administrative menu pages.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `admin_enqueue_scripts` hook.
					 */
					public function enqueue_admin_styles()
						{
							if(empty($_GET['page']) || strpos($_GET['page'], __NAMESPACE__) !== 0)
								return; // Nothing to do; NOT a plugin page in the administrative area.

							$deps = array(); // Plugin dependencies.

							wp_enqueue_style(__NAMESPACE__, $this->url('/client-s/css/menu-pages.min.css'), $deps, $this->version, 'all');
						}

					/**
					 * Adds JS for administrative menu pages.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `admin_enqueue_scripts` hook.
					 */
					public function enqueue_admin_scripts()
						{
							if(empty($_GET['page']) || strpos($_GET['page'], __NAMESPACE__) !== 0)
								return; // Nothing to do; NOT a plugin page in the administrative area.

							$deps = array('jquery'); // Plugin dependencies.

							wp_enqueue_script(__NAMESPACE__, $this->url('/client-s/js/menu-pages.min.js'), $deps, $this->version, TRUE);
						}

					/**
					 * Creates network admin menu pages.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `network_admin_menu` hook.
					 */
					public function add_network_menu_pages()
						{
							add_menu_page(__('Quick Cache', $this->text_domain), __('Quick Cache', $this->text_domain),
							              $this->network_cap, __NAMESPACE__, array($this, 'menu_page_options'),
							              $this->url('/client-s/images/menu-icon.png'));
						}

					/**
					 * Creates admin menu pages.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `admin_menu` hook.
					 */
					public function add_menu_pages()
						{
							if(is_multisite()) return; // Multisite networks MUST use network admin area.

							add_menu_page(__('Quick Cache', $this->text_domain), __('Quick Cache', $this->text_domain),
							              $this->cap, __NAMESPACE__, array($this, 'menu_page_options'),
							              $this->url('/client-s/images/menu-icon.png'));
						}

					/**
					 * Loads the admin menu page options.
					 *
					 * @since 140422 First documented version.
					 *
					 * @see add_network_menu_pages()
					 * @see add_menu_pages()
					 */
					public function menu_page_options()
						{
							require_once dirname(__FILE__).'/includes/menu-pages.php';
							$menu_pages = new menu_pages();
							$menu_pages->options();
						}

					/**
					 * Render admin notices; across all admin dashboard views.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `all_admin_notices` hook.
					 */
					public function all_admin_notices()
						{
							if(($notices = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array()))
								{
									$notices = $updated_notices = array_unique($notices); // De-dupe.

									foreach(array_keys($updated_notices) as $_key) if(strpos($_key, 'persistent-') !== 0)
										unset($updated_notices[$_key]); // Leave persistent notices; ditch others.
									unset($_key); // Housekeeping after updating notices.

									update_option(__NAMESPACE__.'_notices', $updated_notices);
								}
							if(current_user_can($this->cap)) foreach($notices as $_key => $_notice)
								{
									$_dismiss = ''; // Initialize empty string; e.g. reset value on each pass.
									if(strpos($_key, 'persistent-') === 0) // A dismissal link is needed in this case?
										{
											$_dismiss_css = 'display:inline-block; float:right; margin:0 0 0 15px; text-decoration:none; font-weight:bold;';
											$_dismiss     = add_query_arg(urlencode_deep(array(__NAMESPACE__ => array('dismiss_notice' => array('key' => $_key)), '_wpnonce' => wp_create_nonce())));
											$_dismiss     = '<a style="'.esc_attr($_dismiss_css).'" href="'.esc_attr($_dismiss).'">'.__('dismiss &times;', $this->text_domain).'</a>';
										}
									echo apply_filters(__METHOD__.'__notice', '<div class="updated"><p>'.$_notice.$_dismiss.'</p></div>', get_defined_vars());
								}
							unset($_key, $_notice, $_dismiss_css, $_dismiss); // Housekeeping.
						}

					/**
					 * Render admin errors; across all admin dashboard views.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `all_admin_notices` hook.
					 */
					public function all_admin_errors()
						{
							if(($errors = (is_array($errors = get_option(__NAMESPACE__.'_errors'))) ? $errors : array()))
								{
									$errors = $updated_errors = array_unique($errors); // De-dupe.

									foreach(array_keys($updated_errors) as $_key) if(strpos($_key, 'persistent-') !== 0)
										unset($updated_errors[$_key]); // Leave persistent errors; ditch others.
									unset($_key); // Housekeeping after updating notices.

									update_option(__NAMESPACE__.'_errors', $updated_errors);
								}
							if(current_user_can($this->cap)) foreach($errors as $_key => $_error)
								{
									$_dismiss = ''; // Initialize empty string; e.g. reset value on each pass.
									if(strpos($_key, 'persistent-') === 0) // A dismissal link is needed in this case?
										{
											$_dismiss_css = 'display:inline-block; float:right; margin:0 0 0 15px; text-decoration:none; font-weight:bold;';
											$_dismiss     = add_query_arg(urlencode_deep(array(__NAMESPACE__ => array('dismiss_error' => array('key' => $_key)), '_wpnonce' => wp_create_nonce())));
											$_dismiss     = '<a style="'.esc_attr($_dismiss_css).'" href="'.esc_attr($_dismiss).'">'.__('dismiss &times;', $this->text_domain).'</a>';
										}
									echo apply_filters(__METHOD__.'__error', '<div class="error"><p>'.$_error.$_dismiss.'</p></div>', get_defined_vars());
								}
							unset($_key, $_error, $_dismiss_css, $_dismiss); // Housekeeping.
						}

					/**
					 * Extends WP-Cron schedules.
					 *
					 * @since 140422 First documented version.
					 *
					 * @attaches-to `cron_schedules` filter.
					 *
					 * @param array $schedules An array of the current schedules.
					 *
					 * @return array Revised array of WP-Cron schedules.
					 */
					public function extend_cron_schedules($schedules)
						{
							$schedules['every15m'] = array('interval' => 900, 'display' => __('Every 15 Minutes', $this->text_domain));

							return apply_filters(__METHOD__, $schedules, get_defined_vars());
						}

					/**
					 * Wipes out all cache files in the cache directory.
					 *
					 * @since 140422 First documented version.
					 *
					 * @param boolean $manually Defaults to a `FALSE` value.
					 *    Pass as TRUE if the wipe is done manually by the site owner.
					 *
					 * @return integer Total files wiped by this routine (if any).
					 *
					 * @throws \exception If a wipe failure occurs.
					 */
					public function wipe_cache($manually = FALSE)
						{
							$counter = 0; // Initialize.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							if(!is_dir($cache_dir)) return $counter; // Nothing we can do.

							// @TODO When set_time_limit() is disabled by PHP configuration, display a warning message to users upon plugin activation
							@set_time_limit(1800); // In case of HUGE sites w/ a very large directory. Errors are ignored in case `set_time_limit()` is disabled.

							/** @var $_dir_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, '/.+/') as $_dir_file)
								{
									if(($_dir_file->isFile() || $_dir_file->isLink()) && strpos($_dir_file->getSubpathname(), '/') !== FALSE)
										// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
										// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.
										if(!unlink($_dir_file->getPathname())) // Throw exception if unable to delete.
											throw new \exception(sprintf(__('Unable to wipe file: `%1$s`.', $this->text_domain), $_dir_file->getPathname()));
										else $counter++; // Increment counter for each file we purge.

									else if($_dir_file->isDir()) // Directories are last in the iteration.
										if(!rmdir($_dir_file->getPathname())) // Throw exception if unable to delete.
											throw new \exception(sprintf(__('Unable to wipe dir: `%1$s`.', $this->text_domain), $_dir_file->getPathname()));
								}
							unset($_dir_file); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function clear_cache($manually = FALSE)
						{
							$counter = 0; // Initialize.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							// @TODO When set_time_limit() is disabled by PHP configuration, display a warning message to users upon plugin activation
							@set_time_limit(1800); // In case of HUGE sites w/ a very large directory. Errors are ignored in case `set_time_limit()` is disabled.

							$url                          = 'http://'.$_SERVER['HTTP_HOST'].$this->host_dir_token();
							$cache_path_no_scheme_quv_ext = $this->url_to_cache_path($url, '', '', $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT);
							$regex                        = '/^'.preg_quote($cache_dir, '/'). // Consider all schemes; all paths; and all possible variations.
							                                '\/[^\/]+\/'.preg_quote($cache_path_no_scheme_quv_ext, '/').
							                                '(?:\/index)?[.\/]/';

							/** @var $_dir_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, $regex) as $_dir_file)
								{
									if(($_dir_file->isFile() || $_dir_file->isLink()) && strpos($_dir_file->getSubpathname(), '/') !== FALSE)
										// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
										// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.
										if(!unlink($_dir_file->getPathname())) // Throw exception if unable to delete.
											throw new \exception(sprintf(__('Unable to clear file: `%1$s`.', $this->text_domain), $_dir_file->getPathname()));
										else $counter++; // Increment counter for each file we purge.

									else if($_dir_file->isDir()) // Directories are last in the iteration.
										if(!rmdir($_dir_file->getPathname())) // Throw exception if unable to delete.
											throw new \exception(sprintf(__('Unable to clear dir: `%1$s`.', $this->text_domain), $_dir_file->getPathname()));
								}
							unset($_dir_file); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function purge_cache() // i.e. the Quick Cache garbage collector.
						{
							$counter = 0; // Initialize.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							$max_age   = strtotime('-'.$this->options['cache_max_age']);
							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							// @TODO When set_time_limit() is disabled by PHP configuration, display a warning message to users upon plugin activation
							@set_time_limit(1800); // In case of HUGE sites w/ a very large directory. Errors are ignored in case `set_time_limit()` is disabled.

							/** @var $_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, '/.+/') as $_file) if($_file->isFile() || $_file->isLink())
								{
									if($_file->getMTime() < $max_age && strpos($_file->getSubpathname(), '/') !== FALSE)
										// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
										// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.
										if(!unlink($_file->getPathname())) // Throw exception if unable to delete.
											throw new \exception(sprintf(__('Unable to purge file: `%1$s`.', $this->text_domain), $_file->getPathname()));
										else $counter++; // Increment counter for each file we purge.
								}
							unset($_file); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_wipe_cache()
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							$counter = $this->wipe_cache();

							if($counter && is_admin()) // Change notifications cannot be turned off in the lite version.
								{
									$notices   = (is_array($notices = get_option(__NAMESPACE__.'_notices'))) ? $notices : array();
									$notices[] = '<img src="'.esc_attr($this->url('/client-s/images/wipe.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									             __('<strong>Quick Cache:</strong> detected significant changes. Found cache files (auto-wiping).', $this->text_domain);
									update_option(__NAMESPACE__.'_notices', $notices);
								}
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

							if(get_post_status($id) == 'auto-draft')
								return $counter; // Nothing to do.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							$counter += $this->auto_purge_home_page_cache(); // If enabled and necessary.
							$counter += $this->auto_purge_posts_page_cache(); // If enabled & applicable.

							if(!($permalink = get_permalink($id))) return $counter; // Nothing we can do.

							if(($type = get_post_type($id)) && ($type = get_post_type_object($type)) && !empty($type->labels->singular_name))
								$type_singular_name = $type->labels->singular_name; // Singular name for the post type.
							else $type_singular_name = __('Post', $this->text_domain); // Default value.

							$cache_path_no_scheme_quv_ext = $this->url_to_cache_path($permalink, '', '', $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT);
							$regex                        = '/^'.preg_quote($cache_dir, '/'). // Consider all schemes; all path paginations; and all possible variations.
							                                '\/[^\/]+\/'.preg_quote($cache_path_no_scheme_quv_ext, '/').
							                                '(?:\/index)?(?:\.|\/(?:page|comment\-page)\/[0-9]+[.\/])/';

							/** @var $_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, $regex) as $_file) if($_file->isFile() || $_file->isLink())
								{
									if(strpos($_file->getSubpathname(), '/') === FALSE) continue;
									// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
									// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.

									if(!unlink($_file->getPathname())) // Throw exception if unable to delete.
										throw new \exception(sprintf(__('Unable to auto-purge file: `%1$s`.', $this->text_domain), $_file->getPathname()));
									$counter++; // Increment counter for each file purge.

									if(!empty($_notices) || !$this->options['change_notifications_enable'] || !is_admin())
										continue; // Stop here; we already issued a notice, or this notice is N/A.

									$_notices   = (is_array($_notices = get_option(__NAMESPACE__.'_notices'))) ? $_notices : array();
									$_notices[] = '<img src="'.esc_attr($this->url('/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									              sprintf(__('<strong>Quick Cache:</strong> detected changes. Found cache file(s) for %1$s ID: <code>%2$s</code> (auto-purging).', $this->text_domain), $type_singular_name, $id);
									update_option(__NAMESPACE__.'_notices', $_notices);
								}
							unset($_file, $_notices); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_purge_home_page_cache()
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							if(!$this->options['cache_purge_home_page_enable'])
								return $counter; // Nothing to do.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							$cache_path_no_scheme_quv_ext = $this->url_to_cache_path(home_url('/'), '', '', $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT);
							$regex                        = '/^'.preg_quote($cache_dir, '/'). // Consider all schemes; all path paginations; and all possible variations.
							                                '\/[^\/]+\/'.preg_quote($cache_path_no_scheme_quv_ext, '/').
							                                '(?:\/index)?(?:\.|\/(?:page|comment\-page)\/[0-9]+[.\/])/';

							/** @var $_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, $regex) as $_file) if($_file->isFile() || $_file->isLink())
								{
									if(strpos($_file->getSubpathname(), '/') === FALSE) continue;
									// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
									// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.

									if(!unlink($_file->getPathname())) // Throw exception if unable to delete.
										throw new \exception(sprintf(__('Unable to auto-purge file: `%1$s`.', $this->text_domain), $_file->getPathname()));
									$counter++; // Increment counter for each file purge.

									if(!empty($_notices) || !$this->options['change_notifications_enable'] || !is_admin())
										continue; // Stop here; we already issued a notice, or this notice is N/A.

									$_notices   = (is_array($_notices = get_option(__NAMESPACE__.'_notices'))) ? $_notices : array();
									$_notices[] = '<img src="'.esc_attr($this->url('/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									              __('<strong>Quick Cache:</strong> detected changes. Found cache file(s) for the designated "Home Page" (auto-purging).', $this->text_domain);
									update_option(__NAMESPACE__.'_notices', $_notices);
								}
							unset($_file, $_notices); // Just a little housekeeping.

							return apply_filters(__METHOD__, $counter, get_defined_vars());
						}

					public function auto_purge_posts_page_cache()
						{
							$counter = 0; // Initialize.

							if(!$this->options['enable'])
								return $counter; // Nothing to do.

							if(!$this->options['cache_purge_posts_page_enable'])
								return $counter; // Nothing to do.

							$cache_dir = ABSPATH.$this->options['cache_dir'];
							if(!is_dir($cache_dir)) return $counter; // Nothing to do.

							$show_on_front  = get_option('show_on_front');
							$page_for_posts = get_option('page_for_posts');

							if(!in_array($show_on_front, array('posts', 'page'), TRUE))
								return $counter; // Nothing we can do in this case.

							if($show_on_front === 'page' && !$page_for_posts)
								return $counter; // Nothing we can do.

							if($show_on_front === 'posts') $posts_page = home_url('/');
							else if($show_on_front === 'page') $posts_page = get_permalink($page_for_posts);
							if(empty($posts_page)) return $counter; // Nothing we can do.

							$cache_path_no_scheme_quv_ext = $this->url_to_cache_path($posts_page, '', '', $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT);
							$regex                        = '/^'.preg_quote($cache_dir, '/'). // Consider all schemes; all path paginations; and all possible variations.
							                                '\/[^\/]+\/'.preg_quote($cache_path_no_scheme_quv_ext, '/').
							                                '(?:\/index)?(?:\.|\/(?:page|comment\-page)\/[0-9]+[.\/])/';

							/** @var $_file \RecursiveDirectoryIterator For IDEs. */
							foreach($this->dir_regex_iteration($cache_dir, $regex) as $_file) if($_file->isFile() || $_file->isLink())
								{
									if(strpos($_file->getSubpathname(), '/') === FALSE) continue;
									// Don't delete files in the immediate directory; e.g. `qc-advanced-cache` or `.htaccess`, etc.
									// Actual `http|https/...` cache files are nested. Files in the immediate directory are for other purposes.

									if(!unlink($_file->getPathname())) // Throw exception if unable to delete.
										throw new \exception(sprintf(__('Unable to auto-purge file: `%1$s`.', $this->text_domain), $_file->getPathname()));
									$counter++; // Increment counter for each file purge.

									if(!empty($_notices) || !$this->options['change_notifications_enable'] || !is_admin())
										continue; // Stop here; we already issued a notice, or this notice is N/A.

									$_notices   = (is_array($_notices = get_option(__NAMESPACE__.'_notices'))) ? $_notices : array();
									$_notices[] = '<img src="'.esc_attr($this->url('/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
									              __('<strong>Quick Cache:</strong> detected changes. Found cache file(s) for the designated "Posts Page" (auto-purging).', $this->text_domain);
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

							else if(is_file($dirname_abspath_wp_config = dirname(ABSPATH).'/wp-config.php'))
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

							if(preg_match('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[1-9][0-9\.]*|TRUE|([\'"])(?:[^0\'"]|[^\'"]{2,})\\2)\s*\)\s*;/i', $wp_config_file_contents))
								return $wp_config_file_contents; // It's already in there; no need to modify this file.

							if(!($wp_config_file_contents = $this->remove_wp_cache_from_wp_config()))
								return ''; // Unable to remove previous value.

							if(!($wp_config_file_contents = preg_replace('/^\s*(\<\?php|\<\?)\s+/i', '${1}'."\n"."define('WP_CACHE', TRUE);"."\n", $wp_config_file_contents, 1)))
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

							if(preg_match('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:0|FALSE|NULL|([\'"])0?\\2)\s*\)\s*;/i', $wp_config_file_contents))
								return $wp_config_file_contents; // It's already disabled; no need to modify this file.

							if(!($wp_config_file_contents = preg_replace('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[0-9\.]+|TRUE|FALSE|NULL|([\'"])[^\'"]*\\2)\s*\)\s*;/i', '', $wp_config_file_contents)))
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

							if(!is_file($cache_dir.'/qc-advanced-cache'))
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

							if(!is_dir($cache_dir) && mkdir($cache_dir, 0775, TRUE))
								{
									if(is_writable($cache_dir) && !is_file($cache_dir.'/.htaccess'))
										file_put_contents($cache_dir.'/.htaccess', $this->htaccess_deny);
								}
							if(!is_dir($cache_dir) || !is_writable($cache_dir) || !file_put_contents($cache_dir.'/qc-advanced-cache', time()))
								return NULL; // Failure; could not write cache entry. Special return value (NULL) in this case.

							return TRUE; // All done :-)
						}

					public function remove_advanced_cache()
						{
							$advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

							if(!is_file($advanced_cache_file)) return TRUE; // Already gone.

							if(is_readable($advanced_cache_file) && filesize($advanced_cache_file) === 0)
								return TRUE; // Already gone; i.e. it's empty already.

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

					public function check_blog_paths()
						{
							if(!$this->options['enable'])
								return; // Nothing to do.

							if(!is_multisite()) return; // N/A.

							if(!empty($_REQUEST[__NAMESPACE__]))
								return; // Skip on plugin actions.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							if(!is_file($cache_dir.'/qc-blog-paths'))
								$this->update_blog_paths();
						}

					public function update_blog_paths($enable_live_network_counts = NULL)
						{
							$value = // This hook actually rides on a filter.
								$enable_live_network_counts; // Filter value.

							if(!$this->options['enable'])
								return $value; // Nothing to do.

							if(!is_multisite()) return $value; // N/A.

							$cache_dir = ABSPATH.$this->options['cache_dir'];

							$base = '/'; // Initial default value.
							if(defined('PATH_CURRENT_SITE')) $base = PATH_CURRENT_SITE;
							else if(!empty($GLOBALS['base'])) $base = $GLOBALS['base'];

							if(!is_dir($cache_dir) && mkdir($cache_dir, 0775, TRUE))
								{
									if(is_writable($cache_dir) && !is_file($cache_dir.'/.htaccess'))
										file_put_contents($cache_dir.'/.htaccess', $this->htaccess_deny);
								}
							if(is_dir($cache_dir) && is_writable($cache_dir))
								{
									$paths = // Collect child blog paths from the WordPress database.
										$this->wpdb()->get_col("SELECT `path` FROM `".esc_sql($this->wpdb()->blogs)."` WHERE `deleted` <= '0'");

									foreach($paths as &$_path) // Strip base; these need to match `$host_dir_token`.
										$_path = '/'.ltrim(preg_replace('/^'.preg_quote($base, '/').'/', '', $_path), '/');
									unset($_path); // Housekeeping.

									file_put_contents($cache_dir.'/qc-blog-paths', serialize($paths));
								}
							return $value; // Pass through untouched (always).
						}

					public function add_settings_link($links)
						{
							$links[] = '<a href="options-general.php?page='.urlencode(__NAMESPACE__).'">'.__('Settings', $this->text_domain).'</a>';
							$links[] = '<br/><a href="'.esc_attr(add_query_arg(urlencode_deep(array('page' => __NAMESPACE__, __NAMESPACE__.'_pro_preview' => '1')), self_admin_url('/admin.php'))).'">'.__('Preview Pro Features', $this->text_domain).'</a>';
							$links[] = '<a href="'.esc_attr('http://www.websharks-inc.com/product/'.str_replace('_', '-', __NAMESPACE__).'/').'" target="_blank">'.__('Upgrade', $this->text_domain).'</a>';

							return apply_filters(__METHOD__, $links, get_defined_vars());
						}

					/*
					 * See also: `advanced-cache.tpl.php` duplicate.
					 */
					const CACHE_PATH_NO_SCHEME = 1; // Exclude scheme.
					const CACHE_PATH_NO_HOST = 2; // Exclude host (i.e. domain name).
					const CACHE_PATH_NO_PATH = 4; // Exclude path (i.e. the request URI).
					const CACHE_PATH_NO_PATH_INDEX = 8; // Exclude path index (i.e. no default `index`).
					const CACHE_PATH_NO_QUV = 16; // Exclude query, user & version salt.
					const CACHE_PATH_NO_QUERY = 32; // Exclude query string.
					const CACHE_PATH_NO_USER = 64; // Exclude user token.
					const CACHE_PATH_NO_VSALT = 128; // Exclude version salt.
					const CACHE_PATH_NO_EXT = 256; // Exclude extension.

					public function url_to_cache_path($url, $with_user_token = '', $with_version_salt = '', $flags = 0)
						{
							$cache_path        = ''; // Initialize.
							$url               = trim((string)$url);
							$with_user_token   = trim((string)$with_user_token);
							$with_version_salt = trim((string)$with_version_salt);

							if($url && strpos($url, '://') === FALSE)
								$url = '//'.ltrim($url, '/');

							if(!$url || !($url = parse_url($url)))
								return ''; // Invalid URL.

							if(!($flags & $this::CACHE_PATH_NO_SCHEME))
								{
									if(!empty($url['scheme']))
										$cache_path .= $url['scheme'].'/';
									else $cache_path .= is_ssl() ? 'https/' : 'http/';
								}
							if(!($flags & $this::CACHE_PATH_NO_HOST))
								{
									if(!empty($url['host']))
										$cache_path .= $url['host'].'/';
									else $cache_path .= $_SERVER['HTTP_HOST'].'/';
								}
							if(!($flags & $this::CACHE_PATH_NO_PATH))
								{
									if(!empty($url['path']) && strlen($url['path'] = trim($url['path'], '\\/'." \t\n\r\0\x0B")))
										$cache_path .= $url['path'].'/';
									else if(!($flags & $this::CACHE_PATH_NO_PATH_INDEX)) $cache_path .= 'index/';
								}
							if($this->is_extension_loaded('mbstring') && mb_check_encoding($cache_path, 'UTF-8'))
								$cache_path = mb_strtolower($cache_path, 'UTF-8');
							$cache_path = str_replace('.', '-', strtolower($cache_path));

							if(!($flags & $this::CACHE_PATH_NO_QUV))
								{
									if(!($flags & $this::CACHE_PATH_NO_QUERY))
										if(isset($url['query']) && $url['query'] !== '')
											$cache_path = rtrim($cache_path, '/').'.q/'.md5($url['query']).'/';

									if(!($flags & $this::CACHE_PATH_NO_USER))
										if($with_user_token !== '') // Allow a `0` value if desirable.
											$cache_path = rtrim($cache_path, '/').'.u/'.str_replace(array('/', '\\'), '-', $with_user_token).'/';

									if(!($flags & $this::CACHE_PATH_NO_VSALT))
										if($with_version_salt !== '') // Allow a `0` value if desirable.
											$cache_path = rtrim($cache_path, '/').'.v/'.str_replace(array('/', '\\'), '-', $with_version_salt).'/';
								}
							$cache_path = trim(preg_replace('/\/+/', '/', $cache_path), '/');
							$cache_path = preg_replace('/[^a-z0-9\/.]/i', '-', $cache_path);

							if(!($flags & $this::CACHE_PATH_NO_EXT))
								$cache_path .= '.html';

							return $cache_path;
						}

					public function host_token($dashify = FALSE)
						{
							$dashify = (integer)$dashify;
							static $tokens = array(); // Static cache.
							if(isset($tokens[$dashify])) return $tokens[$dashify];

							$host        = strtolower($_SERVER['HTTP_HOST']);
							$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host), '-') : $host;

							return ($tokens[$dashify] = $token_value);
						}

					public function host_dir_token($dashify = FALSE)
						{
							$dashify = (integer)$dashify;
							static $tokens = array(); // Static cache.
							if(isset($tokens[$dashify])) return $tokens[$dashify];

							$cache_dir      = ABSPATH.$this->options['cache_dir'];
							$host_dir_token = '/'; // Assume NOT multisite; or running it's own domain.

							if(is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL))
								{ // Multisite w/ sub-directories; need a valid sub-directory token.

									$base = '/'; // Initial default value.
									if(defined('PATH_CURRENT_SITE')) $base = PATH_CURRENT_SITE;
									else if(!empty($GLOBALS['base'])) $base = $GLOBALS['base'];

									$uri_minus_base = // Supports `/sub-dir/child-blog-sub-dir/` also.
										preg_replace('/^'.preg_quote($base, '/').'/', '', $_SERVER['REQUEST_URI']);

									list($host_dir_token) = explode('/', trim($uri_minus_base, '/'));
									$host_dir_token = (isset($host_dir_token[0])) ? '/'.$host_dir_token.'/' : '/';

									if($host_dir_token !== '/' // Perhaps NOT the main site?
									   && (!is_file($cache_dir.'/qc-blog-paths') // NOT a read/valid blog path?
									       || !in_array($host_dir_token, unserialize(file_get_contents($cache_dir.'/qc-blog-paths')), TRUE))
									) $host_dir_token = '/'; // Main site; e.g. this is NOT a real/valid child blog path.
								}
							$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host_dir_token), '-') : $host_dir_token;

							return ($tokens[$dashify] = $token_value);
						}

					public function dir_regex_iteration($dir, $regex)
						{
							$dir_iterator      = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
							$iterator_iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);
							$regex_iterator    = new \RegexIterator($iterator_iterator, $regex, \RegexIterator::MATCH, \RegexIterator::USE_KEY);

							return apply_filters(__METHOD__, $regex_iterator, get_defined_vars());
						}

					public function is_extension_loaded($extension)
						{
							static $is = array(); // Static cache.
							if(isset($is[$extension])) return $is[$extension];
							return ($is[$extension] = extension_loaded($extension));
						}

					public function function_is_possible($function)
						{
							static $disabled_functions; // Static cache.

							if(!isset($disabled_functions) && function_exists('ini_get'))
								{
									$disabled_functions = array();

									if(($disable_functions = trim(ini_get('disable_functions'))))
										$disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($disable_functions), NULL, PREG_SPLIT_NO_EMPTY));

									if(($blacklist_functions = trim(ini_get('suhosin.executor.func.blacklist'))))
										$disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($blacklist_functions), NULL, PREG_SPLIT_NO_EMPTY));
								}
							$possible = TRUE; // Assume it is.. (intialize).

							if(!function_exists($function) || !is_callable($function)
							   || ($disabled_functions && in_array(strtolower($function), $disabled_functions, TRUE))
							) $possible = FALSE; // Not possible.

							return apply_filters(__METHOD__, $possible, get_defined_vars());
						}

					public $htaccess_deny = "<IfModule authz_core_module>\n\tRequire all denied\n</IfModule>\n<IfModule !authz_core_module>\n\tdeny from all\n</IfModule>";
				}

				/**
				 * @return plugin Class instance.
				 */
				function plugin() // Easy reference.
					{
						return $GLOBALS[__NAMESPACE__];
					}

				$GLOBALS[__NAMESPACE__] = new plugin(); // New plugin instance.
				require_once dirname(__FILE__).'/includes/api-class.php';
			}
		else add_action('all_admin_notices', function () // Do NOT load in this case.
			{
				echo '<div class="error"><p>'. // Running multiple versions of this plugin at same time.
				     __('Please disable the LITE version of Quick Cache before you activate the PRO version.',
				        str_replace('_', '-', __NAMESPACE__)).'</p></div>';
			});
	}