<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait ObUtils
{
    /**
     * Protocol.
     *
     * @since 150422 Rewrite.
     *
     * @type string Protocol
     */
    public $protocol = '';

    /**
     * Host token.
     *
     * @since 150821 Improving multisite compat.
     *
     * @type string Host token.
     */
    public $host_token = '';

    /**
     * Host base/dir tokens.
     *
     * @since 150821 Improving multisite compat.
     *
     * @type string Host base/dir tokens.
     */
    public $host_base_dir_tokens = '';

    

    /**
     * Version salt.
     *
     * @since 150422 Rewrite.
     *
     * @type string Forced to a string.
     */
    public $version_salt = '';

    /**
     * Relative cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type string Cache path.
     */
    public $cache_path = '';

    /**
     * Absolute cache file path.
     *
     * @since 150422 Rewrite.
     *
     * @type string Absolute cache file path.
     */
    public $cache_file = '';

    /**
     * Relative 404 cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type string 404 cache path.
     */
    public $cache_path_404 = '';

    /**
     * Absolute 404 cache file path.
     *
     * @since 150422 Rewrite.
     *
     * @type string Absolute 404 cache file path.
     */
    public $cache_file_404 = '';

    /**
     * Version salt + location.
     *
     * @since 150422 Rewrite.
     *
     * @type string Version salt + location.
     */
    public $salt_location = '';

    /**
     * Calculated max age.
     *
     * @since 151002 Load average checks.
     *
     * @type int Calculated max age.
     */
    public $cache_max_age = 0;

    /**
     * Max age has been disabled?
     *
     * @since 161226 Load average checks.
     *
     * @type bool Max age disabled?
     */
    public $cache_max_age_disabled = false;

    /**
     * Calculated 12 hour expiration time.
     *
     * @since 161119 Calculated 12 hour expiration time.
     *
     * @type int Calculated 12 hour expiration time.
     */
    public $nonce_cache_max_age = 0;

    /**
     * Start output buffering or serve cache.
     *
     * @since 150422 Rewrite.
     * @since 170220 Adding API request constants.
     * @since 17xxxx Condense using `elseif` chains.
     * @since 17xxxx Polishing a little.
     */
    public function maybeStartOutputBuffering()
    {
        $lc_short_name = mb_strtolower(SHORT_NAME);

        if (strcasecmp(PHP_SAPI, 'cli') === 0) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_PHP_SAPI_CLI);
            //
        } elseif (empty($_SERVER['HTTP_HOST']) || !$this->hostToken()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_NO_SERVER_HTTP_HOST);
            //
        } elseif (empty($_SERVER['REQUEST_URI'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_NO_SERVER_REQUEST_URI);
            //
        } elseif (defined('COMET_CACHE_ALLOWED') && !COMET_CACHE_ALLOWED) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_COMET_CACHE_ALLOWED_CONSTANT);
            //
        } elseif (isset($_SERVER['COMET_CACHE_ALLOWED']) && !$_SERVER['COMET_CACHE_ALLOWED']) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_COMET_CACHE_ALLOWED_SERVER_VAR);
            //
        } elseif (defined('DONOTCACHEPAGE')) { // Common to most WP cache plugins.
            return $this->maybeSetDebugInfo($this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
            //
        } elseif (isset($_SERVER['DONOTCACHEPAGE'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
            //
        } elseif (isset($_GET[$lc_short_name.'AC']) && !filter_var($_GET[$lc_short_name.'AC'], FILTER_VALIDATE_BOOLEAN)) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_AC_GET_VAR);
            //
        } elseif (!empty($_REQUEST['preview'])) { // Don't cache previews.
            return $this->maybeSetDebugInfo($this::NC_DEBUG_PREVIEW);
            //
        } elseif (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_XMLRPC_REQUEST_CONSTANT);
            //
        } elseif (defined('REST_REQUEST') && REST_REQUEST) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_REST_REQUEST_CONSTANT);
            //
        } elseif ($this->isUncacheableRequestMethod()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_UNCACHEABLE_REQUEST);
            //
        } elseif (!COMET_CACHE_FEEDS_ENABLE && $this->isFeed()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_FEED_REQUEST);
            //
        } elseif (preg_match('/\/(?:wp\-[^\/]+|xmlrpc)\.php(?:[?]|$)/ui', $_SERVER['REQUEST_URI'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_WP_SYSTEMATICS);
            //
        } elseif (is_admin() || preg_match('/\/wp-admin(?:[\/?]|$)/ui', $_SERVER['REQUEST_URI'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_WP_ADMIN);
            //
        } elseif (is_multisite() && preg_match('/\/files(?:[\/?]|$)/ui', $_SERVER['REQUEST_URI'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_MS_FILES);
            //
        } elseif ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $this->isLikeUserLoggedIn()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
            //
        } elseif (!COMET_CACHE_GET_REQUESTS && $this->requestContainsUncacheableQueryVars()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_GET_REQUEST_QUERIES);
            //
        } elseif (COMET_CACHE_EXCLUDE_HOSTS && preg_match(COMET_CACHE_EXCLUDE_HOSTS, $_SERVER['HTTP_HOST'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_EXCLUDED_HOSTS);
            //
        } elseif (COMET_CACHE_EXCLUDE_URIS && preg_match(COMET_CACHE_EXCLUDE_URIS, $_SERVER['REQUEST_URI'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_EXCLUDED_URIS);
            //
        } elseif (isset($_SERVER['SERVER_ADDR']) && $this->currentIp() === $_SERVER['SERVER_ADDR'] && (!IS_PRO || !$this->isAutoCacheEngine()) && !$this->isLocalhost()) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_SELF_SERVE_REQUEST);
            //
        } elseif (COMET_CACHE_EXCLUDE_AGENTS && !empty($_SERVER['HTTP_USER_AGENT']) && (!IS_PRO || !$this->isAutoCacheEngine()) && preg_match(COMET_CACHE_EXCLUDE_AGENTS, $_SERVER['HTTP_USER_AGENT'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_EXCLUDED_AGENTS);
            //
        } elseif (COMET_CACHE_EXCLUDE_REFS && !empty($_REQUEST['_wp_http_referer']) && preg_match(COMET_CACHE_EXCLUDE_REFS, stripslashes($_REQUEST['_wp_http_referer']))) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_EXCLUDED_REFS);
            //
        } elseif (COMET_CACHE_EXCLUDE_REFS && !empty($_SERVER['HTTP_REFERER']) && preg_match(COMET_CACHE_EXCLUDE_REFS, $_SERVER['HTTP_REFERER'])) {
            return $this->maybeSetDebugInfo($this::NC_DEBUG_EXCLUDED_REFS);
        }
        $this->host_token           = $this->hostToken();
        $this->host_base_dir_tokens = $this->hostBaseDirTokens();
        $this->protocol             = $this->isSsl() ? 'https://' : 'http://';

        $this->version_salt = ''; // Initialize the version salt.

        

        $this->version_salt = $this->applyFilters(get_class($this).'__version_salt', $this->version_salt);
        $this->version_salt = $this->applyFilters(GLOBAL_NS.'_version_salt', $this->version_salt);

        $this->cache_path = $this->buildCachePath($this->protocol.$this->host_token.$_SERVER['REQUEST_URI'], '', $this->version_salt);
        $this->cache_file = COMET_CACHE_DIR.'/'.$this->cache_path; // Not considering a user cache. That's done in the postload phase.

        $this->cache_path_404 = $this->buildCachePath($this->protocol.$this->host_token.rtrim($this->host_base_dir_tokens, '/').'/'.COMET_CACHE_404_CACHE_FILENAME);
        $this->cache_file_404 = COMET_CACHE_DIR.'/'.$this->cache_path_404; // Not considering a user cache at all here--ever.

        $this->salt_location = ltrim($this->version_salt.' '.$this->protocol.$this->host_token.$_SERVER['REQUEST_URI']);

        $this->cache_max_age       = strtotime('-'.COMET_CACHE_MAX_AGE); // Initialize; global config.
        $this->nonce_cache_max_age = strtotime('-12 hours'); // Initialize; based on a fixed expiration time.

        

        if (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN === 'postload' && $this->isLikeUserLoggedIn()) {
            $this->postload['when_logged_in'] = true; // Enable postload check.
            //
        } elseif (extract($this->cacheRead())) { // `['headers' => [], 'output' => '', 'via' => '']`
            $headers_list = $this->headersList(); // Headers enqueued already.

            foreach ($headers as $_header) { // Only send nonexistent headers.
                if (!in_array($_header, $headers_list, true) && mb_stripos($_header, 'last-modified:') !== 0) {
                    header($_header); // Only cacheable/safe headers are stored in the cache.
                }
            } // unset($_header); // Just a little housekeeping.

            if (COMET_CACHE_DEBUGGING_ENABLE && $this->isHtmlXmlDoc($output)) {
                $total_time = number_format(microtime(true) - $this->timer, 5, '.', '');

                $DebugNotes = new Classes\Notes();

                $DebugNotes->add(__('Loaded From Cache', 'comet-cache'), 'via '.$via);
                $DebugNotes->add(__('Loaded From Cache On', 'comet-cache'), date('M jS, Y @ g:i a T'));
                $DebugNotes->add(__('Loaded From Cache In', 'comet-cache'), sprintf(__('%1$s seconds', 'comet-cache'), $total_time));

                $output .= "\n\n".$DebugNotes->asHtmlComments();
            }
            exit($output); // Exit with output contents.
        } else {
            ob_start([$this, 'outputBufferCallbackHandler']);
        }
        return; // Return value not applicable.
    }

    /**
     * Output buffer handler; i.e. the cache file generator.
     *
     * @since 150422 Rewrite.
     * @since 170220 Adding API request constants.
     * @since 17xxxx Implementing `cacheWrite()` utility.
     * @since 17xxxx Condense using `elseif` chains.
     * @since 17xxxx Polishing a little.
     *
     * @attaches-to {@link \ob_start()}
     *
     * @param string $buffer The buffer from {@link \ob_start()}.
     * @param int    $phase  A set of bitmask flags.
     *
     * @throws \Exception If unable to handle output buffering.
     *
     * @return string|bool The output buffer, or `false` to indicate no change.
     *
     * @note Cannot depend on WP functionality here; it will cause problems.
     *    Anything we need from WP should be saved in the postload phase as a scalar value.
     */
    public function outputBufferCallbackHandler($buffer, $phase)
    {
        Classes\AdvCacheBackCompat::zenCacheConstants();

        if (!($phase & PHP_OUTPUT_HANDLER_END)) {
            throw new \Exception('Unexpected OB phase.');
        }
        if (!($output = trim((string) $buffer))) {
            return false; // Empty buffer.
            //
        } elseif (!isset($GLOBALS[GLOBAL_NS.'_shutdown_flag'])) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_EARLY_BUFFER_TERMINATION);
            //
        } elseif (defined('COMET_CACHE_ALLOWED') && !COMET_CACHE_ALLOWED) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_COMET_CACHE_ALLOWED_CONSTANT);
            //
        } elseif (isset($_SERVER['COMET_CACHE_ALLOWED']) && !$_SERVER['COMET_CACHE_ALLOWED']) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_COMET_CACHE_ALLOWED_SERVER_VAR);
            //
        } elseif (defined('DONOTCACHEPAGE')) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
            //
        } elseif (isset($_SERVER['DONOTCACHEPAGE'])) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
            //
        } elseif ($this->is_maintenance) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_MAINTENANCE_PLUGIN);
            //
        } elseif (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_XMLRPC_REQUEST_CONSTANT);
            //
        } elseif (defined('REST_REQUEST') && REST_REQUEST) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_REST_REQUEST_CONSTANT);
            //
        } elseif ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $this->is_user_logged_in) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_IS_LOGGED_IN_USER);
            //
        } elseif ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $this->isLikeUserLoggedIn()) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
            //
        } elseif ($this->is_404 && !COMET_CACHE_CACHE_404_REQUESTS) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_404_REQUEST);
            //
        } elseif (mb_stripos($output, '<body id="error-page">') !== false) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_WP_ERROR_PAGE);
            //
        } elseif (!$this->hasACacheableContentType()) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_UNCACHEABLE_CONTENT_TYPE);
            //
        } elseif (!$this->hasACacheableStatus()) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_UNCACHEABLE_STATUS);
            //
        } elseif ($this->functionIsPossible('zlib_get_coding_type') && zlib_get_coding_type() && (!($zlib_oc = ini_get('zlib.output_compression')) || !filter_var($zlib_oc, FILTER_VALIDATE_BOOLEAN))) {
            return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_OB_ZLIB_CODING_TYPE);
        }
        if (!COMET_CACHE_CACHE_NONCE_VALUES && preg_match('/\b(?:_wpnonce|akismet_comment_nonce)\b/u', $output)) {
            if (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN && $this->isLikeUserLoggedIn()) {
                if (!COMET_CACHE_CACHE_NONCE_VALUES_WHEN_LOGGED_IN) {
                    return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_IS_LOGGED_IN_USER_NONCE);
                }
            } else { // Use the default debug notice for nonce conflicts.
                return (bool) $this->maybeSetDebugInfo($this::NC_DEBUG_PAGE_CONTAINS_NONCE);
            } // An nonce makes the page dynamic; i.e., NOT cache compatible.
        }
        return $this->cacheWrite($output);
    }
}
