<?php
namespace quick_cache // Root namespace.
	{
		if(!defined('WPINC')) // MUST have WordPress.
			exit('Do NOT access this file directly: '.basename(__FILE__));

		/*
		 * This file serves as a template for the Quick Cache plugin in WordPress.
		 * The Quick Cache plugin will fill the `%%` replacement codes automatically.
		 *    e.g. this file becomes: `/wp-content/advanced-cache.php`.
		 */

		/*
		 * Quick Cache configuration constants.
		 * ----------------------------------------------------------------------------
		 */

		/**
		 * Is this Quick Cache Pro?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
		 */
		define('QUICK_CACHE_PRO', FALSE); // Note that we do NOT check `if(defined())` here.

		if(!defined('QUICK_CACHE_ENABLE'))
			/**
			 * Is Quick Cache enabled?
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
			 */
			define('QUICK_CACHE_ENABLE', '%%QUICK_CACHE_ENABLE%%');

		if(!defined('QUICK_CACHE_DEBUGGING_ENABLE'))
			/**
			 * Is Quick Cache debugging enabled?
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
			 */
			define('QUICK_CACHE_DEBUGGING_ENABLE', '%%QUICK_CACHE_DEBUGGING_ENABLE%%');

		if(!defined('QUICK_CACHE_ALLOW_BROWSER_CACHE'))
			/**
			 * Should Quick Cache allow browsers to cache each document?
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
			 *
			 * @note If this is a `FALSE` (or an empty) value; Quick Cache will send no-cache headers.
			 *    If `TRUE`, Quick Cache will NOT send no-cache headers.
			 */
			define('QUICK_CACHE_ALLOW_BROWSER_CACHE', '%%QUICK_CACHE_ALLOW_BROWSER_CACHE%%');

		if(!defined('QUICK_CACHE_GET_REQUESTS'))
			define('QUICK_CACHE_GET_REQUESTS', '%%QUICK_CACHE_GET_REQUESTS%%');

		if(!defined('QUICK_CACHE_FEEDS_ENABLE'))
			define('QUICK_CACHE_FEEDS_ENABLE', '%%QUICK_CACHE_FEEDS_ENABLE%%');

		if(!defined('QUICK_CACHE_CACHE_404_REQUESTS'))
			define('QUICK_CACHE_CACHE_404_REQUESTS', '%%QUICK_CACHE_CACHE_404_REQUESTS%%');

		if(!defined('QUICK_CACHE_DIR'))
			/**
			 * Directory used to store cache files; relative to `ABSPATH`.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string Directory relative to `ABSPATH`.
			 */
			define('QUICK_CACHE_DIR', ABSPATH.'%%QUICK_CACHE_DIR%%');

		if(!defined('QUICK_CACHE_MAX_AGE'))
			define('QUICK_CACHE_MAX_AGE', '%%QUICK_CACHE_MAX_AGE%%');

		/*
		 * A unique filename for the special 404 Cache File (used when 404 caching is enabled).
		 */
		if(!defined('QUICK_CACHE_404_CACHE_FILENAME'))
			define('QUICK_CACHE_404_CACHE_FILENAME', '----404----');

		/*
		 * The heart of Quick Cache.
		 */

		class advanced_cache # `/wp-content/advanced-cache.php`
		{
			public $is_pro = FALSE; // Identifies the lite version of Quick Cache.
			public $timer = 0; // Microtime; defined by class constructor for debugging purposes.

			public $protocol = ''; // Calculated protocol; one of `http://` or `https://`.
			public $version_salt = ''; // Calculated version salt; set by filters only in the lite version.
			public $cache_path = ''; // Calculated cache path; absolute relative (no leading/trailing slashes).
			public $cache_file = ''; // Calculated location; defined by `maybe_start_output_buffering()`.
			public $cache_file_404 = ''; // Calculated location; defined by `maybe_start_output_buffering()`.
			public $salt_location = ''; // Calculated location; defined by `maybe_start_output_buffering()`.

			public $postload = array(); // Off by default; just an empty array.
			public $http_status; // See `maybe_filter_status_header_postload()`.
			public $is_wp_loaded_query = FALSE; // See: `wp_main_query_postload()`.
			public $is_404 = FALSE; // Set on `wp` by `wp_main_query_postload()`.
			public $site_url = ''; // Set on `wp` by `wp_main_query_postload()`.
			public $home_url = ''; // Set on `wp` by `wp_main_query_postload()`.
			public $is_user_logged_in = FALSE; // Set on `wp` by `wp_main_query_postload()`.
			public $is_maintenance = FALSE; // Set on `wp` by `wp_main_query_postload()`.
			public $plugin_file = ''; // Set on `wp` by `wp_main_query_postload()`.

			public $text_domain = ''; // Defined by class constructor; for translations.
			public $hooks = array(); // Array of advanced cache plugin hooks.

			const NC_DEBUG_PHP_SAPI_CLI = 'nc_debug_php_sapi_cli';

			const NC_DEBUG_QCAC_GET_VAR = 'nc_debug_qcac_get_var';

			const NC_DEBUG_NO_SERVER_HTTP_HOST = 'nc_debug_no_server_http_host';

			const NC_DEBUG_NO_SERVER_REQUEST_URI = 'nc_debug_no_server_request_uri';

			const NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT = 'nc_debug_quick_cache_allowed_constant';

			const NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR = 'nc_debug_quick_cache_allowed_server_var';

			const NC_DEBUG_DONOTCACHEPAGE_CONSTANT = 'nc_debug_donotcachepage_constant';

			const NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR = 'nc_debug_donotcachepage_server_var';

			const NC_DEBUG_POST_PUT_DEL_REQUEST = 'nc_debug_post_put_del_request';

			const NC_DEBUG_SELF_SERVE_REQUEST = 'nc_debug_self_serve_request';

			const NC_DEBUG_FEED_REQUEST = 'nc_debug_feed_request';

			const NC_DEBUG_WP_SYSTEMATICS = 'nc_debug_wp_systematics';

			const NC_DEBUG_WP_ADMIN = 'nc_debug_wp_admin';

			const NC_DEBUG_MS_FILES = 'nc_debug_ms_files';

			const NC_DEBUG_IS_LIKE_LOGGED_IN_USER = 'nc_debug_is_like_logged_in_user';

			const NC_DEBUG_IS_LOGGED_IN_USER = 'nc_debug_is_logged_in_user';

			const NC_DEBUG_GET_REQUEST_QUERIES = 'nc_debug_get_request_queries';

			const NC_DEBUG_404_REQUEST = 'nc_debug_404_request';

			const NC_DEBUG_MAINTENANCE_PLUGIN = 'nc_debug_maintenance_plugin';

			const NC_DEBUG_OB_ZLIB_CODING_TYPE = 'nc_debug_ob_zlib_coding_type';

			const NC_DEBUG_WP_ERROR_PAGE = 'nc_debug_wp_error_page';

			const NC_DEBUG_UNCACHEABLE_CONTENT_TYPE = 'nc_debug_uncacheable_content_type';

			const NC_DEBUG_UNCACHEABLE_STATUS = 'nc_debug_uncacheable_status';

			const NC_DEBUG_1ST_TIME_404_SYMLINK = 'nc_debug_1st_time_404_symlink';

			public function __construct() // Class constructor/cache handler.
				{
					if(!WP_CACHE || !QUICK_CACHE_ENABLE)
						return; // Not enabled.

					if(defined('WP_INSTALLING') || defined('RELOCATE'))
						return; // N/A; installing|relocating.

					$this->timer       = microtime(TRUE);
					$this->text_domain = str_replace('_', '-', __NAMESPACE__);

					$this->load_ac_plugins();
					$this->maybe_stop_browser_caching();
					$this->maybe_start_output_buffering();
				}

			public function load_ac_plugins()
				{
					if(!is_dir(WP_CONTENT_DIR.'/ac-plugins'))
						return; // Nothing to do here.

					$GLOBALS[__NAMESPACE__.'__advanced_cache']
						= $this; // Define now; so it's available for plugins.

					foreach((array)glob(WP_CONTENT_DIR.'/ac-plugins/*.php') as $_ac_plugin)
						if(is_file($_ac_plugin)) include_once $_ac_plugin;
					unset($_ac_plugin); // Houskeeping.
				}

			public function maybe_stop_browser_caching()
				{
					if(!empty($_GET['qcABC'])) return;
					if(QUICK_CACHE_ALLOW_BROWSER_CACHE) return;

					header_remove('Last-Modified');
					header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
					header('Cache-Control: no-cache, must-revalidate, max-age=0');
					header('Pragma: no-cache');
				}

			public function maybe_start_output_buffering()
				{
					if(strtoupper(PHP_SAPI) === 'CLI')
						return $this->maybe_set_debug_info($this::NC_DEBUG_PHP_SAPI_CLI);

					if(empty($_SERVER['HTTP_HOST']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_NO_SERVER_HTTP_HOST);

					if(empty($_SERVER['REQUEST_URI']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_NO_SERVER_REQUEST_URI);

					if(isset($_GET['qcAC']) && !filter_var($_GET['qcAC'], FILTER_VALIDATE_BOOLEAN))
						return $this->maybe_set_debug_info($this::NC_DEBUG_QCAC_GET_VAR);

					if(defined('QUICK_CACHE_ALLOWED') && !QUICK_CACHE_ALLOWED)
						return $this->maybe_set_debug_info($this::NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT);

					if(isset($_SERVER['QUICK_CACHE_ALLOWED']) && !$_SERVER['QUICK_CACHE_ALLOWED'])
						return $this->maybe_set_debug_info($this::NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR);

					if(defined('DONOTCACHEPAGE'))
						return $this->maybe_set_debug_info($this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT);

					if(isset($_SERVER['DONOTCACHEPAGE']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);

					if($this->is_post_put_del_request())
						return $this->maybe_set_debug_info($this::NC_DEBUG_POST_PUT_DEL_REQUEST);

					if(isset($_SERVER['REMOTE_ADDR'], $_SERVER['SERVER_ADDR']) && $_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR'])
						if(!$this->is_localhost()) return $this->maybe_set_debug_info($this::NC_DEBUG_SELF_SERVE_REQUEST);

					if(!QUICK_CACHE_FEEDS_ENABLE && $this->is_feed())
						return $this->maybe_set_debug_info($this::NC_DEBUG_FEED_REQUEST);

					if(preg_match('/\/(?:wp\-[^\/]+|xmlrpc)\.php(?:[?]|$)/', $_SERVER['REQUEST_URI']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_WP_SYSTEMATICS);

					if(is_admin() || preg_match('/\/wp-admin(?:[\/?]|$)/', $_SERVER['REQUEST_URI']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_WP_ADMIN);

					if(is_multisite() && preg_match('/\/files(?:[\/?]|$)/', $_SERVER['REQUEST_URI']))
						return $this->maybe_set_debug_info($this::NC_DEBUG_MS_FILES);

					if($this->is_like_user_logged_in()) // Commenters, password-protected access, or actually logged-in.
						return $this->maybe_set_debug_info($this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER);

					if(!QUICK_CACHE_GET_REQUESTS && $this->is_get_request_w_query() && (!isset($_GET['qcAC']) || !filter_var($_GET['qcAC'], FILTER_VALIDATE_BOOLEAN)))
						return $this->maybe_set_debug_info($this::NC_DEBUG_GET_REQUEST_QUERIES);

					$this->protocol       = $this->is_ssl() ? 'https://' : 'http://';
					$this->version_salt   = $this->apply_filters(__CLASS__.'__version_salt', '');
					$this->cache_path     = $this->url_to_cache_path($this->protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '', $this->version_salt);
					$this->cache_file     = QUICK_CACHE_DIR.'/'.$this->cache_path; // NOT considering a user cache at all in the lite version.
					$this->cache_file_404 = QUICK_CACHE_DIR.'/'.$this->url_to_cache_path($this->protocol.$_SERVER['HTTP_HOST'].'/'.QUICK_CACHE_404_CACHE_FILENAME);
					$this->salt_location  = ltrim($this->version_salt.' '.$this->protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

					if(is_file($this->cache_file) && filemtime($this->cache_file) >= strtotime('-'.QUICK_CACHE_MAX_AGE))
						{
							list($headers, $cache) = explode('<!--headers-->', file_get_contents($this->cache_file), 2);

							$headers_list = headers_list(); // Headers already sent (or ready to be sent).
							foreach(unserialize($headers) as $_header) // Preserves original headers sent with this file.
								if(!in_array($_header, $headers_list) && stripos($_header, 'Last-Modified:') !== 0) header($_header);
							unset($_header); // Just a little housekeeping.

							if(QUICK_CACHE_DEBUGGING_ENABLE && $this->is_html_xml_doc($cache)) // Only if HTML comments are possible.
								{
									$total_time = number_format(microtime(TRUE) - $this->timer, 5, '.', '');
									$cache .= "\n".'<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->';
									// translators: This string is actually NOT translatable because the `__()` function is not available at this point in the processing.
									$cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('Quick Cache fully functional :-) Cache file served for (%1$s) in %2$s seconds, on: %3$s.', $this->text_domain), $this->salt_location, $total_time, date('M jS, Y @ g:i a T'))).' -->';
								}
							exit($cache); // Exit with cache contents.
						}
					else // Start buffering output; we may need to cache the HTML generated by this request.
						{
							$this->postload['filter_status_header'] = TRUE; // Filter status header.
							register_shutdown_function(array($this, 'disable_wp_ob_end_flush_all_e_notice'));
							ob_start(array($this, 'output_buffer_callback_handler'), 0, 0); // Start locked output buffering.
						}
					return NULL; // Return value not applicable.
				}

			public function maybe_set_debug_info($reason_code, $reason = '')
				{
					if(!QUICK_CACHE_DEBUGGING_ENABLE)
						return; // Nothing to do.

					$reason = (string)$reason;
					if(!($reason_code = (string)$reason_code))
						return; // Not applicable.

					$this->postload['with_debug_info'] = array('reason_code' => $reason_code, 'reason' => $reason);
				}

			public function maybe_filter_status_header_postload()
				{
					if(empty($this->postload['filter_status_header']))
						return; // Nothing to do in this case.

					$_this = $this; // Reference needed by the closure below.
					add_filter('status_header', function ($status_header, $status_code) use ($_this)
						{
							if($status_code > 0) // Sending a status?
								$_this->http_status = (integer)$status_code;

							return $status_header; // Pass through this filter.

						}, PHP_INT_MAX, 2);
				}

			public function maybe_set_debug_info_postload()
				{
					if(!QUICK_CACHE_DEBUGGING_ENABLE)
						return; // Nothing to do.

					if(is_admin()) return; // Not applicable.

					if(strtoupper(PHP_SAPI) === 'CLI')
						return; // Let's not run the risk here.

					if(empty($this->postload['with_debug_info']) || !is_array($this->postload['with_debug_info']))
						return; // Nothing to do in this case either.

					if(!isset($this->postload['with_debug_info']['reason_code'], $this->postload['with_debug_info']['reason']))
						return; // Nothing to do in this case either.

					$_this = $this; // Need this reference in the closure below (PHP v.5.3 compat).
					add_action('shutdown', function () use ($_this) // Debug info in the shutdown phase.
						{
							if($_this->has_a_cacheable_content_type() && (is_404() || is_front_page() || is_home() || is_singular() || is_archive() || is_post_type_archive() || is_tax() || is_search() || is_feed()))
								echo (string)$_this->maybe_add_nc_debug_info(NULL, $_this->postload['with_debug_info']['reason_code'], $_this->postload['with_debug_info']['reason']);
						}, -(PHP_INT_MAX - 10));
				}

			public function wp_main_query_postload() // Fires on `wp` action hook.
				{
					if($this->is_wp_loaded_query || is_admin())
						return; // Nothing to do.

					if(!is_main_query())
						return; // Not the main query.

					$this->is_wp_loaded_query = TRUE;
					$this->is_404             = is_404();
					$this->site_url           = site_url();
					$this->home_url           = home_url();
					$this->is_user_logged_in  = is_user_logged_in();
					$this->is_maintenance     = function_exists('is_maintenance') && is_maintenance();

					if(function_exists('\\'.__NAMESPACE__.'\\plugin'))
						$this->plugin_file = plugin()->file;
				}

			public function disable_wp_ob_end_flush_all_e_notice()
				{
					error_reporting(error_reporting() & ~E_NOTICE);
				}

			public function output_buffer_callback_handler($buffer, $phase)
				{
					if($phase !== (PHP_OUTPUT_HANDLER_START | PHP_OUTPUT_HANDLER_END))
						// Quick Cache does NOT chunk it's output buffering; so this should never happen.
						throw new \exception(sprintf(__('Unexpected OB phase: `%1$s`.', $this->text_domain), $phase));

					# Exclusion checks; there are MANY of these...

					$cache = trim((string)$buffer);
					if(!isset($cache[0])) // Allows a `0`.
						return FALSE; // Don't cache an empty buffer.

					if(isset($_GET['qcAC']) && !filter_var($_GET['qcAC'], FILTER_VALIDATE_BOOLEAN))
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_QCAC_GET_VAR);

					if(defined('QUICK_CACHE_ALLOWED') && !QUICK_CACHE_ALLOWED)
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT);

					if(isset($_SERVER['QUICK_CACHE_ALLOWED']) && !$_SERVER['QUICK_CACHE_ALLOWED'])
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR);

					if(defined('DONOTCACHEPAGE')) // WP Super Cache compatible.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT);

					if(isset($_SERVER['DONOTCACHEPAGE'])) // WP Super Cache compatible.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);

					if($this->is_user_logged_in) // Actually logged into the site.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_IS_LOGGED_IN_USER);

					if($this->is_like_user_logged_in()) // Commenters, password-protected access, or actually logged-in.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER); // This uses a separate debug notice.

					if($this->is_404 && !QUICK_CACHE_CACHE_404_REQUESTS) // Not caching 404 errors.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_404_REQUEST);

					if(strpos($cache, '<body id="error-page">') !== FALSE)
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_WP_ERROR_PAGE);

					if(!$this->has_a_cacheable_content_type()) // Exclude non-HTML/XML content types.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_UNCACHEABLE_CONTENT_TYPE);

					if(!$this->has_a_cacheable_status()) // This will catch WP Maintenance Mode too.
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_UNCACHEABLE_STATUS);

					if($this->is_maintenance) // <http://wordpress.org/extend/plugins/maintenance-mode>
						return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_MAINTENANCE_PLUGIN);

					if(function_exists('zlib_get_coding_type') && zlib_get_coding_type()
					   && (!($zlib_oc = ini_get('zlib.output_compression')) || !filter_var($zlib_oc, FILTER_VALIDATE_BOOLEAN))
					) return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_OB_ZLIB_CODING_TYPE);

					# Cache directory checks. The cache file directory is created here if necessary.

					if(!is_dir(QUICK_CACHE_DIR) && mkdir(QUICK_CACHE_DIR, 0775, TRUE) && !is_file(QUICK_CACHE_DIR.'/.htaccess'))
						file_put_contents(QUICK_CACHE_DIR.'/.htaccess', $this->htaccess_deny); // We know it's writable here.

					if(!is_dir($cache_file_dir = dirname($this->cache_file))) $cache_file_dir_writable = mkdir($cache_file_dir, 0775, TRUE);
					if(empty($cache_file_dir_writable) && !is_writable($cache_file_dir)) // Only check if it's writable, if we didn't just successfully create it.
						throw new \exception(sprintf(__('Cache directory not writable. Quick Cache needs this directory please: `%1$s`. Set permissions to `755` or higher; `777` might be needed in some cases.', $this->text_domain), $cache_file_dir));

					# This is where a new 404 request might be detected for the first time; and where the 404 error file already exists in this case.

					if($this->is_404 && is_file($this->cache_file_404))
						if(!symlink($this->cache_file_404, $this->cache_file))
							throw new \exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', $this->text_domain), $this->cache_file, $this->cache_file_404, QUICK_CACHE_DIR));
						else return $this->maybe_add_nc_debug_info($buffer, $this::NC_DEBUG_1ST_TIME_404_SYMLINK);

					/* ------- Otherwise, we need to construct & store a new cache file. -------- */

					if(QUICK_CACHE_DEBUGGING_ENABLE && $this->is_html_xml_doc($cache)) // Only if HTML comments are possible.
						{
							$total_time = number_format(microtime(TRUE) - $this->timer, 5, '.', '');
							$cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('Quick Cache file path: %1$s', $this->text_domain), str_replace(ABSPATH, '', $this->is_404 ? $this->cache_file_404 : $this->cache_file))).' -->';
							$cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('Quick Cache file built for (%1$s) in %2$s seconds, on: %3$s.', $this->text_domain),
							                                                ($this->is_404) ? '404 [error document]' : $this->salt_location, $total_time, date('M jS, Y @ g:i a T'))).' -->';
							$cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('This Quick Cache file will auto-expire (and be rebuilt) on: %1$s (based on your configured expiration time).', $this->text_domain), date('M jS, Y @ g:i a T', strtotime('+'.QUICK_CACHE_MAX_AGE)))).' -->';
						}
					$cache_file_tmp = $this->cache_file.'.'.uniqid('', TRUE).'.tmp'; // Cache creation is atomic; e.g. tmp file w/ rename.
					/*
					 * This is NOT a 404, or it is 404 and the 404 cache file doesn't yet exist (so we need to create it).
					 */
					if($this->is_404) // This is a 404; let's create 404 cache file and symlink to it.
						{
							if(file_put_contents($cache_file_tmp, serialize(headers_list()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $this->cache_file_404))
								if(symlink($this->cache_file_404, $this->cache_file)) // If this fails an exception will be thrown down below.
									return $cache; // Return the newly built cache; with possible debug information also.

						} // NOT a 404; let's write a new cache file.
					else if(file_put_contents($cache_file_tmp, serialize(headers_list()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $this->cache_file))
						return $cache; // Return the newly built cache; with possible debug information also.

					@unlink($cache_file_tmp); // Clean this up (if it exists); and throw an exception with information for the site owner.
					throw new \exception(sprintf(__('Quick Cache: failed to write cache file for: `%1$s`; possible permissions issue (or race condition), please check your cache directory: `%2$s`.', $this->text_domain), $_SERVER['REQUEST_URI'], QUICK_CACHE_DIR));
				}

			public function maybe_add_nc_debug_info($doc = NULL, $reason_code = '', $reason = '')
				{
					if(!QUICK_CACHE_DEBUGGING_ENABLE)
						return (string)$doc; // Nothing to do.

					if(isset($doc)) // Allow a NULL value through.
						// This way it can be bypassed in a case where all we want is
						//    just the debug info itself; e.g. when we want the return value.
						if(!$this->is_html_xml_doc($doc = (string)$doc))
							return $doc; // Nothing to do.

					$doc    = (string)$doc;
					$reason = (string)$reason;
					if(!($reason_code = (string)$reason_code))
						return $doc; // Not applicable.

					if(!$reason) switch($reason_code)
					{
						case $this::NC_DEBUG_PHP_SAPI_CLI:
								$reason = __('because `PHP_SAPI` reports that you are currently running from the command line.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_QCAC_GET_VAR:
								$reason = __('because `$_GET[\'qcAC\']` is set to a boolean-ish FALSE value.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_NO_SERVER_HTTP_HOST:
								$reason = __('because `$_SERVER[\'HTTP_HOST\']` is missing from your server configuration.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_NO_SERVER_REQUEST_URI:
								$reason = __('because `$_SERVER[\'REQUEST_URI\']` is missing from your server configuration.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT:
								$reason = __('because the PHP constant `QUICK_CACHE_ALLOWED` has been set to a boolean-ish `FALSE` value at runtime. Perhaps by WordPress itself, or by one of your themes/plugins. This usually means that you have a theme/plugin intentionally disabling the cache on this page; and it\'s usually for a very good reason.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR:
								$reason = __('because the environment variable `$_SERVER[\'QUICK_CACHE_ALLOWED\']` has been set to a boolean-ish `FALSE` value at runtime. Perhaps by WordPress itself, or by one of your themes/plugins. This usually means that you have a theme/plugin intentionally disabling the cache on this page; and it\'s usually for a very good reason.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT:
								$reason = __('because the PHP constant `DONOTCACHEPAGE` has been set at runtime. Perhaps by WordPress itself, or by one of your themes/plugins. This usually means that you have a theme/plugin intentionally disabling the cache on this page; and it\'s usually for a very good reason.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR:
								$reason = __('because the environment variable `$_SERVER[\'DONOTCACHEPAGE\']` has been set at runtime. Perhaps by WordPress itself, or by one of your themes/plugins. This usually means that you have a theme/plugin intentionally disabling the cache on this page; and it\'s usually for a very good reason.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_POST_PUT_DEL_REQUEST:
								$reason = __('because `$_SERVER[\'REQUEST_METHOD\']` is `POST`, `PUT` or `DELETE`. These request types should never (ever) be cached in any way.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_SELF_SERVE_REQUEST:
								$reason = __('because `$_SERVER[\'REMOTE_ADDR\']` === `$_SERVER[\'SERVER_ADDR\']`; i.e. a self-serve request. DEVELOPER TIP: if you are testing on a localhost installation, please add `define(\'LOCALHOST\', TRUE);` to your `/wp-config.php` file while you run tests :-) Remove it (or set it to a `FALSE` value) once you go live on the web.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_FEED_REQUEST:
								$reason = __('because `$_SERVER[\'REQUEST_URI\']` indicates this is a `/feed`; and the configuration of this site says not to cache XML-based feeds.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_WP_SYSTEMATICS:
								$reason = __('because `$_SERVER[\'REQUEST_URI\']` indicates this is a `wp-` or `xmlrpc` file; i.e. a WordPress systematic file. WordPress systematics are never (ever) cached in any way.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_WP_ADMIN:
								$reason = __('because `$_SERVER[\'REQUEST_URI\']` or the `is_admin()` function indicates this is an administrative area of the site.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_MS_FILES:
								$reason = __('because `$_SERVER[\'REQUEST_URI\']` indicates this is a Multisite Network; and this was a request for `/files/*`, not a page.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_IS_LOGGED_IN_USER:
						case $this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER:
								$reason = __('because the current user visiting this page (usually YOU), appears to be logged-in. The current configuration says NOT to cache pages for logged-in visitors. This message may also appear if you have an active PHP session on this site, or if you\'ve left (or replied to) a comment recently. If this message continues, please clear your cookies and try again.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_GET_REQUEST_QUERIES:
								$reason = __('because `$_GET` contains query string data. The current configuration says NOT to cache GET requests with a query string.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_404_REQUEST:
								$reason = __('because the WordPress `is_404()` Conditional Tag says the current page is a 404 error. The current configuration says NOT to cache 404 errors.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_MAINTENANCE_PLUGIN:
								$reason = __('because a plugin running on this installation says this page is in Maintenance Mode; i.e. is not available publicly at this time.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_OB_ZLIB_CODING_TYPE:
								$reason = __('because Quick Cache is unable to cache already-compressed output. Please use `mod_deflate` w/ Apache; or use `zlib.output_compression` in your `php.ini` file. Quick Cache is NOT compatible with `ob_gzhandler()` and others like this.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_WP_ERROR_PAGE:
								$reason = __('because the contents of this document contain `<body id="error-page">`, which indicates this is an auto-generated WordPress error message.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_UNCACHEABLE_CONTENT_TYPE:
								$reason = __('because a `Content-Type:` header was set via PHP at runtime. The header contains a MIME type which is NOT a variation of HTML or XML. This header might have been set by your hosting company, by WordPress itself; or by one of your themes/plugins.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_UNCACHEABLE_STATUS:
								$reason = __('because a `Status:` header (or an `HTTP/` header) was set via PHP at runtime. The header contains a non-`2xx` status code. This indicates the current page was not loaded successfully. This header might have been set by your hosting company, by WordPress itself; or by one of your themes/plugins.', $this->text_domain);
								break; // Break switch handler.

						case $this::NC_DEBUG_1ST_TIME_404_SYMLINK:
								$reason = __('because the WordPress `is_404()` Conditional Tag says the current page is a 404 error; and this is the first time it\'s happened on this page. Your current configuration says that 404 errors SHOULD be cached, so Quick Cache built a cached symlink which points future requests for this location to your already-cached 404 error document. If you reload this page (assuming you don\'t clear the cache before you do so); you should get a cached version of your 404 error document. This message occurs ONCE for each new/unique 404 error request.', $this->text_domain);
								break; // Break switch handler.

						default: // Default case handler.
							$reason = __('due to an unexpected behavior in the application. Please report this as a bug!', $this->text_domain);
							break; // Break switch handler.
					}
					return $doc."\n".'<!-- '.htmlspecialchars(sprintf(__('Quick Cache is NOT caching this page, %1$s', $this->text_domain), $reason)).' -->';
				}

			/*
			 * See also: `quick-cache.inc.php` duplicate.
			 * NOTE: the call to `is_ssl()` in this duplicate uses `$this->is_ssl()` because `is_ssl()`
			 *    may NOT be available in this routine; i.e. it's not been loaded up yet.
			 *
			 * @TODO Find a way to centralize this section so it can be shared between both classes easily.
			 */

			/**
			 * Exclude scheme from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_SCHEME = 1;

			/**
			 * Exclude host (i.e. domain name) from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_HOST = 2;

			/**
			 * Exclude path from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_PATH = 4;

			/**
			 * Exclude path index (i.e. no default `index`) from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_PATH_INDEX = 8;

			/**
			 * Exclude query, user & version salt from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_QUV = 16;

			/**
			 * Exclude query string from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_QUERY = 32;

			/**
			 * Exclude user token from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_USER = 64;

			/**
			 * Exclude version salt from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_VSALT = 128;

			/**
			 * Exclude extension from cache path.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var integer Part of a bitmask.
			 */
			const CACHE_PATH_NO_EXT = 256;

			/**
			 * Converts a URL into a `cache/path`.
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string  $url The input URL to convert.
			 * @param string  $with_user_token Optional user token (if applicable).
			 * @param string  $with_version_salt Optional version salt (if applicable).
			 * @param integer $flags Optional flags; a bitmask provided by `CACHE_PATH_*` constants.
			 *
			 * @return string The resulting `cache/path` based on the input `$url`.
			 */
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
							else $cache_path .= $this->is_ssl() ? 'https/' : 'http/';
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

			/**
			 * Produces a token based on the current `$_SERVER['HTTP_HOST']`.
			 *
			 * @since 140422 First documented version.
			 *
			 * @param boolean $dashify Optional, defaults to a `FALSE` value.
			 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
			 *
			 * @return string Token based on the current `$_SERVER['HTTP_HOST']`.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function host_token($dashify = FALSE)
				{
					$dashify = (integer)$dashify;
					static $tokens = array(); // Static cache.
					if(isset($tokens[$dashify])) return $tokens[$dashify];

					$host        = strtolower($_SERVER['HTTP_HOST']);
					$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host), '-') : $host;

					return ($tokens[$dashify] = $token_value);
				}

			/**
			 * Produces a token based on the current blog sub-directory
			 *    (i.e. in the case of a sub-directory multisite network).
			 *
			 * @since 140422 First documented version.
			 *
			 * @param boolean $dashify Optional, defaults to a `FALSE` value.
			 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
			 *
			 * @return string Produces a token based on the current blog sub-directory
			 *    (i.e. in the case of a sub-directory multisite network).
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 *
			 * @see plugin\update_blog_paths()
			 */
			public function host_dir_token($dashify = FALSE)
				{
					$dashify = (integer)$dashify;
					static $tokens = array(); // Static cache.
					if(isset($tokens[$dashify])) return $tokens[$dashify];

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
							   && (!is_file(QUICK_CACHE_DIR.'/qc-blog-paths') // NOT a read/valid blog path?
							       || !in_array($host_dir_token, unserialize(file_get_contents(QUICK_CACHE_DIR.'/qc-blog-paths')), TRUE))
							) $host_dir_token = '/'; // Main site; e.g. this is NOT a real/valid child blog path.
						}
					$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host_dir_token), '-') : $host_dir_token;

					return ($tokens[$dashify] = $token_value);
				}

			public function is_post_put_del_request()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					if(!empty($_SERVER['REQUEST_METHOD']))
						if(in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('POST', 'PUT', 'DELETE'), TRUE))
							return ($is = TRUE);

					return ($is = FALSE);
				}

			public function is_get_request_w_query()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					if(!empty($_GET) || isset($_SERVER['QUERY_STRING'][0]))
						if(!(isset($_GET['qcABC']) && count($_GET) === 1)) // Ignore this special case.
							return ($is = TRUE);

					return ($is = FALSE);
				}

			public function is_like_user_logged_in()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					/* This checks for a PHP session; i.e. session_start() in PHP where you're dealing with a user session.
					 * WordPress itself does not use sessions, but some plugins/themes do. If you have a theme/plugin using
					 * sessions, and there is an active session open, we consider you logged in; and thus, no caching.
					 * SID is a PHP internal constant to identify a PHP session. It's the same regardless of the app. If PHP
					 * starts a session, SID is defined.
					 */
					if(defined('SID') && SID) return ($is = TRUE); // Session.

					$logged_in_cookies[] = 'comment_author_'; // Comment (and/or reply) authors.
					$logged_in_cookies[] = 'wp-postpass_'; // Password access to protected posts.

					$logged_in_cookies[] = (defined('AUTH_COOKIE')) ? AUTH_COOKIE : 'wordpress_';
					$logged_in_cookies[] = (defined('SECURE_AUTH_COOKIE')) ? SECURE_AUTH_COOKIE : 'wordpress_sec_';
					$logged_in_cookies[] = (defined('LOGGED_IN_COOKIE')) ? LOGGED_IN_COOKIE : 'wordpress_logged_in_';
					$logged_in_cookies   = '/^(?:'.implode('|', array_map(function ($logged_in_cookie)
							{
								return preg_quote($logged_in_cookie, '/'); // Escape.

							}, $logged_in_cookies)).')/';
					$test_cookie         = (defined('TEST_COOKIE')) ? TEST_COOKIE : 'wordpress_test_cookie';

					foreach($_COOKIE as $_key => $_value) if($_key !== $test_cookie)
						if(preg_match($logged_in_cookies, $_key) && $_value) return ($is = TRUE);
					unset($_key, $_value); // Housekeeping.

					return ($is = FALSE);
				}

			public function is_localhost()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					if(defined('LOCALHOST') && LOCALHOST) return ($is = TRUE);

					if(!defined('LOCALHOST') && !empty($_SERVER['HTTP_HOST']))
						if(preg_match('/localhost|127\.0\.0\.1/i', $_SERVER['HTTP_HOST']))
							return ($is = TRUE);

					return ($is = FALSE);
				}

			public function is_feed()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					if(preg_match('/\/feed(?:[\/?]|$)/', $_SERVER['REQUEST_URI']))
						return ($is = TRUE);

					if(isset($_REQUEST['feed']))
						return ($is = TRUE);

					return ($is = FALSE);
				}

			public function is_ssl()
				{
					static $is; // Cache.
					if(isset($is)) return $is;

					if(!empty($_SERVER['SERVER_PORT']))
						if($_SERVER['SERVER_PORT'] === '443')
							return ($is = TRUE);

					if(!empty($_SERVER['HTTPS']))
						if($_SERVER['HTTPS'] === '1' || strcasecmp($_SERVER['HTTPS'], 'on') === 0)
							return ($is = TRUE);

					if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
						if(strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0)
							return ($is = TRUE);

					return ($is = FALSE);
				}

			public function is_html_xml_doc($doc)
				{
					if(($doc = (string)$doc))
						if(stripos($doc, '</html>') !== FALSE || stripos($doc, '<?xml') === 0)
							return TRUE;
					return FALSE; // Not an HTML/XML document.
				}

			public function has_a_cacheable_content_type()
				{
					static $has; // Cache.
					if(isset($has)) return $has;

					foreach(headers_list() as $_header)
						if(stripos($_header, 'Content-Type:') === 0)
							$content_type = $_header; // Last one.
					unset($_header); // Just a little housekeeping.

					if(isset($content_type[0]) && stripos($content_type, 'html') === FALSE && stripos($content_type, 'xml') === FALSE && stripos($content_type, __NAMESPACE__) === FALSE)
						return ($has = FALSE); // Do NOT cache data sent by scripts serving other MIME types.

					return ($has = TRUE); // Assume that it is by default, we are within WP after all.
				}

			public function has_a_cacheable_status()
				{
					static $has; // Cache.
					if(isset($has)) return $has;

					if(isset($this->http_status[0]) && $this->http_status[0] !== '2' && $this->http_status !== '404')
						return ($has = FALSE); // WP `status_header()` sent a non-2xx & non-404 status code.
					/*
					 * PHP's `headers_list()` currently does NOT include `HTTP/` headers.
					 *    This means the following routine will never catch a status sent by `status_header()`.
					 *    However, I'm leaving this check in place in case a future version of PHP adds support for this.
					 *
					 *    For now, we monitor `status_header()` via {@link maybe_filter_status_header_postload()} so that will suffice.
					 */
					foreach(headers_list() as $_header)
						if(preg_match('/^(?:Retry\-After\:\s+(?P<retry>.+)|Status\:\s+(?P<status>[0-9]+)|HTTP\/[0-9]+\.[0-9]+\s+(?P<http_status>[0-9]+))/i', $_header, $_m))
							if(!empty($_m['retry']) || (!empty($_m['status']) && $_m['status'][0] !== '2' && $_m['status'] !== '404')
							   || (!empty($_m['http_status']) && $_m['http_status'][0] !== '2' && $_m['http_status'] !== '404')
							) return ($has = FALSE); // Don't cache (anything that's NOT a 2xx or 404 status).
					unset($_header); // Just a little housekeeping.

					return ($has = TRUE); // Assume that it is by default, we are within WP after all.
				}

			/**
			 * Checks if a PHP extension is loaded up.
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string $extension A PHP extension slug (i.e. extension name).
			 *
			 * @return boolean `TRUE` if the extension is loaded; else `FALSE`.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_extension_loaded($extension)
				{
					static $is = array(); // Static cache.
					if(isset($is[$extension])) return $is[$extension];
					return ($is[$extension] = extension_loaded($extension));
				}

			public function hook_id($function)
				{
					if(is_string($function))
						return $function;

					if(is_object($function)) // Closure.
						$function = array($function, '');
					else $function = (array)$function;

					if(is_object($function[0]))
						return spl_object_hash($function[0]).$function[1];

					else if(is_string($function[0]))
						return $function[0].'::'.$function[1];

					throw new \exception(__('Invalid hook.', $this->text_domain));
				}

			public function add_hook($hook, $function, $priority = 10, $accepted_args = 1)
				{
					$this->hooks[$hook][$priority][$this->hook_id($function)]
						= array('function' => $function, 'accepted_args' => (integer)$accepted_args);
					return TRUE; // Always returns true.
				}

			public function add_action() // Simple `add_hook()` alias.
				{
					return call_user_func_array(array($this, 'add_hook'), func_get_args());
				}

			public function add_filter() // Simple `add_hook()` alias.
				{
					return call_user_func_array(array($this, 'add_hook'), func_get_args());
				}

			public function remove_hook($hook, $function, $priority = 10)
				{
					if(!isset($this->hooks[$hook][$priority][$this->hook_id($function)]))
						return FALSE; // Nothing to remove in this case.

					unset($this->hooks[$hook][$priority][$this->hook_id($function)]);
					if(!$this->hooks[$hook][$priority]) unset($this->hooks[$hook][$priority]);
					return TRUE; // Existed before it was removed in this case.
				}

			public function remove_action() // Simple `remove_hook()` alias.
				{
					return call_user_func_array(array($this, 'remove_hook'), func_get_args());
				}

			public function remove_filter() // Simple `remove_hook()` alias.
				{
					return call_user_func_array(array($this, 'remove_hook'), func_get_args());
				}

			public function do_action($hook)
				{
					if(empty($this->hooks[$hook]))
						return; // No hooks.

					$hook_actions = $this->hooks[$hook];
					ksort($hook_actions); // Sort by priority.

					$args = func_get_args(); // We'll need these below.
					foreach($hook_actions as $_hook_action) foreach($_hook_action as $_action)
						{
							if(!isset($_action['function'], $_action['accepted_args']))
								continue; // Not a valid filter in this case.

							call_user_func_array($_action['function'], array_slice($args, 1, $_action['accepted_args']));
						}
					unset($_hook_action, $_action); // Housekeeping.
				}

			public function apply_filters($hook, $value)
				{
					if(empty($this->hooks[$hook]))
						return $value; // No hooks.

					$hook_filters = $this->hooks[$hook];
					ksort($hook_filters); // Sort by priority.

					$args = func_get_args(); // We'll need these below.
					foreach($hook_filters as $_hook_filter) foreach($_hook_filter as $_filter)
						{
							if(!isset($_filter['function'], $_filter['accepted_args']))
								continue; // Not a valid filter in this case.

							$args[1] = $value; // Continously update the argument `$value`.
							$value   = call_user_func_array($_filter['function'], array_slice($args, 1, $_filter['accepted_args']));
						}
					unset($_hook_filter, $_filter); // Housekeeping.

					return $value; // With applied filters.
				}

			/**
			 * Apache `.htaccess` rules that deny public access to the contents of a directory.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string `.htaccess` fules.
			 */
			public $htaccess_deny = "<IfModule authz_core_module>\n\tRequire all denied\n</IfModule>\n<IfModule !authz_core_module>\n\tdeny from all\n</IfModule>";
		}

		function __($string, $text_domain) // Polyfill `\__()`.
			{
				static $__exists; // Static cache.

				if(($__exists || function_exists('__')) && ($__exists = TRUE))
					return \__($string, $text_domain);

				return $string; // Not possible (yet).
			}

		$GLOBALS[__NAMESPACE__.'__advanced_cache'] = new advanced_cache();
	}
namespace // Global namespace.
	{
		function wp_cache_postload() // See: `wp-settings.php`.
			{
				$advanced_cache = $GLOBALS['quick_cache__advanced_cache'];
				/** * @var $advanced_cache \quick_cache\advanced_cache */

				if(!empty($advanced_cache->postload['filter_status_header']))
					$advanced_cache->maybe_filter_status_header_postload();

				if(!empty($advanced_cache->postload['with_debug_info']))
					$advanced_cache->maybe_set_debug_info_postload();

				add_action('wp', array($advanced_cache, 'wp_main_query_postload'), PHP_INT_MAX);
			}
	}