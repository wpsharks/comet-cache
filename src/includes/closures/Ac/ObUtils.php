<?php
namespace WebSharks\ZenCache;

/*
 * Calculated protocol; one of `http://` or `https://`.
 *
 * @since 150422 Rewrite.
 *
 * @type float One of `http://` or `https://`.
 */
$self->protocol = '';

/*
 * Calculated version salt; set by site configuration data.
 *
 * @since 150422 Rewrite.
 *
 * @type string|mixed Any scalar value does fine.
 */
$self->version_salt = '';

/*
 * Calculated cache path for the current request;
 *    absolute relative (no leading/trailing slashes).
 *
 * @since 150422 Rewrite.
 *
 * @type string Absolute relative (no leading/trailing slashes).
 *             Defined by {@link maybeStartOutputBuffering()}.
 */
$self->cache_path = '';

/*
 * Calculated cache file location for the current request; absolute path.
 *
 * @since 150422 Rewrite.
 *
 * @type string Cache file location for the current request; absolute path.
 *             Defined by {@link maybeStartOutputBuffering()}.
 */
$self->cache_file = '';

/*
 * Centralized 404 cache file location; absolute path.
 *
 * @since 150422 Rewrite.
 *
 * @type string Centralized 404 cache file location; absolute path.
 *             Defined by {@link maybeStartOutputBuffering()}.
 */
$self->cache_file_404 = '';

/*
 * A possible version salt (string value); followed by the current request location.
 *
 * @since 150422 Rewrite.
 *
 * @type string Version salt (string value); followed by the current request location.
 *             Defined by {@link maybeStartOutputBuffering()}.
 */
$self->salt_location = '';

/*
 * Start output buffering (if applicable); or serve a cache file (if possible).
 *
 * @since 150422 Rewrite.
 *
 * @note This is a vital part of ZenCache. This method serves existing (fresh) cache files.
 *    It is also responsible for beginning the process of collecting the output buffer.
 */
$self->maybeStartOutputBuffering = function () use ($self) {
    if (strcasecmp(PHP_SAPI, 'cli') === 0) {
        return $self->maybeSetDebugInfo(NC_DEBUG_PHP_SAPI_CLI);
    }
    if (empty($_SERVER['HTTP_HOST'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_NO_SERVER_HTTP_HOST);
    }
    if (empty($_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_NO_SERVER_REQUEST_URI);
    }
    if (isset($_GET['zcAC']) && !filter_var($_GET['zcAC'], FILTER_VALIDATE_BOOLEAN)) {
        return $self->maybeSetDebugInfo(NC_DEBUG_QCAC_GET_VAR);
    }
    if (defined('ZENCACHE_ALLOWED') && !ZENCACHE_ALLOWED) {
        return $self->maybeSetDebugInfo(NC_DEBUG_ZENCACHE_ALLOWED_CONSTANT);
    }
    if (isset($_SERVER['ZENCACHE_ALLOWED']) && !$_SERVER['ZENCACHE_ALLOWED']) {
        return $self->maybeSetDebugInfo(NC_DEBUG_ZENCACHE_ALLOWED_SERVER_VAR);
    }
    if (defined('DONOTCACHEPAGE')) {
        return $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
    }
    if (isset($_SERVER['DONOTCACHEPAGE'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
    }
    if ($self->isUncacheableRequestMethod()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_UNCACHEABLE_REQUEST);
    }
    if (isset($_SERVER['SERVER_ADDR']) && $self->currentIp() === $_SERVER['SERVER_ADDR']) {
        if ((!IS_PRO || !$self->isAutoCacheEngine()) && !$self->isLocalhost()) {
            return $self->maybeSetDebugInfo(NC_DEBUG_SELF_SERVE_REQUEST);
        }
    }
    if (!ZENCACHE_FEEDS_ENABLE && $self->isFeed()) {
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
    if ((!IS_PRO || !ZENCACHE_WHEN_LOGGED_IN) && $self->isLikeUserLoggedIn()) {
        return $self->maybeSetDebugInfo(NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
    }
    if (!ZENCACHE_GET_REQUESTS && $self->isGetRequestWQuery() && (!isset($_GET['zcAC']) || !filter_var($_GET['zcAC'], FILTER_VALIDATE_BOOLEAN))) {
        return $self->maybeSetDebugInfo(NC_DEBUG_GET_REQUEST_QUERIES);
    }
    if (ZENCACHE_EXCLUDE_URIS && preg_match(ZENCACHE_EXCLUDE_URIS, $_SERVER['REQUEST_URI'])) {
        return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_URIS);
    }
    if (ZENCACHE_EXCLUDE_AGENTS && !empty($_SERVER['HTTP_USER_AGENT']) && (!IS_PRO || !$self->isAutoCacheEngine())) {
        if (preg_match(ZENCACHE_EXCLUDE_AGENTS, $_SERVER['HTTP_USER_AGENT'])) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_AGENTS);
        }
    }
    if (ZENCACHE_EXCLUDE_REFS && !empty($_REQUEST['_wp_http_referer'])) {
        if (preg_match(ZENCACHE_EXCLUDE_REFS, stripslashes($_REQUEST['_wp_http_referer']))) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_REFS);
        }
    }
    if (ZENCACHE_EXCLUDE_REFS && !empty($_SERVER['HTTP_REFERER'])) {
        if (preg_match(ZENCACHE_EXCLUDE_REFS, $_SERVER['HTTP_REFERER'])) {
            return $self->maybeSetDebugInfo(NC_DEBUG_EXCLUDED_REFS);
        }
    }
    $self->protocol = $self->isSsl() ? 'https://' : 'http://';

    $self->version_salt = ''; // Initialize the version salt.
    
    $self->version_salt = $self->applyFilters(GLOBAL_NS.'\\advanced_cache__version_salt', $self->version_salt); // Back compat.
    $self->version_salt = $self->applyFilters(GLOBAL_NS.'_version_salt', $self->version_salt);

    $self->cache_path = $self->buildCachePath($self->protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '', $self->version_salt);

    $self->cache_file     = ZENCACHE_DIR.'/'.$self->cache_path; // NOT considering a user cache; not yet.
    $self->cache_file_404 = ZENCACHE_DIR.'/'.$self->buildCachePath($self->protocol.$_SERVER['HTTP_HOST'].'/'.ZENCACHE_404_CACHE_FILENAME);

    $self->salt_location = ltrim($self->version_salt.' '.$self->protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

    if (IS_PRO && ZENCACHE_WHEN_LOGGED_IN === 'postload' && $self->isLikeUserLoggedIn()) {
        $self->postload['when_logged_in'] = true; // Enable postload check.
    } elseif (is_file($self->cache_file) && filemtime($self->cache_file) >= strtotime('-'.ZENCACHE_MAX_AGE)) {
        list($headers, $cache) = explode('<!--headers-->', file_get_contents($self->cache_file), 2);

        $headers_list = $self->headersList();
        foreach (unserialize($headers) as $_header) {
            if (!in_array($_header, $headers_list, true) && stripos($_header, 'Last-Modified:') !== 0) {
                header($_header); // Only cacheable/safe headers are stored in the cache.
            }
        }
        unset($_header); // Just a little housekeeping.

        if (ZENCACHE_DEBUGGING_ENABLE && $self->isHtmlXmlDoc($cache)) {
            $total_time = number_format(microtime(true) - $self->timer, 5, '.', '');
            $cache .= "\n".'<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->';
            // translators: This string is actually NOT translatable because the `__()` function is not available at this point in the processing.
            $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s fully functional :-) Cache file served for (%2$s) in %3$s seconds, on: %4$s.', SLUG_TD), NAME, $self->salt_location, $total_time, date('M jS, Y @ g:i a T'))).' -->';
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
 *
 * @attaches-to {@link \ob_start()}
 */
$self->outputBufferCallbackHandler = function ($buffer, $phase) use ($self) {
    if (!($phase & PHP_OUTPUT_HANDLER_END)) {
        throw new \Exception(sprintf(__('Unexpected OB phase: `%1$s`.', SLUG_TD), $phase));
    }
    AdvCacheBackCompat::quickCacheConstants();

    $cache = trim((string) $buffer);

    if (!isset($cache[0])) {
        return false; // Don't cache an empty buffer.
    }
    if (!isset($GLOBALS[GLOBAL_NS.'_shutdown_flag'])) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_EARLY_BUFFER_TERMINATION);
    }
    if (isset($_GET['zcAC']) && !filter_var($_GET['zcAC'], FILTER_VALIDATE_BOOLEAN)) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_QCAC_GET_VAR);
    }
    if (defined('ZENCACHE_ALLOWED') && !ZENCACHE_ALLOWED) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_ZENCACHE_ALLOWED_CONSTANT);
    }
    if (isset($_SERVER['ZENCACHE_ALLOWED']) && !$_SERVER['ZENCACHE_ALLOWED']) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_ZENCACHE_ALLOWED_SERVER_VAR);
    }
    if (defined('DONOTCACHEPAGE')) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_CONSTANT);
    }
    if (isset($_SERVER['DONOTCACHEPAGE'])) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR);
    }
    if ((!IS_PRO || !ZENCACHE_WHEN_LOGGED_IN) && $self->is_user_logged_in) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_IS_LOGGED_IN_USER);
    }
    if ((!IS_PRO || !ZENCACHE_WHEN_LOGGED_IN) && $self->isLikeUserLoggedIn()) {
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_IS_LIKE_LOGGED_IN_USER);
    }
    if ($self->is_404 && !ZENCACHE_CACHE_404_REQUESTS) {
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

    if (!is_dir(ZENCACHE_DIR) && mkdir(ZENCACHE_DIR, 0775, true) && !is_file(ZENCACHE_DIR.'/.htaccess')) {
        file_put_contents(ZENCACHE_DIR.'/.htaccess', $self->htaccess_deny);
    }
    if (!is_dir($cache_file_dir = dirname($self->cache_file))) {
        $cache_file_dir_writable = mkdir($cache_file_dir, 0775, true);
    }
    if (empty($cache_file_dir_writable) && !is_writable($cache_file_dir)) {
        throw new \Exception(sprintf(__('Cache directory not writable. %1$s needs this directory please: `%2$s`. Set permissions to `755` or higher; `777` might be needed in some cases.', SLUG_TD), NAME, $cache_file_dir));
    }
    # This is where a new 404 request might be detected for the first time.

    if ($self->is_404 && is_file($self->cache_file_404)) {
        if (!(symlink($self->cache_file_404, $cache_file_tmp) && rename($cache_file_tmp, $self->cache_file))) {
            throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', SLUG_TD), $self->cache_file, $self->cache_file_404, ZENCACHE_DIR));
        }
        $self->cacheUnlock($cache_lock); // Unlock cache directory.
        return (boolean) $self->maybeSetDebugInfo(NC_DEBUG_1ST_TIME_404_SYMLINK);
    }
    /* ------- Otherwise, we need to construct & store a new cache file. ----------------------------------------------- */

    

    if (ZENCACHE_DEBUGGING_ENABLE && $self->isHtmlXmlDoc($cache)) {
        $total_time = number_format(microtime(true) - $self->timer, 5, '.', ''); // Based on the original timer.
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s file path: %2$s', SLUG_TD), NAME, str_replace(WP_CONTENT_DIR, '', $self->is_404 ? $self->cache_file_404 : $self->cache_file))).' -->';
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('%1$s file built for (%2$s%3$s) in %4$s seconds, on: %5$s.', SLUG_TD), NAME, $self->is_404 ? '404 [error document]' : $self->salt_location, (IS_PRO && ZENCACHE_WHEN_LOGGED_IN && $self->user_token ? '; '.sprintf(__('user token: %1$s', SLUG_TD), $self->user_token) : ''), $total_time, date('M jS, Y @ g:i a T'))).' -->';
        $cache .= "\n".'<!-- '.htmlspecialchars(sprintf(__('This %1$s file will auto-expire (and be rebuilt) on: %2$s (based on your configured expiration time).', SLUG_TD), NAME, date('M jS, Y @ g:i a T', strtotime('+'.ZENCACHE_MAX_AGE)))).' -->';
    }
    if ($self->is_404) {
        if (file_put_contents($cache_file_tmp, serialize($self->cacheableHeadersList()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $self->cache_file_404)) {
            if (!(symlink($self->cache_file_404, $cache_file_tmp) && rename($cache_file_tmp, $self->cache_file))) {
                throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', SLUG_TD), $self->cache_file, $self->cache_file_404, ZENCACHE_DIR));
            }
            $self->cacheUnlock($cache_lock); // Unlock cache directory.
            return $cache; // Return the newly built cache; with possible debug information also.
        }
    } elseif (file_put_contents($cache_file_tmp, serialize($self->cacheableHeadersList()).'<!--headers-->'.$cache) && rename($cache_file_tmp, $self->cache_file)) {
        $self->cacheUnlock($cache_lock); // Unlock cache directory.
        return $cache; // Return the newly built cache; with possible debug information also.
    }
    @unlink($cache_file_tmp); // Clean this up (if it exists); and throw an exception with information for the site owner.

    throw new \Exception(sprintf(__('%1$s: failed to write cache file for: `%2$s`; possible permissions issue (or race condition), please check your cache directory: `%3$s`.', SLUG_TD), NAME, $_SERVER['REQUEST_URI'], ZENCACHE_DIR));
};
