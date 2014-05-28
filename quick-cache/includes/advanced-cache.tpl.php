<?php
/**
 * Quick Cache (Advanced Cache Handler)
 *
 * This file serves as a template for the Quick Cache plugin in WordPress.
 * The Quick Cache plugin will fill the `%%` replacement codes automatically.
 *    This file becomes: `/wp-content/advanced-cache.php`.
 *
 * @package quick_cache\advanced_cache
 * @since 140422 First documented version.
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 2
 */
namespace quick_cache
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	/**
	 * Quick Cache Pro flag.
	 *
	 * @since 140422 First documented version.
	 *
	 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
	 */
	define('QUICK_CACHE_PRO', TRUE); // Note that we do NOT check `if(defined())` here.

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
		 * Allow browsers to cache each document?
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
		/**
		 * Cache `$_GET` requests w/ a query string?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
		 */
		define('QUICK_CACHE_GET_REQUESTS', '%%QUICK_CACHE_GET_REQUESTS%%');

	if(!defined('QUICK_CACHE_CACHE_404_REQUESTS'))
		/**
		 * Cache 404 errors?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
		 */
		define('QUICK_CACHE_CACHE_404_REQUESTS', '%%QUICK_CACHE_CACHE_404_REQUESTS%%');

	if(!defined('QUICK_CACHE_FEEDS_ENABLE'))
		/**
		 * Cache XML/RSS/Atom feeds?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string|integer|boolean A boolean-ish value; e.g. `1` or `0`.
		 */
		define('QUICK_CACHE_FEEDS_ENABLE', '%%QUICK_CACHE_FEEDS_ENABLE%%');

	if(!defined('QUICK_CACHE_DIR'))
		/**
		 * Directory used to store cache files; relative to `ABSPATH`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Absolute server directory path.
		 */
		define('QUICK_CACHE_DIR', ABSPATH.'%%QUICK_CACHE_DIR%%');

	if(!defined('QUICK_CACHE_MAX_AGE'))
		/**
		 * Cache expiration time.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Anything compatible with PHP's {@link \strtotime()}.
		 */
		define('QUICK_CACHE_MAX_AGE', '%%QUICK_CACHE_MAX_AGE%%');

	if(!defined('QUICK_CACHE_404_CACHE_FILENAME'))
		/**
		 * 404 file name (if applicable).
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique file name that will not conflict with real paths.
		 *    This should NOT include the extension; basename only please.
		 */
		define('QUICK_CACHE_404_CACHE_FILENAME', '----404----');

	/**
	 * Quick Cache (Advanced Cache Handler)
	 *
	 * @package quick_cache\advanced_cache
	 * @since 140422 First documented version.
	 */
	class advanced_cache # `/wp-content/advanced-cache.php`
	{
		/**
		 * Identifies the pro version of Quick Cache.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var boolean `TRUE` for Quick Cache Pro; else `FALSE`.
		 */
		public $is_pro = FALSE;

		/**
		 * Flagged as `TRUE` if QC advanced cache is active & running.
		 *
		 * @since 14xxxx Improving output buffers
		 *
		 * @var boolean `TRUE` if QC advanced cache is active & running.
		 */
		public $is_running = FALSE;

		/**
		 * Microtime; defined by class constructor for debugging purposes.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var float Result of a call to {@link \microtime()}.
		 */
		public $timer = 0;

		/**
		 * Calculated protocol; one of `http://` or `https://`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var float One of `http://` or `https://`.
		 */
		public $protocol = '';

		/**
		 * Calculated version salt; set by site configuration data.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string|mixed Any scalar value does fine.
		 */
		public $version_salt = '';

		/**
		 * Calculated cache path for the current request;
		 *    absolute relative (no leading/trailing slashes).
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Absolute relative (no leading/trailing slashes).
		 *    Defined by {@link maybe_start_output_buffering()}.
		 */
		public $cache_path = '';

		/**
		 * Calculated cache file location for the current request; absolute path.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Cache file location for the current request; absolute path.
		 *    Defined by {@link maybe_start_output_buffering()}.
		 */
		public $cache_file = '';

		/**
		 * Centralized 404 cache file location; absolute path.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Centralized 404 cache file location; absolute path.
		 *    Defined by {@link maybe_start_output_buffering()}.
		 */
		public $cache_file_404 = '';

		/**
		 * A possible version salt (string value); followed by the current request location.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Version salt (string value); followed by the current request location.
		 *    Defined by {@link maybe_start_output_buffering()}.
		 */
		public $salt_location = '';

		/**
		 * Array of data targeted at the postload phase.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var array Data and/or flags that work with various postload handlers.
		 */
		public $postload = array();

		/**
		 * Last HTTP status code passed through {@link \status_header}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var null|integer Last HTTP status code (if applicable).
		 *
		 * @see maybe_filter_status_header_postload()
		 */
		public $http_status;

		/**
		 * An array of debug info.
		 *
		 * @since 14xxxx Improve output buffering.
		 *
		 * @var array An array of debug info; i.e. `reason_code` and `reason` (optional).
		 */
		public $debug_info = array('reason_code' => '', 'reason' => '');

		/**
		 * Have we caught the main WP loaded being loaded yet?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var boolean `TRUE` if main query has been loaded; else `FALSE`.
		 *
		 * @see wp_main_query_postload()
		 */
		public $is_wp_loaded_query = FALSE;

		/**
		 * Is the current request a WordPress 404 error?
		 *
		 * @since 140422 First documented version.
		 *
		 * @var boolean `TRUE` if is a 404 error; else `FALSE`.
		 *
		 * @see wp_main_query_postload()
		 */
		public $is_404 = FALSE;

		/**
		 * Current WordPress {@link \site_url()}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Current WordPress {@link \site_url()}.
		 *
		 * @see wp_main_query_postload()
		 */
		public $site_url = '';

		/**
		 * Current WordPress {@link \home_url()}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Current WordPress {@link \home_url()}.
		 *
		 * @see wp_main_query_postload()
		 */
		public $home_url = '';

		/**
		 * Flag for {@link \is_user_loged_in()}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var boolean `TRUE` if {@link \is_user_loged_in()}; else `FALSE`.
		 *
		 * @see wp_main_query_postload()
		 */
		public $is_user_logged_in = FALSE;

		/**
		 * Flag for {@link \is_maintenance()}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var boolean `TRUE` if {@link \is_maintenance()}; else `FALSE`.
		 *
		 * @see wp_main_query_postload()
		 */
		public $is_maintenance = FALSE;

		/**
		 * Value for {@link plugin::$file()}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string The value of {@link plugin::$file()}.
		 *
		 * @see wp_main_query_postload()
		 */
		public $plugin_file = '';

		/**
		 * Text domain for translations; based on `__NAMESPACE__`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string Defined by class constructor; for translations.
		 */
		public $text_domain = '';

		/**
		 * Array of hooks added by plugins.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var array An array of any hooks added by plugins.
		 */
		public $hooks = array();

		/**
		 * No-cache because of the current {@link \PHP_SAPI}.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_PHP_SAPI_CLI = 'nc_debug_php_sapi_cli';

		/**
		 * No-cache because the current request includes the `?qcAC=0` parameter.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_QCAC_GET_VAR = 'nc_debug_qcac_get_var';

		/**
		 * No-cache because of a missing `$_SERVER['HTTP_HOST']`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_NO_SERVER_HTTP_HOST = 'nc_debug_no_server_http_host';

		/**
		 * No-cache because of a missing `$_SERVER['REQUEST_URI']`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_NO_SERVER_REQUEST_URI = 'nc_debug_no_server_request_uri';

		/**
		 * No-cache because the {@link \QUICK_CACHE_ALLOWED} constant says not to.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT = 'nc_debug_quick_cache_allowed_constant';

		/**
		 * No-cache because the `$_SERVER['QUICK_CACHE_ALLOWED']` environment variable says not to.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR = 'nc_debug_quick_cache_allowed_server_var';

		/**
		 * No-cache because the {@link \DONOTCACHEPAGE} constant says not to.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_DONOTCACHEPAGE_CONSTANT = 'nc_debug_donotcachepage_constant';

		/**
		 * No-cache because the `$_SERVER['DONOTCACHEPAGE']` environment variable says not to.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR = 'nc_debug_donotcachepage_server_var';

		/**
		 * No-cache because the current request method is `POST|PUT|DELETE`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_POST_PUT_DEL_REQUEST = 'nc_debug_post_put_del_request';

		/**
		 * No-cache because the current request originated from the server itself.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_SELF_SERVE_REQUEST = 'nc_debug_self_serve_request';

		/**
		 * No-cache because the current request is for a feed.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_FEED_REQUEST = 'nc_debug_feed_request';

		/**
		 * No-cache because the current request is systematic.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_WP_SYSTEMATICS = 'nc_debug_wp_systematics';

		/**
		 * No-cache because the current request is for an administrative area.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_WP_ADMIN = 'nc_debug_wp_admin';

		/**
		 * No-cache because the current request is multisite `/files/`.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_MS_FILES = 'nc_debug_ms_files';

		/**
		 * No-cache because the current user is like a logged-in user.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_IS_LIKE_LOGGED_IN_USER = 'nc_debug_is_like_logged_in_user';

		/**
		 * No-cache because the current user is logged into the site.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_IS_LOGGED_IN_USER = 'nc_debug_is_logged_in_user';

		/**
		 * No-cache because the current request contains a query string.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_GET_REQUEST_QUERIES = 'nc_debug_get_request_queries';

		/**
		 * No-cache because the current request is a 404 error.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_404_REQUEST = 'nc_debug_404_request';

		/**
		 * No-cache because the requested page is currently in maintenance mode.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_MAINTENANCE_PLUGIN = 'nc_debug_maintenance_plugin';

		/**
		 * No-cache because the current request is being compressed by an incompatible ZLIB coding type.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_OB_ZLIB_CODING_TYPE = 'nc_debug_ob_zlib_coding_type';

		/**
		 * No-cache because the current request resulted in a WP error message.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_WP_ERROR_PAGE = 'nc_debug_wp_error_page';

		/**
		 * No-cache because the current request is serving an uncacheable content type.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_UNCACHEABLE_CONTENT_TYPE = 'nc_debug_uncacheable_content_type';

		/**
		 * No-cache because the current request sent a non-2xx & non-404 status code.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_UNCACHEABLE_STATUS = 'nc_debug_uncacheable_status';

		/**
		 * No-cache because this is a new 404 error that we are symlinking.
		 *
		 * @since 140422 First documented version.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_1ST_TIME_404_SYMLINK = 'nc_debug_1st_time_404_symlink';

		/**
		 * No-cache because we detected an early buffer termination.
		 *
		 * @since 14xxxx Improving output buffer.
		 *
		 * @var string A unique string identifier in the set of `NC_DEBUG_` constants.
		 */
		const NC_DEBUG_EARLY_BUFFER_TERMINATION = 'nc_debug_early_buffer_termination';

		/**
		 * Class constructor/cache handler.
		 *
		 * @since 140422 First documented version.
		 */
		public function __construct()
		{
			if(!WP_CACHE || !QUICK_CACHE_ENABLE)
				return; // Not enabled.

			if(defined('WP_INSTALLING') || defined('RELOCATE'))
				return; // N/A; installing|relocating.

			$this->is_running  = TRUE;
			$this->timer       = microtime(TRUE);
			$this->text_domain = str_replace('_', '-', __NAMESPACE__);

			$this->load_ac_plugins();
			$this->register_shutdown_flag();
			$this->maybe_stop_browser_caching();
			$this->maybe_start_output_buffering();
		}

		/**
		 * Loads any advanced cache plugin files found inside `/wp-content/ac-plugins`.
		 *
		 * @since 140422 First documented version.
		 */
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

		/**
		 * Registers a shutdown flag.
		 *
		 * @since 14xxxx Improving output buffer.
		 *
		 * @note In `/wp-settings.php`, Quick Cache is loaded before WP registers its own shutdown function.
		 * Therefore, this flag is set before {@link shutdown_action_hook()} fires, and thus before {@link wp_ob_end_flush_all()}.
		 *
		 * @see http://www.php.net/manual/en/function.register-shutdown-function.php
		 */
		public function register_shutdown_flag()
		{
			register_shutdown_function(function ()
			{
				$GLOBALS[__NAMESPACE__.'__shutdown_flag'] = -1;
			});
		}

		/**
		 * Sends no-cache headers (if applicable).
		 *
		 * @since 140422 First documented version.
		 */
		public function maybe_stop_browser_caching()
		{
			if(QUICK_CACHE_ALLOW_BROWSER_CACHE)
				return; // Allow in this case.

			if(!empty($_GET['qcABC']) && filter_var($_GET['qcABC'], FILTER_VALIDATE_BOOLEAN))
				return; // The query var says it's OK here.

			header_remove('Last-Modified');
			header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
			header('Cache-Control: no-cache, must-revalidate, max-age=0');
			header('Pragma: no-cache');
		}

		/**
		 * Start output buffering (if applicable); or serve a cache file (if possible).
		 *
		 * @since 140422 First documented version.
		 *
		 * @note This is a vital part of Quick Cache. This method serves existing (fresh) cache files.
		 *    It is also responsible for beginning the process of collecting the output buffer.
		 */
		public function maybe_start_output_buffering()
		{
			if(strcasecmp(PHP_SAPI, 'cli') === 0)
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
				ob_start(array($this, 'output_buffer_callback_handler')); // Start output buffering.
			}
			return NULL; // Return value not applicable.
		}

		/**
		 * Used to setup debug info (if enabled).
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string $reason_code One of the `NC_DEBUG_` constants.
		 * @param string $reason Optionally override the built-in description with a custom message.
		 */
		public function maybe_set_debug_info($reason_code, $reason = '')
		{
			if(!QUICK_CACHE_DEBUGGING_ENABLE)
				return; // Nothing to do.

			$reason = (string)$reason;
			if(!($reason_code = (string)$reason_code))
				return; // Not applicable.

			$this->debug_info = array('reason_code' => $reason_code, 'reason' => $reason);
		}

		/**
		 * Filters WP {@link \status_header()} (if applicable).
		 *
		 * @since 140422 First documented version.
		 */
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

		/**
		 * Appends `NC_DEBUG_` info in the WordPress `shutdown` phase (if applicable).
		 *
		 * @since 140422 First documented version.
		 */
		public function maybe_set_debug_info_postload()
		{
			if(!QUICK_CACHE_DEBUGGING_ENABLE)
				return; // Nothing to do.

			if(is_admin()) return; // Not applicable.

			if(strcasecmp(PHP_SAPI, 'cli') === 0)
				return; // Let's not run the risk here.

			$_this = $this; // Need this reference in the closure below (PHP v.5.3 compat).
			add_action('shutdown', function () use ($_this) // Debug info in the shutdown phase.
			{
				if($_this->debug_info && $_this->has_a_cacheable_content_type() && (is_404() || is_front_page() || is_home() || is_singular() || is_archive() || is_post_type_archive() || is_tax() || is_search() || is_feed()))
					echo (string)$_this->maybe_get_nc_debug_info($_this->debug_info['reason_code'], $_this->debug_info['reason']);
			}, -(PHP_INT_MAX - 10));
		}

		/**
		 * Grab details from WP and the Quick Cache plugin itself,
		 *    after the main query is loaded (if at all possible).
		 *
		 * This is where we have a chance to grab any values we need from WordPress; or from the QC plugin.
		 *    It is EXTREMEMLY important that we NOT attempt to grab any object references here.
		 *    Anything acquired in this phase should be stored as a scalar value.
		 *    See {@link output_buffer_callback_handler()} for further details.
		 *
		 * @since 140422 First documented version.
		 *
		 * @attaches-to `wp` hook.
		 */
		public function wp_main_query_postload()
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

		/**
		 * Output buffer handler; i.e. the cache file generator.
		 *
		 * @note We CANNOT depend on any WP functionality here; it will cause problems.
		 *    Anything we need from WP should be saved in the postload phase as a scalar value.
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string  $buffer The buffer from {@link \ob_start()}.
		 * @param integer $phase A set of bitmask flags.
		 *
		 * @return string|boolean The output buffer, or `FALSE` to indicate no change.
		 *
		 * @throws \exception If unable to handle output buffering for any reason.
		 *
		 * @attaches-to {@link \ob_start()}
		 */
		public function output_buffer_callback_handler($buffer, $phase)
		{
			if(!($phase & PHP_OUTPUT_HANDLER_END)) // We do NOT chunk the buffer; so this should NOT occur.
				throw new \exception(sprintf(__('Unexpected OB phase: `%1$s`.', $this->text_domain), $phase));

			# Exclusion checks; there are MANY of these...

			$cache = trim((string)$buffer);
			if(!isset($cache[0])) // Allows a `0`.
				return FALSE; // Don't cache an empty buffer.

			if(!isset($GLOBALS[__NAMESPACE__.'__shutdown_flag']))
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_EARLY_BUFFER_TERMINATION);

			if(isset($_GET['qcAC']) && !filter_var($_GET['qcAC'], FILTER_VALIDATE_BOOLEAN))
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_QCAC_GET_VAR);

			if(defined('QUICK_CACHE_ALLOWED') && !QUICK_CACHE_ALLOWED)
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_QUICK_CACHE_ALLOWED_CONSTANT);

			if(isset($_SERVER['QUICK_CACHE_ALLOWED']) && !$_SERVER['QUICK_CACHE_ALLOWED'])
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_QUICK_CACHE_ALLOWED_SERVER_VAR);

			if(defined('DONOTCACHEPAGE')) // WP Super Cache compatible.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT);

			if(isset($_SERVER['DONOTCACHEPAGE'])) // WP Super Cache compatible.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);

			if($this->is_user_logged_in) // Actually logged into the site.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_IS_LOGGED_IN_USER);

			if($this->is_like_user_logged_in()) // Commenters, password-protected access, or actually logged-in.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER); // This uses a separate debug notice.

			if($this->is_404 && !QUICK_CACHE_CACHE_404_REQUESTS) // Not caching 404 errors.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_404_REQUEST);

			if(strpos($cache, '<body id="error-page">') !== FALSE)
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_WP_ERROR_PAGE);

			if(!function_exists('http_response_code') && stripos($cache, '<title>database error</title>') !== FALSE)
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_WP_ERROR_PAGE);

			if(!$this->has_a_cacheable_content_type()) // Exclude non-HTML/XML content types.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_UNCACHEABLE_CONTENT_TYPE);

			if(!$this->has_a_cacheable_status()) // This will catch WP Maintenance Mode too.
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_UNCACHEABLE_STATUS);

			if($this->is_maintenance) // <http://wordpress.org/extend/plugins/maintenance-mode>
				return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_MAINTENANCE_PLUGIN);

			if(function_exists('zlib_get_coding_type') && zlib_get_coding_type()
			   && (!($zlib_oc = ini_get('zlib.output_compression')) || !filter_var($zlib_oc, FILTER_VALIDATE_BOOLEAN))
			) return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_OB_ZLIB_CODING_TYPE);

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
				else return (boolean)$this->maybe_set_debug_info($this::NC_DEBUG_1ST_TIME_404_SYMLINK);

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

		/**
		 * Gets `NC_DEBUG_` info (if applicable).
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string $reason_code One of the `NC_DEBUG_` constants.
		 * @param string $reason Optional; to override the default description with a custom message.
		 *
		 * @return string The debug info; i.e. full description (if applicable).
		 */
		public function maybe_get_nc_debug_info($reason_code = '', $reason = '')
		{
			if(!QUICK_CACHE_DEBUGGING_ENABLE)
				return ''; // Not applicable.

			$reason = (string)$reason;
			if(!($reason_code = (string)$reason_code))
				return ''; // Not applicable.

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

				case $this::NC_DEBUG_EARLY_BUFFER_TERMINATION:
					$reason = __('because Quick Cache detected an early output buffer termination. This may happen when a theme/plugin ends, cleans, or flushes all output buffers before reaching the PHP shutdown phase. It\'s not always a bad thing. Sometimes it is necessary for a theme/plugin to do this. However, in this scenario it is NOT possible to cache the output; since Quick Cache is effectively disabled at runtime when this occurs.', $this->text_domain);
					break; // Break switch handler.

				default: // Default case handler.
					$reason = __('due to an unexpected behavior in the application. Please report this as a bug!', $this->text_domain);
					break; // Break switch handler.
			}
			return "\n".'<!-- '.htmlspecialchars(sprintf(__('Quick Cache is NOT caching this page, %1$s', $this->text_domain), $reason)).' -->';
		}

		/*
		 * See also: `quick-cache.inc.php` duplicates.
		 *    @TODO Find a way to centralize this section so it can be shared between both classes easily.
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
		 * Produces a token based on the current site's base directory.
		 *
		 * @since 14xxxx First documented version.
		 *
		 * @param boolean $dashify Optional, defaults to a `FALSE` value.
		 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
		 *
		 * @return string Produces a token based on the current site's base directory;
		 *    (i.e. in the case of a sub-directory multisite network).
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 *
		 * @see plugin\clear_cache()
		 * @see plugin\update_blog_paths()
		 */
		public function host_base_token($dashify = FALSE)
		{
			$dashify = (integer)$dashify;
			static $tokens = array(); // Static cache.
			if(isset($tokens[$dashify])) return $tokens[$dashify];

			$host_base_token = '/'; // Assume NOT multisite; or running it's own domain.

			if(is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL))
			{ // Multisite w/ sub-directories; need a valid sub-directory token.

				if(defined('PATH_CURRENT_SITE')) $host_base_token = PATH_CURRENT_SITE;
				else if(!empty($GLOBALS['base'])) $host_base_token = $GLOBALS['base'];

				$host_base_token = trim($host_base_token, '\\/'." \t\n\r\0\x0B");
				$host_base_token = (isset($host_base_token[0])) ? '/'.$host_base_token.'/' : '/';
			}
			$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host_base_token), '-') : $host_base_token;

			return ($tokens[$dashify] = $token_value);
		}

		/**
		 * Produces a token based on the current blog's sub-directory.
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
		 * @see plugin\clear_cache()
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

				$uri_minus_base = // Supports `/sub-dir/child-blog-sub-dir/` also.
					preg_replace('/^'.preg_quote($this->host_base_token(), '/').'/', '', $_SERVER['REQUEST_URI']);

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

		/**
		 * Produces tokens for the current site's base directory & current blog's sub-directory.
		 *
		 * @since 140422 First documented version.
		 *
		 * @param boolean $dashify Optional, defaults to a `FALSE` value.
		 *    If `TRUE`, the tokens are returned with dashes in place of `[^a-z0-9\/]`.
		 *
		 * @return string Tokens for the current site's base directory & current blog's sub-directory.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 *
		 * @see clear_cache()
		 * @see update_blog_paths()
		 */
		public function host_base_dir_tokens($dashify = FALSE)
		{
			return preg_replace('/\/{2,}/', '/', $this->host_base_token($dashify).$this->host_dir_token($dashify));
		}

		/**
		 * Is the current request method `POST|PUT|DELETE`?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
		public function is_post_put_del_request()
		{
			static $is; // Cache.
			if(isset($is)) return $is;

			if(!empty($_SERVER['REQUEST_METHOD']))
				if(in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('POST', 'PUT', 'DELETE'), TRUE))
					return ($is = TRUE);

			return ($is = FALSE);
		}

		/**
		 * Does the current request include a query string?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
		public function is_get_request_w_query()
		{
			static $is; // Cache.
			if(isset($is)) return $is;

			if(!empty($_GET) || isset($_SERVER['QUERY_STRING'][0]))
				if(!(isset($_GET['qcABC']) && count($_GET) === 1)) // Ignore this special case.
					return ($is = TRUE);

			return ($is = FALSE);
		}

		/**
		 * Should the current user be considered a logged-in user?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
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

		/**
		 * Are we in a LOCALHOST environment?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
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

		/**
		 * Is the current request for a feed?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
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

		/**
		 * Is the current request over SSL?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
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

		/**
		 * Is a document/string an HTML/XML doc; or no?
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string $doc Input string/document to check.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 */
		public function is_html_xml_doc($doc)
		{
			if(($doc = (string)$doc))
				if(stripos($doc, '</html>') !== FALSE || stripos($doc, '<?xml') === 0)
					return TRUE;
			return FALSE; // Not an HTML/XML document.
		}

		/**
		 * Does the current request have a cacheable content type?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
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

		/**
		 * Does the current request have a cacheable HTTP status code?
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if yes; else `FALSE`.
		 *
		 * @note The return value of this function is cached to reduce overhead on repeat calls.
		 */
		public function has_a_cacheable_status()
		{
			static $has; // Cache.
			if(isset($has)) return $has;

			if(function_exists('http_response_code') && ($http_response_code = (integer)http_response_code()))
				$this->http_status = $http_response_code;

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

		/**
		 * Assigns an ID to each callable attached to a hook/filter.
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string|callable|mixed $function A string or a callable.
		 *
		 * @return string Hook ID for the given `$function`.
		 *
		 * @throws \exception If the hook/function is invalid (i.e. it's not possible to generate an ID).
		 */
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

		/**
		 * Adds a new hook (works with both actions & filters).
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string                $hook The name of a hook to attach to.
		 * @param string|callable|mixed $function A string or a callable.
		 * @param integer               $priority Hook priority; defaults to `10`.
		 * @param integer               $accepted_args Max number of args that should be passed to the `$function`.
		 *
		 * @return boolean This always returns a `TRUE` value.
		 */
		public function add_hook($hook, $function, $priority = 10, $accepted_args = 1)
		{
			$this->hooks[$hook][$priority][$this->hook_id($function)]
				= array('function' => $function, 'accepted_args' => (integer)$accepted_args);
			return TRUE; // Always returns true.
		}

		/**
		 * Adds a new action hook.
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean This always returns a `TRUE` value.
		 *
		 * @see add_hook()
		 */
		public function add_action() // Simple `add_hook()` alias.
		{
			return call_user_func_array(array($this, 'add_hook'), func_get_args());
		}

		/**
		 * Adds a new filter.
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean This always returns a `TRUE` value.
		 *
		 * @see add_hook()
		 */
		public function add_filter() // Simple `add_hook()` alias.
		{
			return call_user_func_array(array($this, 'add_hook'), func_get_args());
		}

		/**
		 * Removes a hook (works with both actions & filters).
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string                $hook The name of a hook to remove.
		 * @param string|callable|mixed $function A string or a callable.
		 * @param integer               $priority Hook priority; defaults to `10`.
		 *
		 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
		 */
		public function remove_hook($hook, $function, $priority = 10)
		{
			if(!isset($this->hooks[$hook][$priority][$this->hook_id($function)]))
				return FALSE; // Nothing to remove in this case.

			unset($this->hooks[$hook][$priority][$this->hook_id($function)]);
			if(!$this->hooks[$hook][$priority]) unset($this->hooks[$hook][$priority]);
			return TRUE; // Existed before it was removed in this case.
		}

		/**
		 * Removes an action.
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
		 *
		 * @see remove_hook()
		 */
		public function remove_action() // Simple `remove_hook()` alias.
		{
			return call_user_func_array(array($this, 'remove_hook'), func_get_args());
		}

		/**
		 * Removes a filter.
		 *
		 * @since 140422 First documented version.
		 *
		 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
		 *
		 * @see remove_hook()
		 */
		public function remove_filter() // Simple `remove_hook()` alias.
		{
			return call_user_func_array(array($this, 'remove_hook'), func_get_args());
		}

		/**
		 * Runs any callables attached to an action.
		 *
		 * @since 140422 First documented version.
		 */
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

		/**
		 * Runs any callables attached to a filter.
		 *
		 * @since 140422 First documented version.
		 *
		 * @param string $hook The name of a filter hook.
		 * @param mixed  $value The value to filter.
		 *
		 * @return mixed The filtered `$value`.
		 */
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

	/**
	 * Polyfill for {@link \__()}.
	 *
	 * @since 140422 First documented version.
	 *
	 * @param string $string String to translate.
	 * @param string $text_domain Plugin text domain.
	 *
	 * @return string Possibly translated string.
	 */
	function __($string, $text_domain) // Polyfill `\__()`.
	{
		static $__exists; // Static cache.

		if(($__exists || function_exists('__')) && ($__exists = TRUE))
			return \__($string, $text_domain);

		return $string; // Not possible (yet).
	}

	/**
	 * Global Quick Cache {@link advanced_cache} instance.
	 *
	 * @since 140422 First documented version.
	 *
	 * @var advanced_cache Global instance reference.
	 */
	$GLOBALS[__NAMESPACE__.'__advanced_cache'] = new advanced_cache();
}
namespace // Global namespace.
{
	/**
	 * Postload event handler; overrides core WP function.
	 *
	 * @since 140422 First documented version.
	 *
	 * @note See `/wp-settings.php` around line #226.
	 */
	function wp_cache_postload() // See: `wp-settings.php`.
	{
		$advanced_cache = $GLOBALS['quick_cache__advanced_cache'];
		/** * @var $advanced_cache \quick_cache\advanced_cache */
		if(!$advanced_cache->is_running) return;

		if(!empty($advanced_cache->postload['filter_status_header']))
			$advanced_cache->maybe_filter_status_header_postload();

		$advanced_cache->maybe_set_debug_info_postload();

		add_action('wp', array($advanced_cache, 'wp_main_query_postload'), PHP_INT_MAX);
	}
}