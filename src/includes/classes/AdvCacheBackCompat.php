<?php
namespace WebSharks\ZenCache;

/**
 * AC back compat.
 *
 * @since 150422 Rewrite.
 */
class AdvCacheBackCompat
{
    /**
     * Back compat. with `qcAC` and `qcABC`.
     *
     * @since 150422 Rewrite.
     */
    public static function qcRequestVars()
    {
        $super_gs    = array(
            '_GET'     => &$_GET,
            '_REQUEST' => &$_REQUEST,
        );
        $qc_suffixes = array('AC', 'ABC');

        foreach ($super_gs as $_super_g_key => &$_super_g_value) {
            foreach ($qc_suffixes as $_qc_suffix) {
                if (array_key_exists('qc'.$_qc_suffix, $_super_g_value)) {
                    if ($_super_g_key === '_GET' && !isset($_GET['zc'.$_qc_suffix])) {
                        $_GET['zc'.$_qc_suffix] = $_super_g_value['qc'.$_qc_suffix];
                    }
                    foreach ($super_gs as $__super_g_key => &$__super_g_value) {
                        unset($__super_g_value['qc'.$_qc_suffix]);
                    }
                    unset($__super_g_key, $__super_g_value); // Housekeeping.
                }
            }
        }
        unset($_super_g_key, $_super_g_value, $_qc_suffix);
    }

    /**
     * Back compat. with `QUICK_CACHE_` constants.
     *
     * @since 150422 Rewrite.
     */
    public static function quickCacheConstants()
    {
        if (!($constants = get_defined_constants(true)) || empty($constants['user'])) {
            return; // Nothing to do; i.e. no user-defined constants.
        }
        foreach ($constants['user'] as $_constant => $_value) {
            if (stripos($_constant, 'QUICK_CACHE_') !== 0) {
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

        if (isset($_SERVER['QUICK_CACHE_ALLOWED']) && !isset($_SERVER[GLOBAL_NS.'_ALLOWED'])) {
            $_SERVER[GLOBAL_NS.'_ALLOWED'] = $_SERVER['QUICK_CACHE_ALLOWED'];
        }
    }
}
