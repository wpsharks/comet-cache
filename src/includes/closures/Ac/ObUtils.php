<?php
namespace WebSharks\CometCache;

/*
 * Calculated protocol; one of `http://` or `https://`.
 *
 * @since 150422 Rewrite.
 *
 * @type float One of `http://` or `https://`.
 */
$self->protocol = '';

/*
 * Host token for this request.
 *
 * @since 150821 Improving multisite compat.
 *
 * @type string Host token for this request.
 */
$self->host_token = '';

/*
 * Host base/dir tokens for this request.
 *
 * @since 150821 Improving multisite compat.
 *
 * @type string Host base/dir tokens for this request.
 */
$self->host_base_dir_tokens = '';

/*
 * Calculated version salt; set by site configuration data.
 *
 * @since 150422 Rewrite.
 *
 * @type string|mixed Any scalar value does fine.
 */
$self->version_salt = '';

/*
 * Relative cache path for the current request.
 *
 * @since 150422 Rewrite.
 *
 * @type string Cache path for the current request.
 */
$self->cache_path = '';

/*
 * Absolute cache file path for the current request.
 *
 * @since 150422 Rewrite.
 *
 * @type string Absolute cache file path for the current request.
 */
$self->cache_file = '';

/*
 * Relative 404 cache path for the current request.
 *
 * @since 150422 Rewrite.
 *
 * @type string 404 cache path for the current request.
 */
$self->cache_path_404 = '';

/*
 * Absolute 404 cache file path for the current request.
 *
 * @since 150422 Rewrite.
 *
 * @type string Absolute 404 cache file path for the current request.
 */
$self->cache_file_404 = '';

/*
 * Version salt followed by the current request location.
 *
 * @since 150422 Rewrite.
 *
 * @type string Version salt followed by the current request location.
 */
$self->salt_location = '';

/*
 * Calculated max age; i.e., before expiration.
 *
 * @since 151002 Load average checks in pro version.
 *
 * @type integer Calculated max age; i.e., before expiration.
 */
$self->cache_max_age = 0;

/*
 * Start output buffering (if applicable); or serve a cache file (if possible).
 *
 * @since 150422 Rewrite.
 *
 * @note This is a vital part of Comet Cache. This method serves existing (fresh) cache files.
 *    It is also responsible for beginning the process of collecting the output buffer.
 */
$self->maybeStartOutputBuffering = function () use ($self) {
    if (strcasecmp(PHP_SAPI, 'cli') === 0) {
        return $self->maybeSetDebugInfo(NC_DEBUG_PHP_SAPI_CLI);
    }
    if (empty($_SERVER['HTTP_HOST']) || !$self->hostToken()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_NO_SERVER_HTTP_HOST);
    }
    if (empty($_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_NO_SERVER_REQUEST_URI);
    }
    if (defined('COMET_CACHE_ALLOWED') && !COMET_CACHE_ALLOWED) {
        return $self->maybeSetDebugInfo(NC_DEBUG_COMET_CACHE_ALLOWED_CONSTANT);
    }
    if (isset($_SERVER['COMET_CACHE_ALLOWED']) && !$_SERVER['COMET_CACHE_ALLOWED']) {
        return $self->maybeSetDebugInfo(NC_DEBUG_COMET_CACHE_ALLOWED_SERVER_VAR);
    }
    if (defined('DONOTCACHEPAGE')) {
        return $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
    }
    if (isset($_SERVER['DONOTCACHEPAGE'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
    }
    if (isset($_GET[strtolower(SHORT_NAME).'AC']) && !filter_var($_GET[strtolower(SHORT_NAME).'AC'], FILTER_VALIDATE_BOOLEAN)) {
        return $self->maybeSetDebugInfo(NC_DEBUG_AC_GET_VAR);
    }
    if ($self->isUncacheableRequestMethod()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_UNCACHEABLE_REQUEST);
    }
    if (isset($_SERVER['SERVER_ADDR']) && $self->currentIp() === $_SERVER['SERVER_ADDR']) {
        if ((!IS_PRO || !$self->isAutoCacheEngine()) && !$self->isLocalhost()) {
            return $self->maybeSetDebugInfo(NC_DEBUG_SELF_SERVE_REQUEST);
        }
    }
    if (!COMET_CACHE_FEEDS_ENABLE && $self->isFeed()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_FEED_REQUEST);
    }
    if (preg_match('/\/(?:wp\-[^\/]+|xmlrpc)\.php(?:[?]|$)/i', $_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_WP_SYSTEMATICS);
    }
    if (is_admin() || preg_match('/\/wp-admin(?:[\/?]|$)/i', $_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_WP_ADMIN);
    }
    if (is_multisite() && preg_match('/\/files(?:[\/?]|$)/i', $_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_MS_FILES);
    }
    if ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $self->isLikeUserLoggedIn()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
    }
    if (!COMET_CACHE_GET_REQUESTS && $self->requestContainsUncacheableQueryVars()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_GET_REQUEST_QUERIES);
    }
    if (!empty($_REQUEST['preview'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_PREVIEW);
    }
    if (COMET_CACHE_EXCLUDE_URIS && preg_match(COMET_CACHE_EXCLUDE_URIS, $_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_URIS);
    }
    if (COMET_CACHE_EXCLUDE_AGENTS && !empty($_SERVER['HTTP_USER_AGENT']) && (!IS_PRO || !$self->isAutoCacheEngine())) {
        if (preg_match(COMET_CACHE_EXCLUDE_AGENTS, $_SERVER['HTTP_USER_AGENT'])) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_AGENTS);
        }
    }
    if (COMET_CACHE_EXCLUDE_REFS && !empty($_REQUEST['_wp_http_referer'])) {
        if (preg_match(COMET_CACHE_EXCLUDE_REFS, stripslashes($_REQUEST['_wp_http_referer']))) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_REFS);
        }
    }
    if (COMET_CACHE_EXCLUDE_REFS && !empty($_SERVER['HTTP_REFERER'])) {
        if (preg_match(COMET_CACHE_EXCLUDE_REFS, $_SERVER['HTTP_REFERER'])) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_REFS);
        }
    }
    $self->protocol             = $self->isSsl() ? 'https://' : 'http://';
    $self->host_token           = $self->hostToken();
    $self->host_base_dir_tokens = $self->hostBaseDirTokens();

    $self->version_salt = ''; // Initialize the version salt.
    
    $self->version_salt = $self->applyFilters(get_class($self).'__version_salt', $self->version_salt);
    $self->version_salt = $self->applyFilters(GLOBAL_NS.'_version_salt', $self->version_salt);

    $self->cache_path = $self->buildCachePath($self->protocol.$self->host_token.$_SERVER['REQUEST_URI'], '', $self->version_salt);
    $self->cache_file = COMET_CACHE_DIR.'/'.$self->cache_path; // Not considering a user cache. That's done in the postload phase.

    $self->cache_path_404 = $self->buildCachePath($self->protocol.$self->host_token.rtrim($self->host_base_dir_tokens, '/').'/'.COMET_CACHE_404_CACHE_FILENAME);
    $self->cache_file_404 = COMET_CACHE_DIR.'/'.$self->cache_path_404; // Not considering a user cache at all here--ever.

    $self->salt_location = ltrim($self->version_salt.' '.$self->protocol.$self->host_token.$_SERVER['REQUEST_URI']);

    $self->cache_max_age = strtotime('-'.COMET_CACHE_MAX_AGE);
    
    if (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN === 'postload' && $self->isLikeUserLoggedIn()) {
        $self->postload['when_logged_in'] = true; // Enable postload check.
    } elseif (is_file($self->cache_file) && (!$self->cache_max_age || filemtime($self->cache_file) >= $self->cache_max_age)) {
        list($headers, $cache) = explode('<!--headers-->', file_get_contents($self->cache_file), 2);

        $headers_list = $self->headersList();
        foreach (unserialize($headers) as $_header) {
            if (!in_array($_header, $headers_list, true) && stripos($_header, 'Last-Modified:') !== 0) {
                header($_header); // Only cacheable/safe headers are stored in the cache.
            }
        }
        unset($_header); // Just a little housekeeping.

        if (COMET_CACHE_DEBUGGING_ENABLE && $self->isHtmlXmlDoc($cache)) {
            $total_time = number_format(microtime(true) - $self->timer, 5, '.', '');
            $cache .= "\n".'<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->';
            // translators: This string is actually NOT translatable because the `__()` function is not available at this point in the processing.
            $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s fully functional :-) Cache file served for (%2$s) in %3$s seconds, on: %4$s.', 'comet-cache'), NAME, $self->salt_location, $total_time, date('M jS, Y @ g:i a T'))).' -->';
        }
        exit($cache); // Exit with cache contents.
    } else {
        ob_start(array($self, 'outputBufferCallbackHandler'));
    }
    return; // Return value not applicable.
};

/*
 * Output buffer handler; i.e. the cache file generator.
 *
 * @note We CANNOT depend on any WP functionality here; it will cause problems.
 *    Anything we need from WP should be saved in the postload phase as a scalar value.
 *
 * @since 150422 Rewrite.
 *
 * @param string $buffer The buffer from {@link \ob_start()}.
 * @param int    $phase  A set of bitmask flags.
 *
 * @throws \Exception If unable to handle output buffering for any reason.
 *
 * @return string|bool The output buffer, or `FALSE` to indicate no change.
 *
 * @attaches-to {@link \ob_start()}
 */
$self->outputBufferCallbackHandler = function ($buffer, $phase) use ($self) {
    if (!($phase & PHP_OUTPUT_HANDLER_END)) {
        throw new \Exception(sprintf(__('Unexpected OB phase: `%1$s`.', 'comet-cache'), $phase));
    }
    AdvCacheBackCompat::zenCacheConstants();

    $cache = trim((string) $buffer);

    if (!isset($cache[0])) {
        return false; // Don't cache an empty buffer.
    }
    if (!isset($GLOBALS[GLOBAL_NS.'_shutdown_flag'])) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_EARLY_BUFFER_TERMINATION);
    }
    if (defined('COMET_CACHE_ALLOWED') && !COMET_CACHE_ALLOWED) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_COMET_CACHE_ALLOWED_CONSTANT);
    }
    if (isset($_SERVER['COMET_CACHE_ALLOWED']) && !$_SERVER['COMET_CACHE_ALLOWED']) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_COMET_CACHE_ALLOWED_SERVER_VAR);
    }
    if (defined('DONOTCACHEPAGE')) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
    }
    if (isset($_SERVER['DONOTCACHEPAGE'])) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
    }
    if ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $self->is_user_logged_in) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_IS_LOGGED_IN_USER);
    }
    if ((!IS_PRO || !COMET_CACHE_WHEN_LOGGED_IN) && $self->isLikeUserLoggedIn()) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
    }
    if (!COMET_CACHE_CACHE_NONCE_VALUES && preg_match('/\b(?:_wpnonce|akismet_comment_nonce)\b/', $cache)) {
        if (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN && $self->isLikeUserLoggedIn()) {
            if (!COMET_CACHE_CACHE_NONCE_VALUES_WHEN_LOGGED_IN) {
                return (boolean)$self->maybeSetDebugInfo(NC_DEBUG_IS_LOGGED_IN_USER_NONCE);
            }
        } else { // Use the default debug notice for nonce conflicts.
            return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_PAGE_CONTAINS_NONCE);
        } // An nonce makes the page dynamic; i.e., NOT cache compatible.
    }
    if ($self->is_404 && !COMET_CACHE_CACHE_404_REQUESTS) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_404_REQUEST);
    }
    if (stripos($cache, '<body id="error-page">') !== false) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_WP_ERROR_PAGE);
    }
    if (!$self->functionIsPossible('http_response_code')) {
        if (stripos($cache, '<title>database error</title>') !== false) {
            return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_WP_ERROR_PAGE);
        }
    }
    if (!$self->hasACacheableContentType()) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_UNCACHEABLE_CONTENT_TYPE);
    }
    if (!$self->hasACacheableStatus()) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_UNCACHEABLE_STATUS);
    }
    if ($self->is_maintenance) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_MAINTENANCE_PLUGIN);
    }
    if ($self->functionIsPossible('zlib_get_coding_type') && zlib_get_coding_type()
        && (!($zlib_oc = ini_get('zlib.output_compression')) || !filter_var($zlib_oc, FILTER_VALIDATE_BOOLEAN))) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_OB_ZLIB_CODING_TYPE);
    }
    # Lock the cache directory while writes take place here.

    $cache_lock = $self->cacheLock(); // Lock cache directory.

    # Construct a temp file for atomic cache writes.

    $cache_file_tmp = $self->addTmpSuffix($self->cache_file);

    # Cache directory checks. The cache file directory is created here if necessary.

    if (!is_dir(COMET_CACHE_DIR) && mkdir(COMET_CACHE_DIR, 0775, true) && !is_file(COMET_CACHE_DIR.'/.htaccess')) {
        file_put_contents(COMET_CACHE_DIR.'/.htaccess', $self->htaccess_deny);
    }
    if (!is_dir($cache_file_dir = dirname($self->cache_file))) {
        $cache_file_dir_writable = mkdir($cache_file_dir, 0775, true);
    }
    if (empty($cache_file_dir_writable) && !is_writable($cache_file_dir)) {
        throw new \Exception(sprintf(__('Cache directory not writable. %1$s needs this directory please: `%2$s`. Set permissions to `755` or higher; `777` might be needed in some cases.', 'comet-cache'), NAME, $cache_file_dir));
    }
    # This is where a new 404 request might be detected for the first time.

    if ($self->is_404 && is_file($self->cache_file_404)) {
        if (!(symlink($self->cache_file_404, $cache_file_tmp) && rename($cache_file_tmp, $self->cache_file))) {
            throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), $self->cache_file, $self->cache_file_404, COMET_CACHE_DIR));
        }
        $self->cacheUnlock($cache_lock); // Release.
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_1ST_TIME_404_SYMLINK);
    }
    /* ------- Otherwise, we need to construct & store a new cache file. ----------------------------------------------- */

    

    if (COMET_CACHE_DEBUGGING_ENABLE && $self->isHtmlXmlDoc($cache)) {
        $total_time = number_format(microtime(true) - $self->timer, 5, '.', ''); // Based on the original timer.
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s file path: %2$s', 'comet-cache'), NAME, str_replace(WP_CONTENT_DIR, '', $self->is_404 ? $self->cache_file_404 : $self->cache_file))).' -->';
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s file built for (%2$s%3$s) in %4$s seconds, on: %5$s.', 'comet-cache'), NAME, $self->is_404 ? '404 [error document]' : $self->salt_location, (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN && $self->user_token ? '; '.sprintf(__('user token: %1$s', 'comet-cache'), $self->user_token) : ''), $total_time, date('M jS, Y @ g:i a T'))).' -->';
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('This %1$s file will auto-expire (and be rebuilt) on: %2$s (based on your configured expiration time).', 'comet-cache'), NAME, date('M jS, Y @ g:i a T', strtotime('+'.COMET_CACHE_MAX_AGE)))).' -->';
    }
    if ($self->is_404) {
        if (file_put_contents($cache_file_tmp, serialize($self->cacheableHeadersList()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $self->cache_file_404)) {
            if (!(symlink($self->cache_file_404, $cache_file_tmp) && rename($cache_file_tmp, $self->cache_file))) {
                throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), $self->cache_file, $self->cache_file_404, COMET_CACHE_DIR));
            }
            $self->cacheUnlock($cache_lock); // Release.
            return $cache; // Return the newly built cache; with possible debug information also.
        }
    } elseif (file_put_contents($cache_file_tmp, serialize($self->cacheableHeadersList()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $self->cache_file)) {
        $self->cacheUnlock($cache_lock); // Release.
        return $cache; // Return the newly built cache; with possible debug information also.
    }
    @unlink($cache_file_tmp); // Clean this up (if it exists); and throw an exception with information for the site owner.
    throw new \Exception(sprintf(__('%1$s: failed to write cache file for: `%2$s`; possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), NAME, $_SERVER['REQUEST_URI'], COMET_CACHE_DIR));
};
