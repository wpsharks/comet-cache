<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait UrlUtils
{
    /**
     * Parses a URL.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param string $url_uri_qsl Input URL, URI, or query string w/ a leading `?`.
     * @param int    $component   Optional component to retrieve.
     *
     * @return array|string|int|null Array, else `string|int|null` component value.
     */
    public function parseUrl($url_uri_qsl, $component = -1)
    {
        $url_uri_qsl = (string) $url_uri_qsl;
        $component   = (integer) $component;
        ${'//'}      = mb_strpos($url_uri_qsl, '//') === 0;

        if ($url_uri_qsl && mb_strpos($url_uri_qsl, '&amp;') !== false) {
            $url_uri_qsl = str_replace('&amp;', '&', $url_uri_qsl);
        }
        if ($component > -1) {
            if (${'//'} && $component === PHP_URL_SCHEME) {
                return $part = '//';
            }
            return $part = parse_url($url_uri_qsl, $component);
        } else {
            if (!is_array($parts = parse_url($url_uri_qsl))) {
                return $parts = [];
            }
            if (${'//'}) {
                $parts['scheme'] = '//';
            }
            return $parts;
        }
    }

    /**
     * Unparses a URL.
     *
     * @since 150821 Improving multisite compat.
     *
     * @param array $parts Input URL parts.
     *
     * @return string Unparsed URL in string format.
     */
    public function unParseUrl(array $parts)
    {
        $scheme   = '';
        $host     = '';
        $port     = '';
        $user     = '';
        $pass     = '';
        $path     = '';
        $query    = '';
        $fragment = '';

        if (!empty($parts['scheme'])) {
            if ($parts['scheme'] === '//') {
                $scheme = $parts['scheme'];
            } else {
                $scheme = $parts['scheme'].'://';
            }
        }
        if (!empty($parts['host'])) {
            $host = $parts['host'];
        }
        if (!empty($parts['port'])) {
            $port = ':'.$parts['port'];
        }
        if (!empty($parts['user'])) {
            $user = $parts['user'];
        }
        if (!empty($parts['pass'])) {
            $pass = $parts['pass'];
        }
        if ($user || $pass) {
            $pass .= '@';
        }
        if (!empty($parts['path'])) {
            $path = '/'.ltrim($parts['path'], '/');
        }
        if (!empty($parts['query'])) {
            $query = '?'.$parts['query'];
        }
        if (!empty($parts['fragment'])) {
            $fragment = '#'.$parts['fragment'];
        }
        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment;
    }

    /**
     * Is the current request over SSL?
     *
     * @since 150422 Rewrite.
     *
     * @return bool `TRUE` if the current request is over SSL.
     *
     * @note The return value of this function is cached to reduce overhead on repeat calls.
     */
    public function isSsl()
    {
        if (!is_null($is = &$this->staticKey('isSsl'))) {
            return $is; // Already cached this.
        }
        if (!empty($_SERVER['SERVER_PORT'])) {
            if ((integer) $_SERVER['SERVER_PORT'] === 443) {
                return $is = true;
            }
        }
        if (!empty($_SERVER['HTTPS'])) {
            if (filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
                return $is = true;
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            if (strcasecmp((string) $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0) {
                return $is = true;
            }
        }
        return $is = false;
    }

    /**
     * Current URL.
     *
     * @since 150821 Improving multisite compat.
     *
     * @return string Current URL.
     */
    public function currentUrl()
    {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            return ''; // Not possible.
        }
        return ($this->isSsl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}
