<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait ConditionalUtils
{
    /**
     * PHP's language constructs.
     *
     * @type array PHP's language constructs.
     * @note Keys unimportant; subject to change.
     *
     * @since 160222 First documented version.
     */
    public $php_constructs = [
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
    ];

    /**
     * Is AdvancedCache class?
     *
     * @since 150821 Improving multisite compat.
     *
     * @return bool True if this is the AdvancedCache class.
     */
    public function isAdvancedCache()
    {
        return $this instanceof Classes\AdvancedCache;
    }

    /**
     * Is Plugin class?
     *
     * @since 150821 Improving multisite compat.
     *
     * @return bool True if this is the Plugin class.
     */
    public function isPlugin()
    {
        return $this instanceof Classes\Plugin;
    }

    /**
     * `POST`, `PUT`, `DELETE`?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if `POST`, `PUT`, `DELETE`.
     */
    public function isPostPutDeleteRequest()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (!empty($_POST)) {
            return $is = true;
        } elseif (!empty($_SERVER['REQUEST_METHOD']) && in_array(mb_strtoupper($_SERVER['REQUEST_METHOD']), ['POST', 'PUT', 'DELETE'], true)) {
            return $is = true;
        }
        return $is = false;
    }

    /**
     * Is the current request method is uncacheable?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if current request method is uncacheable.
     */
    public function isUncacheableRequestMethod()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (!empty($_POST)) {
            return $is = true;
        } elseif (!empty($_SERVER['REQUEST_METHOD']) && mb_strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET') {
            return $is = true;
        }
        return $is = false;
    }

    /**
     * Does the current request include an uncacheable query string?
     *
     * @since 151002 Improving Nginx support.
     * @since 161119 Adding support for ignored GET vars.
     *
     * @return bool True if request includes an uncacheable query string.
     */
    public function requestContainsUncacheableQueryVars()
    {
        if (($contains = &$this->staticKey(__FUNCTION__)) !== null) {
            return $contains; // Already cached this.
        }
        if (!$_GET) { // No GET vars whatsoever?
            return $contains = false; // Nothing to check.
        }
        $short_name_lc = mb_strtolower(SHORT_NAME); // Needed below.

        if (isset($_GET[$short_name_lc.'AC']) && filter_var($_GET[$short_name_lc.'AC'], FILTER_VALIDATE_BOOLEAN)) {
            return $contains = false; // `?ccAC` allows caching.
        }
        return $contains = $this->filterQueryVars($_GET) ? true : false;
    }

    /**
     * Should the current user should be considered a logged-in user?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if current user should be considered a logged-in user.
     */
    public function isLikeUserLoggedIn()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (defined('SID') && SID) {
            return $is = true;
        } elseif (empty($_COOKIE)) {
            return $is = false;
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
            if (!is_scalar($_value)) {
                continue; // See https://git.io/v1dTw
            }

            $_key   = (string) $_key;
            $_value = (string) $_value;

            if (isset($_value[0]) && preg_match($regex_logged_in_cookies, $_key)) {
                return $is = true; // Like a logged-in user.
            }
        } // unset($_key, $_value); // Housekeeping.

        return $is = false;
    }

    /**
     * Are we in a LOCALHOST environment?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if we are in a LOCALHOST environment.
     */
    public function isLocalhost()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (defined('LOCALHOST')) {
            return $is = (bool) LOCALHOST;
        } elseif (preg_match('/\b(?:localhost|127\.0\.0\.1)\b/ui', $this->hostToken())) {
            return $is = true;
        }
        return $is = false;
    }

    

    /**
     * Is the current request for a feed?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if the current request is for a feed.
     */
    public function isFeed()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (isset($_REQUEST['feed'])) {
            return $is = true;
        } elseif (!empty($_SERVER['REQUEST_URI']) && preg_match('/\/feed(?:[\/?]|$)/', $_SERVER['REQUEST_URI'])) {
            return $is = true;
        }
        return $is = false;
    }

    /**
     * Is a document/string an HTML/XML doc; or no?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @param string $doc Input string/document to check.
     *
     * @return bool True if `$doc` is an HTML/XML doc type.
     */
    public function isHtmlXmlDoc($doc)
    {
        $doc      = trim((string) $doc);
        $doc_hash = sha1($doc);

        if (($is = &$this->staticKey(__FUNCTION__, $doc_hash)) !== null) {
            return $is; // Already cached this.
        }
        if (mb_stripos($doc, '</html>') !== false || mb_stripos($doc, '<?xml') === 0) {
            return $is = true;
        }
        return $is = false;
    }

    /**
     * Does the current request have a cacheable content type?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if the current request has a cacheable content type.
     *
     * @warning Do NOT call upon this method until the end of a script execution.
     */
    public function hasACacheableContentType()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        foreach ($this->headersList() as $_key => $_header) {
            if (mb_stripos($_header, 'content-type:') === 0) {
                $content_type = $_header;
            } // Use last content-type header.
        } // unset($_key, $_header); // Housekeeping.

        if (isset($content_type[0]) && mb_stripos($content_type, 'html') === false
                && mb_stripos($content_type, 'xml') === false && mb_stripos($content_type, GLOBAL_NS) === false) {
            return $is = false; // Do NOT cache data sent by scripts serving other MIME types.
        }
        return $is = true;
    }

    /**
     * Does the current request have a cacheable HTTP status code?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @return bool True if the current request has a cacheable HTTP status code.
     *
     * @warning Do NOT call upon this method until the end of a script execution.
     */
    public function hasACacheableStatus()
    {
        if (($is = &$this->staticKey(__FUNCTION__)) !== null) {
            return $is; // Already cached this.
        }
        if (($http_status = (string) $this->httpStatus()) && $http_status[0] !== '2' && $http_status !== '404') {
            return $is = false; // A non-2xx & non-404 status code.
        }
        foreach ($this->headersList() as $_key => $_header) {
            if (!preg_match('/^(?:Retry\-After\:\s+(?P<retry>.+)|Status\:\s+(?P<status>[0-9]+)|HTTP\/[0-9]+(?:\.[0-9]+)?\s+(?P<http_status>[0-9]+))/ui', $_header, $_m)) {
                continue; // Not applicable; i.e., Not a header that is worth checking here.
            } elseif (!empty($_m['retry']) || (!empty($_m['status']) && $_m['status'][0] !== '2' && $_m['status'] !== '404')
                    || (!empty($_m['http_status']) && $_m['http_status'][0] !== '2' && $_m['http_status'] !== '404')) {
                return $is = false; // Not a cacheable status.
            }
        } // unset($_key, $_header, $_m); // Housekeeping.

        return $is = true;
    }

    /**
     * Checks if a PHP extension is loaded up.
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @param string $extension A PHP extension slug (i.e. extension name).
     *
     * @return bool True if the extension is loaded.
     */
    public function isExtensionLoaded($extension)
    {
        $extension = (string) $extension;

        if (($is = &$this->staticKey(__FUNCTION__, $extension)) !== null) {
            return $is; // Already cached this.
        }
        return $is = (bool) extension_loaded($extension);
    }

    /**
     * Is a particular function possible in every way?
     *
     * @since 150422 Rewrite.
     * @since 161119 Enhancements.
     *
     * @param string $function A PHP function (or user function) to check.
     *
     * @return string True if the function is possible.
     */
    public function functionIsPossible($function)
    {
        $function = mb_strtolower((string) $function);

        if (($is = &$this->staticKey(__FUNCTION__, $function)) !== null) {
            return $is; // Already cached this.
        }
        if (($disabled_functions = &$this->staticKey(__FUNCTION__.'_disabled_functions')) === null) {
            $disabled_functions = []; // Initialize disabled/blacklisted functions.

            if (($disable_functions = trim(ini_get('disable_functions')))) {
                $disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', mb_strtolower($disable_functions), -1, PREG_SPLIT_NO_EMPTY));
            }
            if (($blacklist_functions = trim(ini_get('suhosin.executor.func.blacklist')))) {
                $disabled_functions = array_merge($disabled_functions, preg_split('/[\s;,]+/', mb_strtolower($blacklist_functions), -1, PREG_SPLIT_NO_EMPTY));
            }
            if (($opcache_restrict_api = trim(ini_get('opcache.restrict_api'))) && mb_stripos(__FILE__, $opcache_restrict_api) !== 0) {
                $disabled_functions = array_merge($disabled_functions, ['opcache_compile_file', 'opcache_get_configuration', 'opcache_get_status', 'opcache_invalidate', 'opcache_is_script_cached', 'opcache_reset']);
            }
            if (filter_var(ini_get('suhosin.executor.disable_eval'), FILTER_VALIDATE_BOOLEAN)) {
                $disabled_functions = array_merge($disabled_functions, ['eval']);
            }
        }
        if ($disabled_functions && in_array($function, $disabled_functions, true)) {
            return $is = false; // Not possible.
        } elseif ((!function_exists($function) || !is_callable($function)) && !in_array($function, $this->php_constructs, true)) {
            return $is = false; // Not possible.
        }
        return $is = true;
    }
}
