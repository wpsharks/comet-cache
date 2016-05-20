<?php
/**
 * PHP vX.x Handlers.
 *
 * @since 160503 Reorganizing structure.
 *
 * @copyright WebSharks, Inc. <http://websharks-inc.com>
 * @license GNU General Public License, version 3
 */

/**
 * Running a compatible version w/ all required extensions?
 *
 * @since 141004 First documented version.
 *
 * @return bool True if running a compatible version w/ all required extensions.
 */
function wp_php_rv()
{
    if (isset($GLOBALS['wp_php_rv'])) {
        ___wp_php_rv_initialize();
    }
    $min_version         = $GLOBALS['___wp_php_rv']['min'];
    $max_version         = $GLOBALS['___wp_php_rv']['max'];
    $minimum_bits        = $GLOBALS['___wp_php_rv']['bits'];
    $required_extensions = $GLOBALS['___wp_php_rv']['extensions'];

    if ($min_version && version_compare(PHP_VERSION, $min_version, '<')) {
        return false;
    } elseif ($max_version && version_compare(PHP_VERSION, $max_version, '>')) {
        return false;
    } elseif ($minimum_bits && $minimum_bits / 8 > PHP_INT_SIZE) {
        return false;
    } elseif ($required_extensions) {
        foreach ($required_extensions as $_required_extension) {
            if (!extension_loaded($_required_extension)) {
                return false;
            }
        } // unset($_required_extension); // Housekeeping.
    }
    return true;
}

/**
 * Initializes each instance; unsets `$GLOBALS['wp_php_rv']`.
 *
 * @since 141004 First documented version.
 *
 * @note `$GLOBALS['wp_php_rv']` is for the API, we use a different variable internally.
 *    The internal global is defined here: `$GLOBALS['___wp_php_rv']`.
 */
function ___wp_php_rv_initialize()
{
    $GLOBALS['___wp_php_rv'] = array(
        'min'        => '5.3',
        'max'        => '',
        'bits'       => 0,
        'extensions' => array(),
    );
    if (!empty($GLOBALS['wp_php_rv'])) {
        if (is_string($GLOBALS['wp_php_rv'])) {
            $GLOBALS['___wp_php_rv']['min'] = $GLOBALS['wp_php_rv'];
        } elseif (is_array($GLOBALS['wp_php_rv'])) {
            if (!empty($GLOBALS['wp_php_rv']['min'])) {
                $GLOBALS['___wp_php_rv']['min'] = (string) $GLOBALS['wp_php_rv']['min'];
            } elseif (!empty($GLOBALS['wp_php_rv']['rv'])) {
                $GLOBALS['___wp_php_rv']['min'] = (string) $GLOBALS['wp_php_rv']['rv'];
            }
            if (!empty($GLOBALS['wp_php_rv']['max'])) {
                $GLOBALS['___wp_php_rv']['max'] = (string) $GLOBALS['wp_php_rv']['max'];
            }
            if (!empty($GLOBALS['wp_php_rv']['bits'])) {
                $GLOBALS['___wp_php_rv']['bits'] = (int) $GLOBALS['wp_php_rv']['bits'];
            }
            if (!empty($GLOBALS['wp_php_rv']['extensions'])) {
                $GLOBALS['___wp_php_rv']['extensions'] = (array) $GLOBALS['wp_php_rv']['extensions'];
            } elseif (!empty($GLOBALS['wp_php_rv']['re'])) {
                $GLOBALS['___wp_php_rv']['extensions'] = (array) $GLOBALS['wp_php_rv']['re'];
            }
        }
    } // End of API conversion to internal global settings.
    unset($GLOBALS['wp_php_rv']); // Unset each time to avoid theme/plugin conflicts.
}
