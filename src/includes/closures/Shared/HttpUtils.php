<?php
namespace WebSharks\ZenCache;

/*
 * Current HTTP protocol; i.e. `HTTP/1.0` or `HTTP/1.1`.
 *
 * @since 150422 Rewrite.
 *
 * @return string Current HTTP protocol; i.e. `HTTP/1.0` or `HTTP/1.1`.
 */
$self->httpProtocol = function () use ($self) {
    if (!is_null($protocol = &$self->staticKey('httpProtocol'))) {
        return $protocol; // Already cached this.
    }
    $protocol = !empty($_SERVER['SERVER_PROTOCOL'])
        ? strtoupper((string) $_SERVER['SERVER_PROTOCOL']) : 'HTTP/1.0';

    if ($protocol !== 'HTTP/1.1' && $protocol !== 'HTTP/1.0') {
        $protocol = 'HTTP/1.0'; // Default value.
    }
    return $protocol;
};

/*
 * An array of all headers sent via PHP; and the current HTTP status header too.
 *
 * @since 150422 Rewrite.
 *
 * @return array PHP {@link headers_list()} supplemented with
 *    HTTP status code when possible.
 *
 * @warning Do NOT call upon this method until the end of a script execution.
 */
$self->headersList = function () use ($self) {
    if (!is_null($headers = &$self->staticKey('headersList'))) {
        return $headers; // Already cached this.
    }
    $headers = headers_list(); // Lacks status.

    if (($status = (string) $self->httpStatus())) {
        array_unshift($headers, $self->httpProtocol().' '.$status);
    }
    return $headers;
};

/*
 * An array of all cacheable/safe headers sent via PHP; and the current HTTP status header too.
 *
 * @since 150422 Rewrite.
 *
 * @return array PHP {@link headers_list()} supplemented with
 *    HTTP status code when possible.
 *
 * @warning Do NOT call upon this method until the end of a script execution.
 *
 * @see http://www.websharks-inc.com/r/wikipedia-http-header-response-fields/
 */
$self->cacheableHeadersList = function () use ($self) {
    if (!is_null($headers = &$self->staticKey('cacheableHeadersList'))) {
        return $headers; // Already cached this.
    }
    $headers = headers_list(); // Lacks status.

    $cacheable_headers = array(
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
    );
    $cacheable_headers = array_map('strtolower', $cacheable_headers);

    foreach ($headers as $_key => $_header) {
        $_header = strtolower((string) strstr($_header, ':', true));
        if (!$_header || !in_array($_header, $cacheable_headers, true)) {
            unset($headers[$_key]);
        }
    }
    unset($_key, $_header); // Housekeeping.

    if (($status = (string) $self->httpStatus())) {
        array_unshift($headers, $self->httpProtocol().' '.$status);
    }
    return $headers;
};

/*
 * HTTP status code if at all possible.
 *
 * @since 150422 Rewrite.
 *
 * @return integer HTTP status code if at all possible; else `0`.
 *
 * @warning Do NOT call upon this method until the end of a script execution.
 *
 * @note Calling this method will automatically update HTTP status-related flags.
 */
$self->httpStatus = function () use ($self) {
    if (!is_null($status = &$self->staticKey('httpStatus'))) {
        return $status; // Already cached this.
    }
    $status                    = 0; // Initialize.
    $has_property_is_404       = property_exists($self, 'is_404');
    $has_property_http_status  = property_exists($self, 'http_status');

    if ($has_property_is_404 && $self->{'is_404'}) {
        $status = 404; // WordPress said so.
    } elseif ($self->functionIsPossible('http_response_code') && ($code = (integer) http_response_code())) {
        $status = (integer) $code; // {@link \http_response_code()} available since PHP v5.4.
    } elseif ($has_property_http_status && (integer) $self->{'http_status'}) {
        $status = (integer) $self->{'http_status'}; // {@link \status_header()} filter.
    }
    if ($status && $has_property_http_status) {
        $self->{'http_status'} = $status; // Prefer over {@link status_header()}.
    }
    if ($status === 404 && $has_property_is_404) {
        $self->{'is_404'} = true; // Prefer over {@link is_404()}.
    }
    return $status;
};
