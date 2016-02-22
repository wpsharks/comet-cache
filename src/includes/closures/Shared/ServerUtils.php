<?php
namespace WebSharks\CometCache;

/*
 * Is running on Apache?
 *
 * @since 151002 This is Apache?
 *
 * @return bool True if running Apache.
 */
$self->isApache = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isApache'))) {
        return $is; // Already cached this.
    }
    if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
            return ($is = true);
        }
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Is running on Nginx?
 *
 * @since 151002 This is Nginx?
 *
 * @return bool True if running Nginx.
 */
$self->isNginx = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isNginx'))) {
        return $is; // Already cached this.
    }
    if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
            return ($is = true);
        }
    }
    return ($is = false);
};

/*
 * Is running on Windows IIS?
 *
 * @since 151002 This is Windows IIS?
 *
 * @return bool True if running Windows IIS.
 */
$self->isIis = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isIis'))) {
        return $is; // Already cached this.
    }
    if (!empty($_SERVER['SERVER_SOFTWARE']) && is_string($_SERVER['SERVER_SOFTWARE'])) {
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'microsoft-iis') !== false) {
            return ($is = true);
        }
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'expressiondevserver') !== false) {
            return ($is = true);
        }
    }
    return ($is = false);
};
