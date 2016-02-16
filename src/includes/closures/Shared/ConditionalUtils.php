<?php
namespace WebSharks\ZenCache;

/**
 * PHP's language constructs.
 *
 * @type array PHP's language constructs.
 *            Keys are currently unimportant. Subject to change.
 *
 * @since 16xxxx First documented version.
 */
$self->php_constructs = array(
    'die'             => 'die',
    'echo'            => 'echo',
    'empty'           => 'empty',
    'exit'            => 'exit',
    'eval'            => 'eval',
    'include'         => 'include',
    'include_once'    => 'include_once',
    'isset'           => 'isset',
    'list'            => 'list',
    'require'         => 'require',
    'require_once'    => 'require_once',
    'return'          => 'return',
    'print'           => 'print',
    'unset'           => 'unset',
    '__halt_compiler' => '__halt_compiler',
);

/*
 * Is AdvancedCache class?
 *
 * @since 150821 Improving multisite compat.
 *
 * @return bool `TRUE` if this is the AdvancedCache class.
 */
$self->isAdvancedCache = function () use ($self) {
    return $self instanceof AdvancedCache;
};

/*
 * Is Plugin class?
 *
 * @since 150821 Improving multisite compat.
 *
 * @return bool `TRUE` if this is the Plugin class.
 */
$self->isPlugin = function () use ($self) {
    return $self instanceof Plugin;
};

/*
 * Is the current request method `POST`, `PUT` or `DELETE`?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if current request method is `POST`, `PUT` or `DELETE`.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isPostPutDeleteRequest = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isPostPutDeleteRequest'))) {
        return $is; // Already cached this.
    }
    if (!empty($_POST)) {
        return ($is = true);
    }
    if (!empty($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
        if (in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('POST', 'PUT', 'DELETE'), true)) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Does the current request include an uncacheable query string?
 *
 * @since 151002 Improving Nginx support.
 *
 * @return boolean True if request includes an uncacheable query string.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->requestContainsUncacheableQueryVars = function () use ($self) {
    if (!is_null($is = &$self->staticKey('requestContainsUncacheableQueryVars'))) {
        return $is; // Already cached this.
    }
    if (!empty($_GET) || !empty($_SERVER['QUERY_STRING'])) {
        $_get_count         = !empty($_GET) ? count($_GET) : 0;
        $is_abc_only        = $_get_count === 1 && isset($_GET[strtolower(SHORT_NAME).'ABC']);
        $is_nginx_q_only    = $_get_count === 1 && isset($_GET['q']) && $self->isNginx();
        $is_ac_get_var_true = isset($_GET[strtolower(SHORT_NAME).'AC']) && filter_var($_GET[strtolower(SHORT_NAME).'AC'], FILTER_VALIDATE_BOOLEAN);

        if (!$is_abc_only && !$is_nginx_q_only && !$is_ac_get_var_true) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Is the current request method is uncacheable?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if current request method is uncacheable.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isUncacheableRequestMethod = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isUncacheableRequestMethod'))) {
        return $is; // Already cached this.
    }
    if (!empty($_POST)) {
        return ($is = true);
    }
    if (!empty($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
        if (!in_array(strtoupper($_SERVER['REQUEST_METHOD']), array('GET'), true)) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Should the current user should be considered a logged-in user?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if current user should be considered a logged-in user.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isLikeUserLoggedIn = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isLikeUserLoggedIn'))) {
        return $is; // Already cached this.
    }
    if (defined('SID') && SID) {
        return ($is = true); // Session ID.
    }
    if (empty($_COOKIE)) {
        return ($is = false); // No cookies.
    }
    $regex_logged_in_cookies = '/^'; // Initialize.

    if (defined('LOGGED_IN_COOKIE') && LOGGED_IN_COOKIE) {
        $regex_logged_in_cookies .= preg_quote(LOGGED_IN_COOKIE, '/');
    } else { // Use the default hard-coded cookie prefix.
        $regex_logged_in_cookies .= 'wordpress_logged_in_';
    }
    $regex_logged_in_cookies .= '|comment_author_';
    $regex_logged_in_cookies .= '|wp[_\-]postpass_';

    $regex_logged_in_cookies .= '/'; // Close regex.

    foreach ($_COOKIE as $_key => $_value) {
        if ($_value && preg_match($regex_logged_in_cookies, $_key)) {
            return ($is = true); // Like a logged-in user.
        }
    } unset($_key, $_value); // Housekeeping.

    return ($is = false);
};

/*
 * Are we in a LOCALHOST environment?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if we are in a LOCALHOST environment.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isLocalhost = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isLocalhost'))) {
        return $is; // Already cached this.
    }
    if (defined('LOCALHOST')) {
        return ($is = (boolean) LOCALHOST);
    }
    if (preg_match('/\b(?:localhost|127\.0\.0\.1)\b/i', $self->hostToken())) {
        return ($is = true);
    }
    return ($is = false);
};



/*
 * Is the current request for a feed?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if the current request is for a feed.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isFeed = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isFeed'))) {
        return $is; // Already cached this.
    }
    if (isset($_REQUEST['feed'])) {
        return ($is = true);
    }
    if (!empty($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {
        if (preg_match('/\/feed(?:[\/?]|$)/', $_SERVER['REQUEST_URI'])) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Is a document/string an HTML/XML doc; or no?
 *
 * @since 150422 Rewrite.
 *
 * @param string $doc Input string/document to check.
 *
 * @return boolean True if `$doc` is an HTML/XML doc type.
 */
$self->isHtmlXmlDoc = function ($doc) use ($self) {
    $doc      = trim((string) $doc);
    $doc_hash = sha1($doc);

    if (!is_null($is = &$self->staticKey('isHtmlXmlDoc', $doc_hash))) {
        return $is; // Already cached this.
    }
    if (stripos($doc, '</html>') !== false) {
        return ($is = true);
    }
    if (stripos($doc, '<?xml') === 0) {
        return ($is = true);
    }
    return ($is = false);
};

/*
 * Does the current request have a cacheable content type?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if the current request has a cacheable content type.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 *
 * @warning Do NOT call upon this method until the end of a script execution.
 */
$self->hasACacheableContentType = function () use ($self) {
    if (!is_null($is = &$self->staticKey('hasACacheableContentType'))) {
        return $is; // Already cached this.
    }
    foreach ($self->headersList() as $_key => $_header) {
        if (stripos($_header, 'Content-Type:') === 0) {
            $content_type = $_header; // Last one.
        }
    } unset($_key, $_header); // Housekeeping.

    if (isset($content_type[0]) && stripos($content_type, 'html') === false
        && stripos($content_type, 'xml') === false && stripos($content_type, GLOBAL_NS) === false) {
        return ($is = false); // Do NOT cache data sent by scripts serving other MIME types.
    }
    return ($is = true);
};

/*
 * Does the current request have a cacheable HTTP status code?
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if the current request has a cacheable HTTP status code.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 *
 * @warning Do NOT call upon this method until the end of a script execution.
 */
$self->hasACacheableStatus = function () use ($self) {
    if (!is_null($is = &$self->staticKey('hasACacheableStatus'))) {
        return $is; // Already cached this.
    }
    if (($http_status = (string) $self->httpStatus()) && $http_status[0] !== '2' && $http_status !== '404') {
        return ($is = false); // A non-2xx & non-404 status code.
    }
    foreach ($self->headersList() as $_key => $_header) {
        if (preg_match('/^(?:Retry\-After\:\s+(?P<retry>.+)|Status\:\s+(?P<status>[0-9]+)|HTTP\/[0-9]+(?:\.[0-9]+)?\s+(?P<http_status>[0-9]+))/i', $_header, $_m)) {
            if (!empty($_m['retry']) || (!empty($_m['status']) && $_m['status'][0] !== '2' && $_m['status'] !== '404')
               || (!empty($_m['http_status']) && $_m['http_status'][0] !== '2' && $_m['http_status'] !== '404')
            ) {
                return ($is = false); // Not a cacheable status.
            }
        }
    } unset($_key, $_header); // Housekeeping.

    return ($is = true);
};

/*
 * Checks if a PHP extension is loaded up.
 *
 * @since 150422 Rewrite.
 *
 * @param string $extension A PHP extension slug (i.e. extension name).
 *
 * @return boolean `TRUE` if the extension is loaded.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isExtensionLoaded = function ($extension) use ($self) {
    $extension = (string) $extension;

    if (!is_null($is = &$self->staticKey('isExtensionLoaded', $extension))) {
        return $is; // Already cached this.
    }
    return ($is = (boolean) extension_loaded($extension));
};

/*
 * Is a particular function possible in every way?
 *
 * @since 150422 Rewrite.
 *
 * @param string $function A PHP function (or user function) to check.
 *
 * @return string `TRUE` if the function is possible.
 *
 * @note This checks (among other things) if the function exists and that it's callable.
 *    It also checks the currently configured `disable_functions` and `suhosin.executor.func.blacklist`.
 */
$self->functionIsPossible = function ($function) use ($self) {
    $function = (string) $function;

    if (!is_null($is = &$self->staticKey('functionIsPossible', $function))) {
        return $is; // Already cached this.
    }
    if (is_null($disabled_functions = &$self->staticKey('functionIsPossible_disabled_functions'))) {
        $disabled_functions = array(); // Initialize disabled/blacklisted functions.

        if (($disable_functions = trim(ini_get('disable_functions')))) {
            $disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($disable_functions), -1, PREG_SPLIT_NO_EMPTY));
        }
        if (($blacklist_functions = trim(ini_get('suhosin.executor.func.blacklist')))) {
            $disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', strtolower($blacklist_functions), -1, PREG_SPLIT_NO_EMPTY));
        }
        if(filter_var(ini_get('suhosin.executor.disable_eval'), FILTER_VALIDATE_BOOLEAN)) {
            $disabled_functions = array_merge($disabled_functions, array('eval'));
        }
    }
    if (!function_exists($function) || !is_callable($function)) {
        if(!in_array($function, $self->php_constructs, true)) { // A language construct
            return ($is = false); // Not possible.
        }
    }
    if ($disabled_functions && in_array(strtolower($function), $disabled_functions, true)) {
        return ($is = false); // Not possible.
    }
    return ($is = true);
};
