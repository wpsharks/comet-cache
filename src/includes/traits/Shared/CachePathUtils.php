<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait CachePathUtils
{
    /**
     * Filter query vars; e.g., remove those we ignore.
     *
     * @since 161119 Adding support for ignored GET vars.
     * @since 161119 Adding support for sorted query vars.
     *
     * @param array $_vars Query vars to filter.
     *
     * @return array Filtered query vars.
     */
    public function filterQueryVars($_vars)
    {
        $_vars     = (array) $_vars; // Force array.
        $cache_key = $_vars === $_GET ? md5(serialize($_vars)) : '';

        if ($cache_key && ($vars = &$this->staticKey(__FUNCTION__, $cache_key)) !== null) {
            return $vars; // Already cached this.
        }
        $vars                          = $_vars; // Copy.
        $short_name_lc                 = mb_strtolower(SHORT_NAME);
        $ignore_get_request_vars_regex = defined('COMET_CACHE_IGNORE_GET_REQUEST_VARS') ? COMET_CACHE_IGNORE_GET_REQUEST_VARS : '';

        foreach ($vars as $_key => $_value) {
            if (!is_string($_key)) {
                continue; // Not applicable.
            } elseif ($_key === $short_name_lc.'AC' || $_key === $short_name_lc.'ABC') {
                unset($vars[$_key]);
            } elseif ($ignore_get_request_vars_regex && preg_match($ignore_get_request_vars_regex, $_key)) {
                unset($vars[$_key]);
            }
        } // unset($_key, $_value); // Housekeeping.

        return $vars = $vars ? $this->ksortDeep($vars) : [];
    }

    /**
     * Cache-path suffix frag (regex).
     *
     * @since 151220 Enhancing translation support.
     *
     * @param string $regex_suffix_frag Existing regex suffix frag?
     *
     * @return string Cache-path suffix frag (regex).
     */
    public function cachePathRegexSuffixFrag($regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        if ($regex_suffix_frag === $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG) {
            return $this->cachePathRegexDefaultSuffixFrag();
        }
        return (string) $regex_suffix_frag;
    }

    /**
     * Default cache-path suffix frag (regex).
     *
     * @since 151220 Enhancing translation support.
     *
     * @return string Default cache-path suffix frag (regex).
     */
    public function cachePathRegexDefaultSuffixFrag()
    {
        if ($this->isPlugin() && !empty($GLOBALS['wp_rewrite'])) {
            $pagination_base          = $GLOBALS['wp_rewrite']->pagination_base;
            $comments_pagination_base = $GLOBALS['wp_rewrite']->comments_pagination_base;
            return '(?:\/index|\/amp)?(?:\.|\/(?:'.preg_quote($pagination_base, '/').'\/[0-9]+|'.preg_quote($comments_pagination_base, '/').'\-[0-9]+)[.\/])';
        } else {
            return '(?:\/index|\/amp)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])';
        }
    }

    /**
     * Converts a URL into a `cache/path` based on input `$flags`.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $url               The input URL to convert.
     * @param string $with_user_token   Optional user token (if applicable).
     * @param string $with_version_salt Optional version salt (if applicable).
     * @param int    $flags             Optional flags. A bitmask via `$this::CACHE_PATH_*` constants.
     *
     * @return string The resulting `cache/path` based on the input `$url` & `$flags`.
     */
    public function buildCachePath($url, $with_user_token = '', $with_version_salt = '', $flags = self::CACHE_PATH_DEFAULT)
    {
        # Force parameter types.

        $url               = trim((string) $url);
        $with_user_token   = trim((string) $with_user_token);
        $with_version_salt = trim((string) $with_version_salt);

        # Initialize variables.

        $is_multisite                = is_multisite();
        $can_consider_domain_mapping = $is_multisite && $this->canConsiderDomainMapping();
        $cache_path                  = ''; // Initialize cache path being built here.

        # Deal w/ domain mapping considerations.

        if ($flags & $this::CACHE_PATH_CONSIDER_DOMAIN_MAPPING && $is_multisite && $can_consider_domain_mapping) {
            if ($flags & $this::CACHE_PATH_REVERSE_DOMAIN_MAPPING) {
                $url = $this->domainMappingReverseUrlFilter($url);
            } else {
                $url = $this->domainMappingUrlFilter($url);
            }
        }
        # Validate the URL we have now.

        if (!$url || !($url_parts = $this->parseUrl($url))) {
            return $cache_path = ''; // Not possible.
        }
        if (empty($url_parts['scheme']) || $url_parts['scheme'] === '//') {
            return $cache_path = ''; // Not possible.
        }
        if (empty($url_parts['host'])) {
            return $cache_path = ''; // Not possible.
        }
        # Initialize additional variables; based on the parsed URL.

        $is_url_domain_mapped = $is_multisite && $can_consider_domain_mapping && $this->domainMappingBlogId($url);
        $host_base_dir_tokens = $this->hostBaseDirTokens(false, $is_url_domain_mapped, !empty($url_parts['path']) ? $url_parts['path'] : '/');

        $is_a_multisite_base_dir      = $is_multisite && $host_base_dir_tokens && $host_base_dir_tokens !== '/' && mb_stripos(!empty($url_parts['path']) ? rtrim($url_parts['path'], '/').'/' : '/', $host_base_dir_tokens) === 0;
        $is_a_multisite_base_dir_root = $is_multisite && $is_a_multisite_base_dir && strcasecmp(trim($host_base_dir_tokens, '/'), trim(!empty($url_parts['path']) ? $url_parts['path'] : '/', '/')) === 0;

        # Build and return the cache path.

        if (!($flags & $this::CACHE_PATH_NO_SCHEME)) {
            $cache_path .= $url_parts['scheme'].'/';
        }
        if (!($flags & $this::CACHE_PATH_NO_HOST)) {
            $cache_path .= $url_parts['host'].'/';
            // Put multisite sub-roots into a host directory of their own.
            // e.g., `example-com[[-base]-child1]` instead of `example-com`.
            if ($is_a_multisite_base_dir && $host_base_dir_tokens && $host_base_dir_tokens !== '/') {
                $host_base_dir_suffix = preg_replace('/[^a-z0-9.]/ui', '-', rtrim($host_base_dir_tokens, '/'));
                $cache_path           = rtrim($cache_path, '/').$host_base_dir_suffix.'/';
            }
        }
        if (!($flags & $this::CACHE_PATH_NO_PATH)) {
            if (isset($url_parts['path'][201])) {
                $_path_tmp = '/'; // Initialize tmp path.
                foreach (explode('/', $url_parts['path']) as $_path_component) {
                    if (!isset($_path_component[0])) {
                        continue; // Empty.
                    }
                    if (isset($_path_component[201])) {
                        $_path_component = 'lpc-'.sha1($_path_component);
                    }
                    $_path_tmp .= $_path_component.'/';
                }
                $url_parts['path'] = $_path_tmp; // Shorter components.
                unset($_path_component, $_path_tmp); // Housekeeping.

                if (isset($url_parts['path'][2001])) {
                    $url_parts['path'] = '/lp-'.sha1($url_parts['path']).'/';
                }
            } // Now add the path and check for a possible root `index/` suffix.
            if (!empty($url_parts['path']) && mb_strlen($url_parts['path'] = trim($url_parts['path'], '\\/'." \t\n\r\0\x0B"))) {
                $cache_path .= $url_parts['path'].'/'; // Add the path as it exists.

                if (!($flags & $this::CACHE_PATH_NO_PATH_INDEX) && $is_multisite && $is_a_multisite_base_dir_root) {
                    // We should build an `index/` when this ends with a multisite [[/base]/child1] root.
                    //  e.g., `http/example-com[[-base]-child1][[/base]/child1]` is a root directory.
                    $cache_path .= 'index/'; // Use an index suffix.
                }
            } elseif (!($flags & $this::CACHE_PATH_NO_PATH_INDEX)) {
                $cache_path .= 'index/';
            }
        }
        $cache_path = str_replace('.', '-', mb_strtolower($cache_path));

        if (!($flags & $this::CACHE_PATH_NO_QUV)) {
            if (!($flags & $this::CACHE_PATH_NO_QUERY)) {
                if (isset($url_parts['query']) && $url_parts['query'] !== '') {
                    // Support for ignored GET vars.
                    parse_str($url_parts['query'], $_query_vars);
                    $_query_vars = $this->filterQueryVars($_query_vars);
                    // ↑ Also sorts query vars for smarter caching.

                    if ($_query_vars) { // If we have cacheable query vars.
                        $cache_path = rtrim($cache_path, '/').'.q/'.md5(serialize($_query_vars)).'/';
                    } // unset($_query_vars); // Housekeeping.
                }
            }
            if (!($flags & $this::CACHE_PATH_NO_USER)) {
                if ($with_user_token !== '') {
                    $cache_path = rtrim($cache_path, '/').'.u/'.str_replace(['/', '\\'], '-', $with_user_token).'/';
                }
            }
            if (!($flags & $this::CACHE_PATH_NO_VSALT)) {
                if ($with_version_salt !== '') {
                    $cache_path = rtrim($cache_path, '/').'.v/'.str_replace(['/', '\\'], '-', $with_version_salt).'/';
                }
            }
        }
        $cache_path = trim(preg_replace(['/\/+/u', '/\.+/u'], ['/', '.'], $cache_path), '/');

        if ($flags & $this::CACHE_PATH_ALLOW_WD_REGEX) {
            $cache_path = preg_replace('/[^a-z0-9\/.+*\^$]/ui', '-', $cache_path);
        } elseif ($flags & $this::CACHE_PATH_ALLOW_WILDCARDS) {
            $cache_path = preg_replace('/[^a-z0-9\/.+*]/ui', '-', $cache_path);
        } else {
            $cache_path = preg_replace('/[^a-z0-9\/.+]/ui', '-', $cache_path);
        }
        if (!($flags & $this::CACHE_PATH_NO_EXT)) {
            $cache_path .= '.html';
        }
        return $cache_path;
    }

    /**
     * Regex pattern for a call to `deleteFilesFromCacheDir()`.
     *
     * @since 151114 Updated to support an arbitrary URL instead of a regex frag.
     *
     * @param string $url               The input URL to convert. This CAN be left empty when necessary.
     *                                  If empty, the final regex pattern will be `/^'.$regex_suffix_frag.'/ui`.
     *                                  If empty, it's a good idea to start `$regex_suffix_frag` with `.*?`.
     * @param string $regex_suffix_frag Regex fragment to come after the `$regex_frag`.
     *                                  Defaults to: `(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])`.
     *                                  Note: this should NOT have delimiters; i.e. do NOT start or end with `/`.
     *                                  See also: {@link $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG}.
     *
     * @return string Regex pattern for a call to `deleteFilesFromCacheDir()`.
     */
    public function buildCachePathRegex($url, $regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        $url               = trim((string) $url);
        $regex_suffix_frag = $this->cachePathRegexSuffixFrag($regex_suffix_frag);
        $cache_path_regex  = ''; // Initialize regex.

        if ($url) {
            $flags = $this::CACHE_PATH_NO_SCHEME // Scheme added below.
                                | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT;
            $cache_path       = $this->buildCachePath($url, '', '', $flags); // Without the scheme.
            $cache_path_regex = isset($cache_path[0]) ? '\/https?\/'.preg_quote($cache_path, '/') : '';
        }
        return '/^'.$cache_path_regex.$regex_suffix_frag.'/ui';
    }

    /**
     * Regex pattern for a call to `deleteFilesFromHostCacheDir()`.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $url               The input URL to convert. This CAN be left empty when necessary.
     *                                  If empty, the final regex pattern will be `/^'.$regex_suffix_frag.'/ui`.
     *                                  If empty, it's a good idea to start `$regex_suffix_frag` with `.*?`.
     * @param string $regex_suffix_frag Regex fragment to come after the relative cache/path regex frag.
     *                                  Defaults to: `(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])`.
     *                                  Note: this should NOT have delimiters; i.e. do NOT start or end with `/`.
     *                                  See also: {@link $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG}.
     *
     * @return string Regex pattern for a call to `deleteFilesFromHostCacheDir()`.
     */
    public function buildHostCachePathRegex($url, $regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        $url                           = trim((string) $url);
        $regex_suffix_frag             = $this->cachePathRegexSuffixFrag($regex_suffix_frag);
        $abs_relative_cache_path_regex = ''; // Initialize regex.
        $is_url_domain_mapped          = false; // Initialize.

        if ($url) {
            if (is_multisite() && $this->canConsiderDomainMapping()) {
                // Shortest possible URI; i.e., consider domain mapping.
                $url                  = $this->domainMappingUrlFilter($url);
                $is_url_domain_mapped = $url && $this->domainMappingBlogId($url);
            }
            if ($url && ($url_parts = $this->parseUrl($url)) && !empty($url_parts['host'])) {
                $flags = $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_HOST
                         | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT;

                $host_base_dir_tokens = $this->hostBaseDirTokens(false, $is_url_domain_mapped, !empty($url_parts['path']) ? $url_parts['path'] : '/');
                $host_url             = rtrim('http://'.$url_parts['host'].$host_base_dir_tokens, '/');
                $host_cache_path      = $this->buildCachePath($host_url, '', '', $flags);

                $cache_path          = $this->buildCachePath($url, '', '', $flags);
                $relative_cache_path = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/ui', '', $cache_path);

                $abs_relative_cache_path       = isset($relative_cache_path[0]) ? '/'.$relative_cache_path : '';
                $abs_relative_cache_path_regex = isset($abs_relative_cache_path[0]) ? preg_quote($abs_relative_cache_path, '/') : '';
            }
        }
        return '/^'.$abs_relative_cache_path_regex.$regex_suffix_frag.'/ui';
    }

    /**
     * Regex pattern for a call to `deleteFilesFromCacheDir()`.
     *
     * @since 151114 Improving watered-down regex syntax.
     *
     * @param string $url The input URL to convert. This CAN be left empty when necessary.
     *                    This may also contain watered-down regex; i.e., `*^$` characters are OK here.
     *                    However, `^$` are discarded, as they are unnecessary in this context.
     *
     *   If empty, the final regex pattern will be `/^'.$regex_suffix_frag.'/ui`.
     *   If empty, it's a good idea to start `$regex_suffix_frag` with `.*?`.
     * @param string $regex_suffix_frag Regex fragment to come after the `$regex_frag`.
     *                                  Defaults to: `(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])`.
     *                                  Note: this should NOT have delimiters; i.e. do NOT start or end with `/`.
     *                                  See also: {@link $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG}.
     *
     * @return string Regex pattern for a call to `deleteFilesFromCacheDir()`.
     */
    public function buildCachePathRegexFromWcUrl($url, $regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        $url               = trim((string) $url, '^$');
        $regex_suffix_frag = $this->cachePathRegexSuffixFrag($regex_suffix_frag);
        $cache_path_regex  = ''; // Initialize regex.

        if ($url) { // After `^$` trimming above.
            $flags = $this::CACHE_PATH_ALLOW_WILDCARDS | $this::CACHE_PATH_NO_SCHEME
                                | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT;
            $cache_path       = $this->buildCachePath($url, '', '', $flags); // Without the scheme.
            $cache_path_regex = isset($cache_path[0]) ? '\/https?\/'.$this->wdRegexToActualRegexFrag($cache_path) : '';
        }
        return '/^'.$cache_path_regex.$regex_suffix_frag.'/ui';
    }

    /**
     * Regex pattern for a call to `deleteFilesFromHostCacheDir()`.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $uris              A line-delimited list of URIs. These may contain `*^$` also.
     *                                  However, `^$` are discarded, as they are unnecessary in this context.
     * @param string $regex_suffix_frag Regex fragment to come after each relative cache/path.
     *                                  Defaults to: `(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])`.
     *                                  Note: this should NOT have delimiters; i.e. do NOT start or end with `/`.
     *                                  See also: {@link $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG}.
     *
     * @return string Regex pattern for a call to `deleteFilesFromHostCacheDir()`.
     */
    public function buildHostCachePathRegexFragsFromWcUris($uris, $regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        $uris              = trim((string) $uris);
        $regex_suffix_frag = $this->cachePathRegexSuffixFrag($regex_suffix_frag);

        $flags = $this::CACHE_PATH_ALLOW_WILDCARDS | $this::CACHE_PATH_NO_SCHEME | $this::CACHE_PATH_NO_HOST
                 | $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT;

        $host            = 'doesnt-matter.foo.bar';
        $host_url        = rtrim('http://'.$host, '/');
        $host_cache_path = $this->buildCachePath($host_url, '', '', $flags);
        $uri_patterns    = array_unique(preg_split('/['."\r\n".']+/', $uris, -1, PREG_SPLIT_NO_EMPTY));

        foreach ($uri_patterns as $_key => &$_uri_pattern) {
            if (($_uri_pattern = trim($_uri_pattern, '^$'))) {
                $_cache_path          = $this->buildCachePath($host_url.'/'.trim($_uri_pattern, '/'), '', '', $flags);
                $_relative_cache_path = preg_replace('/^'.preg_quote($host_cache_path, '/').'(?:\/|$)/ui', '', $_cache_path);
                $_uri_pattern         = $this->wdRegexToActualRegexFrag($_relative_cache_path);
            }
            if (!$_uri_pattern) {
                unset($uri_patterns[$_key]); // Remove it.
            }
        }
        unset($_key, $_uri_pattern, $_cache_path, $_relative_cache_path); // Housekeeping.

        return $uri_patterns ? '(?:'.implode('|', array_unique($uri_patterns)).')'.$regex_suffix_frag : '';
    }

    /**
     * Regex pattern for a call to `deleteFilesFromCacheDir()`.
     *
     * @since 151114 Moving this low-level routine into a method of a different name.
     *
     * @param string $regex_frag        A regex fragment. This CAN be left empty when necessary.
     *                                  If empty, the final regex pattern will be `/^'.$regex_suffix_frag.'/ui`.
     *                                  If empty, it's a good idea to start `$regex_suffix_frag` with `.*?`.
     * @param string $regex_suffix_frag Regex fragment to come after the `$regex_frag`.
     *                                  Defaults to: `(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])`.
     *                                  Note: this should NOT have delimiters; i.e. do NOT start or end with `/`.
     *                                  See also: {@link $this::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG}.
     *
     * @return string Regex pattern for a call to `deleteFilesFromCacheDir()`.
     */
    public function assembleCachePathRegex($regex_frag, $regex_suffix_frag = self::CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG)
    {
        $regex_frag        = (string) $regex_frag;
        $regex_suffix_frag = $this->cachePathRegexSuffixFrag($regex_suffix_frag);

        return '/^'.$regex_frag.$regex_suffix_frag.'/ui';
    }
}
