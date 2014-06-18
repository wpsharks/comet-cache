<?php
namespace quick_cache // Root namespace.
{
	if(!defined('WPINC')) // MUST have WordPress.
		exit('Do NOT access this file directly: '.basename(__FILE__));

	if(!class_exists('\\'.__NAMESPACE__.'\\share'))
	{
		/**
		 * Quick Cache (Shared Methods)
		 *
		 * @package quick_cache\share
		 * @since 14xxxx Reorganizing class members.
		 */
		abstract class share // Shared between {@link advanced_cache} and {@link plugin}.
		{
			/* --------------------------------------------------------------------------------------
			 * Class properties.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Identifies pro version of Quick Cache.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var boolean `TRUE` for Quick Cache Pro.
			 */
			public $is_pro = FALSE;

			/**
			 * Version string in YYMMDD[+build] format.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string Current version of the software.
			 */
			public $version = '140605';

			/**
			 * Text domain for translations; based on `__NAMESPACE__`.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var string Defined by class constructor; for translations.
			 */
			public $text_domain = '';

			/**
			 * An instance-based cache for class members.
			 *
			 * @since 14xxxx Reorganizing class members.
			 *
			 * @var array An instance-based cache for class members.
			 */
			public $cache = array();

			/**
			 * A global static cache for class members.
			 *
			 * @since 14xxxx Reorganizing class members.
			 *
			 * @var array Global static cache for class members.
			 */
			public static $static = array();

			/**
			 * Array of hooks added by plugins.
			 *
			 * @since 140422 First documented version.
			 *
			 * @var array An array of any hooks added by plugins.
			 */
			public $hooks = array();

			/**
			 * Flag indicating the current user login cookie is expired or invalid.
			 *
			 * @since 140429 Improving user cache handlers.
			 *
			 * @var boolean `TRUE` if current user login cookie is expired or invalid.
			 *    See also {@link user_token()} and {@link advanced_cache::maybe_start_ob_when_logged_in_postload()}.
			 */
			public $user_login_cookie_expired_or_invalid = FALSE;

			/* --------------------------------------------------------------------------------------
			 * Cache path class constants.
			 -------------------------------------------------------------------------------------- */

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

			/* --------------------------------------------------------------------------------------
			 * Shared constructor.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Class constructor.
			 *
			 * @since 140422 First documented version.
			 */
			public function __construct()
			{
				if(strpos(__NAMESPACE__, '\\') !== FALSE) // Sanity check.
					throw new \exception('Not a root namespace: `'.__NAMESPACE__.'`.');

				$this->text_domain = str_replace('_', '-', __NAMESPACE__);
			}

			/* --------------------------------------------------------------------------------------
			 * Cache directory/path/URL utilities.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Absolute server path to the cache directory.
			 *
			 * @since 14xxxx Reorganizing class members.
			 *
			 * @param string $rel_path Optional; a relative path within the cache directory.
			 *
			 * @return string Absolute server path to the cache directory.
			 *
			 * @throws \exception If unable to determine the cache directory location.
			 */
			public function cache_dir($rel_path = '')
			{
				if(method_exists($this, 'wp_content_dir_to') && isset($this->cache_sub_dir))
					$cache_dir = $this->wp_content_dir_to($this->cache_sub_dir);

				else if(defined('QUICK_CACHE_DIR') && QUICK_CACHE_DIR)
					$cache_dir = QUICK_CACHE_DIR; // Global constant.

				else throw new \exception(__('Unable to determine cache directory location.', $this->text_domain));

				return $cache_dir.(($rel_path) ? '/'.ltrim((string)$rel_path) : '');
			}

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

				return $cache_path; // Do not filter.
			}

			/* --------------------------------------------------------------------------------------
			 * Token generation utilities.
			 -------------------------------------------------------------------------------------- */

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

				if(isset(static::$static[__FUNCTION__][$dashify]))
					return static::$static[__FUNCTION__][$dashify];

				$host        = strtolower($_SERVER['HTTP_HOST']);
				$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host), '-') : $host;

				return (static::$static[__FUNCTION__][$dashify] = $token_value);
			}

			/**
			 * Produces a token based on the current site's base directory.
			 *
			 * @since 140605 First documented version.
			 *
			 * @param boolean $dashify Optional, defaults to a `FALSE` value.
			 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
			 *
			 * @return string Produces a token based on the current site's base directory;
			 *    (i.e. in the case of a sub-directory multisite network).
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 *
			 * @see plugin::clear_cache()
			 * @see plugin::update_blog_paths()
			 */
			public function host_base_token($dashify = FALSE)
			{
				$dashify = (integer)$dashify;

				if(isset(static::$static[__FUNCTION__][$dashify]))
					return static::$static[__FUNCTION__][$dashify];

				$host_base_token = '/'; // Assume NOT multisite; or running it's own domain.

				if(is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL))
				{ // Multisite w/ sub-directories; need a valid sub-directory token.

					if(defined('PATH_CURRENT_SITE')) $host_base_token = PATH_CURRENT_SITE;
					else if(!empty($GLOBALS['base'])) $host_base_token = $GLOBALS['base'];

					$host_base_token = trim($host_base_token, '\\/'." \t\n\r\0\x0B");
					$host_base_token = (isset($host_base_token[0])) ? '/'.$host_base_token.'/' : '/';
				}
				$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host_base_token), '-') : $host_base_token;

				return (static::$static[__FUNCTION__][$dashify] = $token_value);
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
			 * @see plugin::clear_cache()
			 * @see plugin::update_blog_paths()
			 */
			public function host_dir_token($dashify = FALSE)
			{
				$dashify = (integer)$dashify;

				if(isset(static::$static[__FUNCTION__][$dashify]))
					return static::$static[__FUNCTION__][$dashify];

				$host_dir_token = '/'; // Assume NOT multisite; or on it's own domain.
				if(is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL))
				{ // Multisite w/ sub-directories; need a valid sub-directory token.

					$uri_minus_base = // Supports `/sub-dir/child-blog-sub-dir/` also.
						preg_replace('/^'.preg_quote($this->host_base_token(), '/').'/', '', $_SERVER['REQUEST_URI']);

					list($host_dir_token) = explode('/', trim($uri_minus_base, '/'));
					$host_dir_token = (isset($host_dir_token[0])) ? '/'.$host_dir_token.'/' : '/';

					if($host_dir_token !== '/' // Perhaps NOT the main site?
					   && (!is_file(($cache_dir = $this->cache_dir()).'/qc-blog-paths') // NOT a read/valid blog path?
					       || !in_array($host_dir_token, unserialize(file_get_contents($cache_dir.'/qc-blog-paths')), TRUE))
					) $host_dir_token = '/'; // Main site; e.g. this is NOT a real/valid child blog path.
				}
				$token_value = ($dashify) ? trim(preg_replace('/[^a-z0-9\/]/i', '-', $host_dir_token), '-') : $host_dir_token;

				return (static::$static[__FUNCTION__][$dashify] = $token_value);
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
			 * @see plugin::clear_cache()
			 * @see plugin::update_blog_paths()
			 */
			public function host_base_dir_tokens($dashify = FALSE)
			{
				$dashify = (integer)$dashify;

				if(isset(static::$static[__FUNCTION__][$dashify]))
					return static::$static[__FUNCTION__][$dashify];

				$tokens = preg_replace('/\/{2,}/', '/', $this->host_base_token($dashify).$this->host_dir_token($dashify));

				return (static::$static[__FUNCTION__][$dashify] = $tokens);
			}

			/**
			 * Produces a token based on the current user.
			 *
			 * @since 140422 First documented version.
			 *
			 * @return string Produces a token based on the current user;
			 *    else an empty string if that's not possible to do.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 *
			 * @note This routine may trigger a flag which indicates that the current user was logged-in at some point,
			 *    but now the login cookie can no longer be validated by WordPress; i.e. they are NOT actually logged in any longer.
			 *    See {@link $user_login_cookie_expired_or_invalid}
			 *
			 * @warning Do NOT call upon this method until WordPress reaches it's cache postload phase.
			 */
			public function user_token() // When/if possible.
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				$wp_validate_auth_cookie_possible = $this->function_is_possible('wp_validate_auth_cookie');
				if($wp_validate_auth_cookie_possible && ($user_id = (integer)wp_validate_auth_cookie('', 'logged_in')))
					return (static::$static[__FUNCTION__] = $user_id); // A real user in this case.

				else if(!empty($_COOKIE['comment_author_email_'.COOKIEHASH]) && is_string($_COOKIE['comment_author_email_'.COOKIEHASH]))
					return (static::$static[__FUNCTION__] = md5(strtolower(stripslashes($_COOKIE['comment_author_email_'.COOKIEHASH]))));

				else if(!empty($_COOKIE['wp-postpass_'.COOKIEHASH]) && is_string($_COOKIE['wp-postpass_'.COOKIEHASH]))
					return (static::$static[__FUNCTION__] = md5(stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH])));

				else if(defined('SID') && SID) return (static::$static[__FUNCTION__] = preg_replace('/[^a-z0-9]/i', '', SID));

				if($wp_validate_auth_cookie_possible // We were unable to validate the login cookie?
				   && !empty($_COOKIE['wordpress_logged_in_'.COOKIEHASH]) && is_string($_COOKIE['wordpress_logged_in_'.COOKIEHASH])
				) $this->user_login_cookie_expired_or_invalid = TRUE; // Flag as `TRUE`.

				return (static::$static[__FUNCTION__] = '');
			}

			/* --------------------------------------------------------------------------------------
			 * Conditional utilities.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Is the current request method `POST|PUT|DELETE`?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if a `POST|PUT|DELETE` request.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_post_put_del_request()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(!empty($_SERVER['REQUEST_METHOD']))
					if(in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('POST', 'PUT', 'DELETE'), TRUE))
						return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Does the current request include a query string?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if request includes a query string.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_get_request_w_query()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(!empty($_GET) || isset($_SERVER['QUERY_STRING'][0]))
					if(!(isset($_GET['qcABC']) && count($_GET) === 1)) // Ignore this special case.
						return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Should the current user should be considered a logged-in user?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if current user should be considered a logged-in user.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_like_user_logged_in()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(defined('SID') && SID) // Session ID.
					return (static::$static[__FUNCTION__] = TRUE);

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
					if(preg_match($logged_in_cookies, $_key) && $_value)
						return (static::$static[__FUNCTION__] = TRUE);
				unset($_key, $_value); // Housekeeping.

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Are we in a LOCALHOST environment?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if we are in a LOCALHOST environment.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_localhost()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(defined('LOCALHOST') && LOCALHOST)
					return (static::$static[__FUNCTION__] = TRUE);

				if(!defined('LOCALHOST') && !empty($_SERVER['HTTP_HOST']))
					if(preg_match('/localhost|127\.0\.0\.1/i', $_SERVER['HTTP_HOST']))
						return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Is the current request for the Auto-Cache Engine?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if the current request is for the Auto-Cache Engine.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_auto_cache_engine()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(!empty($_SERVER['HTTP_USER_AGENT']))
					if(stripos($_SERVER['HTTP_USER_AGENT'], __NAMESPACE__) !== FALSE)
						return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Is the current request for a feed?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if the current request is for a feed.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_feed()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(preg_match('/\/feed(?:[\/?]|$)/', $_SERVER['REQUEST_URI']))
					return (static::$static[__FUNCTION__] = TRUE);

				if(isset($_REQUEST['feed'])) // Query var?
					return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Is the current request over SSL?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if the current request is over SSL.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_ssl()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(!empty($_SERVER['SERVER_PORT']))
					if($_SERVER['SERVER_PORT'] === '443')
						return (static::$static[__FUNCTION__] = TRUE);

				if(!empty($_SERVER['HTTPS']))
					if($_SERVER['HTTPS'] === '1' || strcasecmp($_SERVER['HTTPS'], 'on') === 0)
						return (static::$static[__FUNCTION__] = TRUE);

				if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
					if(strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0)
						return (static::$static[__FUNCTION__] = TRUE);

				return (static::$static[__FUNCTION__] = FALSE);
			}

			/**
			 * Is a document/string an HTML/XML doc; or no?
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string $doc Input string/document to check.
			 *
			 * @return boolean `TRUE` if `$doc` is an HTML/XML doc type.
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
			 * @return boolean `TRUE` if the current request has a cacheable content type.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 *
			 * @warning Do NOT call upon this method until the end of a script execution.
			 */
			public function has_a_cacheable_content_type()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				foreach($this->headers_list() as $_header)
					if(stripos($_header, 'Content-Type:') === 0)
						$content_type = $_header; // Last one.
				unset($_header); // Just a little housekeeping.

				if(isset($content_type[0]) && stripos($content_type, 'html') === FALSE && stripos($content_type, 'xml') === FALSE && stripos($content_type, __NAMESPACE__) === FALSE)
					return (static::$static[__FUNCTION__] = FALSE); // Do NOT cache data sent by scripts serving other MIME types.

				return (static::$static[__FUNCTION__] = TRUE); // Assume that it is by default.
			}

			/**
			 * Does the current request have a cacheable HTTP status code?
			 *
			 * @since 140422 First documented version.
			 *
			 * @return boolean `TRUE` if the current request has a cacheable HTTP status code.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 *
			 * @warning Do NOT call upon this method until the end of a script execution.
			 */
			public function has_a_cacheable_status()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				if(($http_status = (string)$this->http_status()) && $http_status[0] !== '2' && $http_status !== '404')
					return (static::$static[__FUNCTION__] = FALSE); // A non-2xx & non-404 status code.

				foreach($this->headers_list() as $_header)
					if(preg_match('/^(?:Retry\-After\:\s+(?P<retry>.+)|Status\:\s+(?P<status>[0-9]+)|HTTP\/[0-9]+\.[0-9]+\s+(?P<http_status>[0-9]+))/i', $_header, $_m))
						if(!empty($_m['retry']) || (!empty($_m['status']) && $_m['status'][0] !== '2' && $_m['status'] !== '404')
						   || (!empty($_m['http_status']) && $_m['http_status'][0] !== '2' && $_m['http_status'] !== '404')
						) return (static::$static[__FUNCTION__] = FALSE); // Not a cacheable status.
				unset($_header); // Just a little housekeeping.

				return (static::$static[__FUNCTION__] = TRUE); // Assume that it is by default.
			}

			/* --------------------------------------------------------------------------------------
			 * Function/extension utilities.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Checks if a PHP extension is loaded up.
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string $extension A PHP extension slug (i.e. extension name).
			 *
			 * @return boolean `TRUE` if the extension is loaded.
			 *
			 * @note The return value of this function is cached to reduce overhead on repeat calls.
			 */
			public function is_extension_loaded($extension)
			{
				if(isset(static::$static[__FUNCTION__][$extension]))
					return static::$static[__FUNCTION__][$extension];
				return (static::$static[__FUNCTION__][$extension] = extension_loaded($extension));
			}

			/**
			 * Is a particular function possible in every way?
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string $function A PHP function (or user function) to check.
			 *
			 * @return string `TRUE` if the function is possible.
			 *
			 * @note This checks (among other things) if the function exists and that it's callable.
			 *    It also checks the currently configured `disable_functions` and `suhosin.executor.func.blacklist`.
			 */
			public function function_is_possible($function)
			{
				if(isset(static::$static[__FUNCTION__][$function]))
					return static::$static[__FUNCTION__][$function];

				if(isset(static::$static[__FUNCTION__]['___disabled_functions']))
					$disabled_functions =& static::$static[__FUNCTION__]['___disabled_functions'];

				else // We need to collect the disabled functions and cache them now.
				{
					static::$static[__FUNCTION__]['___disabled_functions'] = array(); // `$disabled_functions` =& reference.
					$disabled_functions                                    =& static::$static[__FUNCTION__]['___disabled_functions'];

					if(function_exists('ini_get')) // Only if {@link ini_get()} is possible itself.
					{
						if(($disable_functions = trim(ini_get('disable_functions'))))
							$disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($disable_functions), NULL, PREG_SPLIT_NO_EMPTY));

						if(($blacklist_functions = trim(ini_get('suhosin.executor.func.blacklist'))))
							$disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($blacklist_functions), NULL, PREG_SPLIT_NO_EMPTY));
					}
				}
				$possible = TRUE; // Assume it is.. (intialize).

				if(!function_exists($function) || !is_callable($function)
				   || ($disabled_functions && in_array(strtolower($function), $disabled_functions, TRUE))
				) $possible = FALSE; // Not possible.

				return (static::$static[__FUNCTION__][$function] = $possible);
			}

			/* --------------------------------------------------------------------------------------
			 * HTTP protocol/status utility methods.
			 -------------------------------------------------------------------------------------- */

			/**
			 * Current HTTP protocol; i.e. `HTTP/1.0` or `HTTP/1.1`.
			 *
			 * @since 14xxxx Correcting 404 cache response status code.
			 *
			 * @return string Current HTTP protocol; i.e. `HTTP/1.0` or `HTTP/1.1`.
			 */
			public function http_protocol()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				$protocol = !empty($_SERVER['SERVER_PROTOCOL'])
					? strtoupper((string)$_SERVER['SERVER_PROTOCOL']) : 'HTTP/1.0';

				if($protocol !== 'HTTP/1.1' && $protocol !== 'HTTP/1.0')
					$protocol = 'HTTP/1.0'; // Default value.

				return (static::$static[__FUNCTION__] = $protocol);
			}

			/**
			 * An array of all headers sent via PHP; and the current HTTP status header too.
			 *
			 * @since 14xxxx Correcting 404 cache response status code.
			 *
			 * @return array PHP {@link headers_list()} supplemented with
			 *    HTTP status code when possible.
			 *
			 * @warning Do NOT call upon this method until the end of a script execution.
			 */
			public function headers_list()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				$headers_list = headers_list(); // Lacks HTTP status header.

				if(($http_status = (string)$this->http_status()))
					$headers_list[] = $this->http_protocol().' '.$http_status;

				return (static::$static[__FUNCTION__] = $headers_list);
			}

			/**
			 * HTTP status code if at all possible.
			 *
			 * @since 14xxxx Correcting 404 cache response status code.
			 *
			 * @return integer HTTP status code if at all possible; else `0`.
			 *
			 * @warning Do NOT call upon this method until the end of a script execution.
			 */
			public function http_status()
			{
				if(isset(static::$static[__FUNCTION__]))
					return static::$static[__FUNCTION__];

				$http_status               = 0; // Initialize.
				$has_property__is_404      = property_exists($this, 'is_404');
				$has_property__http_status = property_exists($this, 'http_status');

				// Determine current HTTP status code.

				if($has_property__is_404 && $this->{'is_404'})
					$http_status = 404; // WordPress said so.

				else if($this->function_is_possible('http_response_code') && ($http_response_code = (integer)http_response_code()))
					$http_status = $http_response_code; // {@link \http_response_code()} available since PHP v5.4.

				else if($has_property__http_status && (integer)$this->{'http_status'})
					$http_status = (integer)$this->{'http_status'}; // {@link \status_header()} filter.

				// Dynamically update class property flags related to the HTTP status code.

				if($http_status && $has_property__http_status) // Update {@link $http_status}?
					$this->{'http_status'} = $http_status; // Prefer over {@link status_header()}.

				if($http_status === 404 && $has_property__is_404) // Update {@link $is_404}?
					$this->{'is_404'} = TRUE; // Prefer over {@link is_404()}.

				return (static::$static[__FUNCTION__] = $http_status);
			}

			/* --------------------------------------------------------------------------------------
			 * Misc. utility methods.
			 -------------------------------------------------------------------------------------- */

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
			 * Recursive directory iterator based on a regex pattern.
			 *
			 * @since 140422 First documented version.
			 *
			 * @param string $dir An absolute server directory path.
			 * @param string $regex A regex pattern; compares to each full file path.
			 *
			 * @return \RegexIterator Navigable with {@link \foreach()}; where each item
			 *    is a {@link \RecursiveDirectoryIterator}.
			 */
			public function dir_regex_iteration($dir, $regex)
			{
				$dir_iterator      = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
				$iterator_iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);
				$regex_iterator    = new \RegexIterator($iterator_iterator, $regex, \RegexIterator::MATCH, \RegexIterator::USE_KEY);

				return $regex_iterator;
			}

			/* --------------------------------------------------------------------------------------
			 * Hook/filter API for Quick Cache.
			 -------------------------------------------------------------------------------------- */

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
			 *
			 * @param string $hook The name of an action hook.
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

			/* --------------------------------------------------------------------------------------
			 * Misc. long property values.
			 -------------------------------------------------------------------------------------- */

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
		function __($string, $text_domain)
		{
			static $exists; // Static cache.
			if(($exists || function_exists('__')) && ($exists = TRUE))
				return \__($string, $text_domain);

			return $string; // Not possible (yet).
		}
	}
}