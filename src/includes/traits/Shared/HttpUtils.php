<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait HttpUtils
{
    /**
     * Current HTTP protocol.
     *
     * @since 150422 Rewrite.
     *
     * @return string Current HTTP protocol.
     */
    public function httpProtocol()
    {
        if (!is_null($protocol = &$this->staticKey('httpProtocol'))) {
            return $protocol; // Already cached this.
        }
        if (!empty($_SERVER['SERVER_PROTOCOL']) && is_string($_SERVER['SERVER_PROTOCOL'])) {
            $protocol = mb_strtoupper($_SERVER['SERVER_PROTOCOL']);
        }
        if (!$protocol || mb_stripos($protocol, 'HTTP/') !== 0) {
            $protocol = 'HTTP/1.0'; // Default value.
        }
        return $protocol;
    }

    /**
     * PHP {@link headers_list()} + HTTP status.
     *
     * @since 150422 Rewrite.
     *
     * @return array PHP {@link headers_list()} + HTTP status.
     *
     * @warning Do NOT call until end of script execution.
     */
    public function headersList()
    {
        if (!is_null($headers = &$this->staticKey('headersList'))) {
            return $headers; // Already cached this.
        }
        $headers = headers_list(); // Lacks status.

        if (($status = (string) $this->httpStatus())) {
            array_unshift($headers, $this->httpProtocol().' '.$status);
        }
        return $headers;
    }

    /**
     * PHP {@link headers_list()} + HTTP status.
     *
     * @since 150422 Rewrite.
     *
     * @return array PHP {@link headers_list()} + HTTP status.
     *
     * @warning Do NOT call until end of script execution.
     */
    public function cacheableHeadersList()
    {
        if (!is_null($headers = &$this->staticKey('cacheableHeadersList'))) {
            return $headers; // Already cached this.
        }
        $headers = headers_list(); // Lacks status.

        $cacheable_headers = [
            'Access-Control-Allow-Origin',
            'Accept-Ranges',
            'Age',
            'Allow',
            'Cache-Control',
            'Connection',
            'Content-Encoding',
            'Content-Language',
            'Content-Length',
            'Content-Location',
            'Content-MD5',
            'Content-Disposition',
            'Content-Range',
            'Content-Type',
            'Date',
            'ETag',
            'Expires',
            'Last-Modified',
            'Link',
            'Location',
            'P3P',
            'Pragma',
            'Proxy-Authenticate',
            'Refresh',
            'Retry-After',
            'Server',
            'Status',
            'Strict-Transport-Security',
            'Trailer',
            'Transfer-Encoding',
            'Upgrade',
            'Vary',
            'Via',
            'Warning',
            'WWW-Authenticate',
            'X-Frame-Options',
            'Public-Key-Pins',
            'X-XSS-Protection',
            'Content-Security-Policy',
            'X-Content-Security-Policy',
            'X-WebKit-CSP',
            'X-Content-Type-Options',
            'X-Powered-By',
            'X-UA-Compatible',
        ];
        $cacheable_headers = array_map('mb_strtolower', $cacheable_headers);

        foreach ($headers as $_key => $_header) {
            $_header = mb_strtolower((string) strstr($_header, ':', true));
            if (!$_header || !in_array($_header, $cacheable_headers, true)) {
                unset($headers[$_key]);
            }
        }
        unset($_key, $_header); // Housekeeping.

        if (($status = (string) $this->httpStatus())) {
            array_unshift($headers, $this->httpProtocol().' '.$status);
        }
        return $headers;
    }

    /**
     * HTTP status code.
     *
     * @since 150422 Rewrite.
     *
     * @return int HTTP status code.
     *
     * @warning Do NOT call until end of script execution.
     *
     * @note Automatically updates HTTP status-related flags.
     */
    public function httpStatus()
    {
        if (!is_null($status = &$this->staticKey('httpStatus'))) {
            return $status; // Already cached this.
        }
        $status                   = 0; // Initialize.
        $has_property_is_404      = property_exists($this, 'is_404');
        $has_property_http_status = property_exists($this, 'http_status');

        if ($has_property_is_404 && $this->is_404) {
            $status = 404; // WordPress said so.
        } elseif (($code = (integer) http_response_code())) {
            $status = (integer) $code; // {@link \http_response_code()} available since PHP v5.4.
        } elseif ($has_property_http_status && (integer) $this->http_status) {
            $status = (integer) $this->http_status; // {@link \status_header()} filter.
        }
        if ($status && $has_property_http_status) {
            $this->http_status = $status; // Prefer over {@link status_header()}.
        }
        if ($status === 404 && $has_property_is_404) {
            $this->is_404 = true; // Prefer over {@link is_404()}.
        }
        return $status;
    }

    /**
     * Sends no-cache headers.
     *
     * @since 151220 Enhancing no-cache headers.
     */
    public function sendNoCacheHeaders()
    {
        header_remove('Last-Modified');
        header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
}
