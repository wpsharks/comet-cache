<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait TokenUtils
{
    

    /**
     * Current host.
     *
     * @since 150422 Rewrite.
     *
     * @param bool   $dashify                        Optional, defaults to a `FALSE` value.
     *                                               If `TRUE`, the token is returned with dashes in place of `[^a-z0-9]`.
     * @param bool   $consider_domain_mapping        Consider?
     * @param string $consider_domain_mapping_domain A specific domain?
     *
     * @return string Current host.
     *
     * @note The return value of this function is cached to reduce overhead on repeat calls.
     */
    public function hostToken($dashify = false, $consider_domain_mapping = false, $consider_domain_mapping_domain = '')
    {
        if (!is_null($token = &$this->staticKey('hostToken', [$dashify, $consider_domain_mapping, $consider_domain_mapping_domain]))) {
            return $token; // Already cached this.
        }
        $token = ''; // Initialize token value.

        if (!is_multisite() || $this->isAdvancedCache()) {
            $token = !empty($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
        } elseif ($consider_domain_mapping && $this->canConsiderDomainMapping()) {
            if (($consider_domain_mapping_domain = trim((string) $consider_domain_mapping_domain))) {
                $token = $consider_domain_mapping_domain;
            } elseif ($this->isDomainMapping()) {
                $token = !empty($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
            } else { // For the current blog ID.
                $token = $this->domainMappingUrlFilter($this->currentUrl());
                $token = $this->parseUrl($token, PHP_URL_HOST);
            }
        }
        if (!$token) { // Use default?
            $token = !empty($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
        }
        if (!$token && $this->isPlugin()) {
            $token = (string) parse_url(home_url(), PHP_URL_HOST);
        }
        if ($token) { // Have token?
            $token = mb_strtolower($token);
            if ($dashify) { // Dashify it?
                $token = preg_replace('/[^a-z0-9]/ui', '-', $token);
                $token = trim($token, '-');
            }
        }
        return $token;
    }

    /**
     * Host for a specific blog.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param bool   $dashify                        Optional, defaults to a `FALSE` value.
     *                                               If `TRUE`, the token is returned with dashes in place of `[^a-z0-9]`.
     * @param bool   $consider_domain_mapping        Consider?
     * @param string $consider_domain_mapping_domain A specific domain?
     * @param bool   $fallback                       Fallback on blog's domain when mapping?
     * @param int    $blog_id                        For which blog ID?
     *
     * @return string Host for a specific blog.
     *
     * @note The return value of this function is NOT cached in support of `switch_to_blog()`.
     */
    public function hostTokenForBlog($dashify = false, $consider_domain_mapping = false, $consider_domain_mapping_domain = '', $fallback = false, $blog_id = 0)
    {
        if (!is_multisite() || $this->isAdvancedCache()) {
            return $this->hostToken($dashify, $consider_domain_mapping, $consider_domain_mapping_domain);
        }
        $token = ''; // Initialize token value.

        if ($consider_domain_mapping && $this->canConsiderDomainMapping()) {
            if (($consider_domain_mapping_domain = trim((string) $consider_domain_mapping_domain))) {
                $token = $consider_domain_mapping_domain; // Force this value.
            } else {
                $token = $this->domainMappingBlogDomain($blog_id, $fallback);
            }
        } elseif (($blog_details = $this->blogDetails($blog_id))) {
            $token = $blog_details->domain; // Unmapped domain.
        }
        if ($token) { // Have token?
            $token = mb_strtolower($token);
            if ($dashify) { // Dashify it?
                $token = preg_replace('/[^a-z0-9]/ui', '-', $token);
                $token = trim($token, '-');
            }
        }
        return $token;
    }

    /**
     * Current site's base directory.
     *
     * @since 150422 Rewrite.
     *
     * @param bool $dashify                 Optional, defaults to a `FALSE` value.
     *                                      If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
     * @param bool $consider_domain_mapping Consider?
     *
     * @return string Current site's base directory.
     *
     * @note The return value of this function is cached to reduce overhead on repeat calls.
     */
    public function hostBaseToken($dashify = false, $consider_domain_mapping = false)
    {
        if (!is_null($token = &$this->staticKey('hostBaseToken', [$dashify, $consider_domain_mapping]))) {
            return $token; // Already cached this.
        }
        $token = '/'; // Assume NOT multisite; or own domain.

        if (!is_multisite()) {
            return $token; // Not applicable.
        }
        if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
            return $token; // Not applicable.
        }
        if ($consider_domain_mapping && $this->canConsiderDomainMapping()) {
            return $token; // Not applicable.
        }
        if (defined('PATH_CURRENT_SITE')) {
            $token = (string) PATH_CURRENT_SITE;
        }
        $token = trim($token, '\\/'." \t\n\r\0\x0B");
        $token = isset($token[0]) ? '/'.$token.'/' : '/';

        if ($token !== '/' && $dashify) {
            $token = preg_replace('/[^a-z0-9\/]/ui', '-', $token);
            $token = trim($token, '-');
        }
        return $token;
    }

    /**
     * Current blog's sub-directory.
     *
     * @since 150422 Rewrite.
     *
     * @param bool   $dashify                 Optional, defaults to a `FALSE` value.
     *                                        If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
     * @param bool   $consider_domain_mapping Consider?
     * @param string $path                    Defaults to the current URI path.
     *
     * @return string Current blog's sub-directory.
     *
     * @note The return value of this function is cached to reduce overhead on repeat calls.
     */
    public function hostDirToken($dashify = false, $consider_domain_mapping = false, $path = null)
    {
        if (!isset($path)) { // Use current/default path?
            $path = (string) $this->parseUrl($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        $path = '/'.ltrim((string) $path, '/'); // Force leading slash.

        if (!is_null($token = &$this->staticKey('hostDirToken', [$dashify, $consider_domain_mapping, $path]))) {
            return $token; // Already cached this.
        }
        $token = '/'; // Assume NOT multisite; or own domain.

        if (!is_multisite()) {
            return $token; // Not applicable.
        }
        if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
            return $token; // Not applicable.
        }
        if ($consider_domain_mapping && $this->canConsiderDomainMapping()) {
            return $token; // Not applicable.
        }
        if ($path && $path !== '/' && ($host_base_token = trim($this->hostBaseToken(), '/'))) {
            $path_minus_base = preg_replace('/^\/'.preg_quote($host_base_token, '/').'(\/|$)/ui', '${1}', $path);
        } else {
            $path_minus_base = $path; // Default value.
        }
        list($token) = explode('/', trim($path_minus_base, '/'));
        $token       = trim($token, '\\/'." \t\n\r\0\x0B");
        $token       = isset($token[0]) ? '/'.$token.'/' : '/';

        if ($token !== '/') { // Perhaps NOT the main site?
            $blog_paths_file = $this->cacheDir().'/'.mb_strtolower(SHORT_NAME).'-blog-paths';
            if (!is_file($blog_paths_file) || !in_array($token, unserialize(file_get_contents($blog_paths_file)), true)) {
                $token = '/'; // NOT a real/valid child blog path.
            }
        }
        if ($token !== '/' && $dashify) {
            $token = preg_replace('/[^a-z0-9\/]/ui', '-', $token);
            $token = trim($token, '-');
        }
        return $token;
    }

    /**
     * A blog's sub-directory.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param bool $dashify                 Optional, defaults to a `FALSE` value.
     *                                      If `TRUE`, the token is returned with dashes in place of `[^a-z0-9]`.
     * @param bool $consider_domain_mapping Consider?
     * @param int  $blog_id                 For which blog ID?
     *
     * @return string A blog's sub-directory.
     *
     * @note The return value of this function is NOT cached in support of `switch_to_blog()`.
     */
    public function hostDirTokenForBlog($dashify = false, $consider_domain_mapping = false, $blog_id = 0)
    {
        if (!is_multisite() || $this->isAdvancedCache()) {
            return $this->hostDirToken($dashify, $consider_domain_mapping);
        }
        $token = '/'; // Initialize token value.

        if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
            return $token; // Not applicable.
        }
        if ($consider_domain_mapping && $this->canConsiderDomainMapping()) {
            return $token; // Not applicable.
        }
        if (($blog_details = $this->blogDetails($blog_id))) {
            $path = $blog_details->path; // e.g., `[/base]/path/` (includes base).
            if ($path && $path !== '/' && ($host_base_token = trim($this->hostBaseToken(), '/'))) {
                $path_minus_base = preg_replace('/^\/'.preg_quote($host_base_token, '/').'(\/|$)/ui', '${1}', $path);
            } else {
                $path_minus_base = $path; // Default value.
            }
            list($token) = explode('/', trim($path_minus_base, '/'));
        }
        $token = trim($token, '\\/'." \t\n\r\0\x0B");
        $token = isset($token[0]) ? '/'.$token.'/' : '/';

        if ($token !== '/') { // Perhaps NOT the main site?
            $blog_paths_file = $this->cacheDir().'/'.mb_strtolower(SHORT_NAME).'-blog-paths';
            if (!is_file($blog_paths_file) || !in_array($token, unserialize(file_get_contents($blog_paths_file)), true)) {
                $token = '/'; // NOT a real/valid child blog path.
            }
        }
        if ($token !== '/' && $dashify) {
            $token = preg_replace('/[^a-z0-9\/]/ui', '-', $token);
            $token = trim($token, '-');
        }
        return $token;
    }

    /**
     * Current site's base directory & current blog's sub-directory.
     *
     * @since 150422 Rewrite.
     *
     * @param bool   $dashify                 Optional, defaults to a `FALSE` value.
     *                                        If `TRUE`, the tokens are returned with dashes in place of `[^a-z0-9\/]`.
     * @param bool   $consider_domain_mapping Consider?
     * @param string $path                    Defaults to the current URI path.
     *
     * @return string Current site's base directory & current blog's sub-directory.
     *
     * @note The return value of this function is cached to reduce overhead on repeat calls.
     */
    public function hostBaseDirTokens($dashify = false, $consider_domain_mapping = false, $path = null)
    {
        if (!is_null($tokens = &$this->staticKey('hostBaseDirTokens', [$dashify, $consider_domain_mapping, $path]))) {
            return $tokens; // Already cached this.
        }
        $tokens = $this->hostBaseToken($dashify, $consider_domain_mapping);
        $tokens .= $this->hostDirToken($dashify, $consider_domain_mapping, $path);

        return $tokens = preg_replace('/\/+/u', '/', $tokens);
    }

    /**
     * A site's base directory & a blog's sub-directory.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param bool $dashify                 Optional, defaults to a `FALSE` value.
     *                                      If `TRUE`, the tokens are returned with dashes in place of `[^a-z0-9\/]`.
     * @param bool $consider_domain_mapping Consider?
     * @param int  $blog_id                 For which blog ID?
     *
     * @return string A site's base directory & a blog's sub-directory.
     *
     * @note The return value of this function is NOT cached in support of `switch_to_blog()`.
     */
    public function hostBaseDirTokensForBlog($dashify = false, $consider_domain_mapping = false, $blog_id = 0)
    {
        $tokens = $this->hostBaseToken($dashify, $consider_domain_mapping);
        $tokens .= $this->hostDirTokenForBlog($dashify, $consider_domain_mapping, $blog_id);

        return $tokens = preg_replace('/\/+/u', '/', $tokens);
    }

    
}
