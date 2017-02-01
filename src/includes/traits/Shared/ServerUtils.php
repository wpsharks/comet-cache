<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait ServerUtils
{
    /**
     * Is running on Apache?
     *
     * @since 151002 This is Apache?
     *
     * @return bool True if running Apache.
     */
    public function isApache()
    {
        if (!is_null($is = &$this->staticKey(__FUNCTION__))) {
            return $is; // Already cached this.
        }
        if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
            if (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
                return $is = true;
            } elseif (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false) {
                return $is = true;
            }
        } // Checking `SERVER_SOFTWARE` is faster.

        if ($this->functionIsPossible('apache_get_version')) {
            return $is = true;
        }
        return $is = false;
    }

    /**
     * Is running on Nginx?
     *
     * @since 151002 This is Nginx?
     *
     * @return bool True if running Nginx.
     */
    public function isNginx()
    {
        if (!is_null($is = &$this->staticKey(__FUNCTION__))) {
            return $is; // Already cached this.
        }
        if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
            if (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
                return $is = true;
            }
        } // Checking `SERVER_SOFTWARE` is faster.

        if (!empty($_SERVER['WP_NGINX_CONFIG'])) {
            return $is = true; // See: <http://jas.xyz/2jnfXOF>
        }
        return $is = false;
    }

    /**
     * Is running on Windows IIS?
     *
     * @since 151002 This is Windows IIS?
     *
     * @return bool True if running Windows IIS.
     */
    public function isIis()
    {
        if (!is_null($is = &$this->staticKey(__FUNCTION__))) {
            return $is; // Already cached this.
        }
        if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
            if (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'microsoft-iis') !== false) {
                return $is = true;
            } elseif (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'expressiondevserver') !== false) {
                return $is = true;
            }
        } // Checking `SERVER_SOFTWARE` is faster.

        return $is = false;
    }
}
