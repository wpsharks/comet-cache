<?php
namespace WebSharks\ZenCache;



/*
 * Produces a token based on the current `$_SERVER['HTTP_HOST']`.
 *
 * @since 150422 Rewrite.
 *
 * @param boolean $dashify Optional, defaults to a `FALSE` value.
 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
 *
 * @return string Token based on the current `$_SERVER['HTTP_HOST']`.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->hostToken = function ($dashify = false) use ($self) {
    $dashify = (integer) $dashify;

    if (!is_null($token = &$self->staticKey('hostToken', $dashify))) {
        return $token; // Already cached this.
    }
    $token = !empty($_SERVER['HTTP_HOST'])
        ? strtolower((string) $_SERVER['HTTP_HOST'])
        : '';
    if ($dashify) {
        $token = preg_replace('/[^a-z0-9\/]/i', '-', $token);
        $token = trim($token, '-');
    }
    return $token;
};

/*
 * Produces a token based on the current site's base directory.
 *
 * @since 150422 Rewrite.
 *
 * @param boolean $dashify Optional, defaults to a `FALSE` value.
 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
 *
 * @return string Produces a token based on the current site's base directory;
 *    (i.e. in the case of a sub-directory multisite network).
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->hostBaseToken = function ($dashify = false) use ($self) {
    $dashify = (integer) $dashify;

    if (!is_null($token = &$self->staticKey('hostBaseToken', $dashify))) {
        return $token; // Already cached this.
    }
    $token = '/'; // Assume NOT multisite; or own domain.

    if (is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL)) {
        if (defined('PATH_CURRENT_SITE')) {
            $token = (string) PATH_CURRENT_SITE;
        } elseif (!empty($GLOBALS['base'])) {
            $token = (string) $GLOBALS['base'];
        }
        $token = trim($token, '\\/'." \t\n\r\0\x0B");
        $token = isset($token[0]) ? '/'.$token.'/' : '/';
    }
    if ($dashify && $token !== '/') {
        $token = preg_replace('/[^a-z0-9\/]/i', '-', $token);
        $token = trim($token, '-');
    }
    return $token;
};

/*
 * Produces a token based on the current blog's sub-directory.
 *
 * @since 150422 Rewrite.
 *
 * @param boolean $dashify Optional, defaults to a `FALSE` value.
 *    If `TRUE`, the token is returned with dashes in place of `[^a-z0-9\/]`.
 *
 * @return string Produces a token based on the current blog sub-directory
 *    (i.e. in the case of a sub-directory multisite network).
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->hostDirToken = function ($dashify = false) use ($self) {
    $dashify = (integer) $dashify;

    if (!is_null($token = &$self->staticKey('hostDirToken', $dashify))) {
        return $token; // Already cached this.
    }
    $token = '/'; // Assume NOT multisite; or own domain.

    if (is_multisite() && (!defined('SUBDOMAIN_INSTALL') || !SUBDOMAIN_INSTALL)) {
        $uri_minus_base = !empty($_SERVER['REQUEST_URI'])
            ? preg_replace('/^'.preg_quote($self->hostBaseToken(), '/').'/', '', (string) $_SERVER['REQUEST_URI'])
            : '';
        list($token) = explode('/', trim($uri_minus_base, '/'));
        $token       = isset($token[0]) ? '/'.$token.'/' : '/';

        if ($token !== '/' // Perhaps NOT the main site?
           && (!is_file(($cache_dir = $self->cacheDir()).'/zc-blog-paths')
               || !in_array($token, unserialize(file_get_contents($cache_dir.'/zc-blog-paths')), true))
        ) {
            $token = '/'; // NOT a real/valid child blog path.
        }
    }
    if ($dashify && $token !== '/') {
        $token = preg_replace('/[^a-z0-9\/]/i', '-', $token);
        $token = trim($token, '-');
    }
    return $token;
};

/*
 * Produces tokens for the current site's base directory & current blog's sub-directory.
 *
 * @since 150422 Rewrite.
 *
 * @param boolean $dashify Optional, defaults to a `FALSE` value.
 *    If `TRUE`, the tokens are returned with dashes in place of `[^a-z0-9\/]`.
 *
 * @return string Tokens for the current site's base directory & current blog's sub-directory.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->hostBaseDirTokens = function ($dashify = false) use ($self) {
    $dashify = (integer) $dashify;

    if (!is_null($tokens = &$self->staticKey('hostBaseDirTokens', $dashify))) {
        return $tokens; // Already cached this.
    }
    $tokens = $self->hostBaseToken($dashify).$self->hostDirToken($dashify);
    $tokens = preg_replace('/\/{2,}/', '/', $tokens);

    return $tokens;
};


