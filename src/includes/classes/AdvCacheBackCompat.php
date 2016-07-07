<?php
namespace WebSharks\CometCache\Classes;

/**
 * AC back compat.
 *
 * @since 150422 Rewrite.
 */
class AdvCacheBackCompat
{
    /**
     * Back compat. with `zcAC` and `zcABC`.
     *
     * @since 150422 Rewrite.
     */
    public static function zcRequestVars()
    {
        $super_gs = [
            '_GET'     => &$_GET,
            '_REQUEST' => &$_REQUEST,
        ];
        $zc_suffixes = ['AC', 'ABC'];

        foreach ($super_gs as $_super_g_key => &$_super_g_value) {
            foreach ($zc_suffixes as $_zc_suffix) {
                if (array_key_exists('zc'.$_zc_suffix, $_super_g_value)) {
                    if ($_super_g_key === '_GET' && !isset($_GET['cc'.$_zc_suffix])) {
                        $_GET['cc'.$_zc_suffix] = $_super_g_value['zc'.$_zc_suffix];
                    }
                    foreach ($super_gs as $__super_g_key => &$__super_g_value) {
                        unset($__super_g_value['zc'.$_zc_suffix]);
                    }
                    unset($__super_g_key, $__super_g_value); // Housekeeping.
                }
            }
        }
        unset($_super_g_key, $_super_g_value, $_zc_suffix);
    }

    /**
     * Back compat. with `ZENCACHE_` constants.
     *
     * @since 150422 Rewrite.
     */
    public static function zenCacheConstants()
    {
        $_global_ns = mb_strtoupper(GLOBAL_NS);

        if (!($constants = get_defined_constants(true)) || empty($constants['user'])) {
            return; // Nothing to do; i.e. no user-defined constants.
        }
        foreach ($constants['user'] as $_constant => $_value) {
            if (mb_stripos($_constant, 'ZENCACHE_') !== 0) {
                continue; // Nothing to do here.
            }
            if (!($_constant_sub_name = mb_substr($_constant, 9))) {
                continue; // Nothing to do here.
            }
            if (!defined($_global_ns.'_'.$_constant_sub_name)) {
                define($_global_ns.'_'.$_constant_sub_name, $_value);
            }
        }
        if (isset($_SERVER['ZENCACHE_ALLOWED']) && !isset($_SERVER[$_global_ns.'_ALLOWED'])) {
            $_SERVER[$_global_ns.'_ALLOWED'] = $_SERVER['ZENCACHE_ALLOWED'];
        }

        unset($_constant, $_value, $_global_ns); // Housekeeping.
    }

    /**
     * Back compat. with `COMET_CACHE_ALLOW_BROWSER_CACHE` constants.
     *
     * @since 160706 Renaming COMET_CACHE_ALLOW_BROWSER_CACHE to COMET_CACHE_ALLOW_CLIENT_SIDE_CACHE
     */
    public static function browserCacheConstant()
    {
        $_global_ns = mb_strtoupper(GLOBAL_NS);

        if (defined('COMET_CACHE_ALLOW_BROWSER_CACHE')) {
            define($_global_ns.'_ALLOW_CLIENT_SIDE_CACHE', COMET_CACHE_ALLOW_BROWSER_CACHE);
        }
    }
}
