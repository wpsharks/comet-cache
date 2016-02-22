<?php
namespace WebSharks\CometCache;

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
        $super_gs    = array(
            '_GET'     => &$_GET,
            '_REQUEST' => &$_REQUEST,
        );
        $zc_suffixes = array('AC', 'ABC');

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
        if (!($constants = get_defined_constants(true)) || empty($constants['user'])) {
            return; // Nothing to do; i.e. no user-defined constants.
        }
        foreach ($constants['user'] as $_constant => $_value) {
            if (stripos($_constant, 'ZENCACHE_') !== 0) {
                continue; // Nothing to do here.
            }
            if (!($_constant_sub_name = substr($_constant, 12))) {
                continue; // Nothing to do here.
            }
            if (!defined(GLOBAL_NS.'_'.$_constant_sub_name)) {
                define(GLOBAL_NS.'_'.$_constant_sub_name, $_value);
            }
        }
        unset($_constant, $_value); // Housekeeping.

        if (isset($_SERVER['ZENCACHE_ALLOWED']) && !isset($_SERVER[GLOBAL_NS.'_ALLOWED'])) {
            $_SERVER[GLOBAL_NS.'_ALLOWED'] = $_SERVER['ZENCACHE_ALLOWED'];
        }
    }
}
