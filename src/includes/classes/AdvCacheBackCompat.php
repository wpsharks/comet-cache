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
     * Back compat with `zc` vars.
     *
     * @since 150422 Rewrite.
     * @since 17xxxx Polishing a little.
     * @since 17xxxx Making this more dynamic.
     */
    public static function zcRequestVars()
    {
        $super_globals = [
            '_GET'     => &$_GET,
            '_REQUEST' => &$_REQUEST,
        ];
        $key_suffixes  = ['AC', 'ABC'];
        $lc_short_name = mb_strtolower(SHORT_NAME);

        foreach ($super_globals as $_key => &$_array) {
            foreach ($key_suffixes as $_suffix) {
                if (!array_key_exists('zc'.$_suffix, $_array)) {
                    continue; // No relevant key in array.
                }
                if ($_key === '_GET' && !isset($_GET[$lc_short_name.$_suffix])) {
                    $_GET[$lc_short_name.$_suffix] = $_array['zc'.$_suffix];
                } // This sets the new key with the value from the old key.

                foreach ($super_globals as $__key => &$__array) {
                    unset($__array['zc'.$_suffix]); // Purge old key.
                } // Must unset temporary vars by reference.
                unset($__key, $__array); // Housekeeping.
                //
            } // unset($_suffix); // Housekeeping.
        } // Must unset temporary vars by reference.
        unset($_key, $_array);
    }

    /**
     * Back compat. with `ZENCACHE_` constants.
     *
     * @since 150422 Rewrite.
     * @since 17xxxx Polishing a little.
     * @since 17xxxx Making this more dynamic.
     */
    public static function zenCacheConstants()
    {
        $uc_global_ns = mb_strtoupper(GLOBAL_NS);

        if (!($constants = get_defined_constants(true)) || empty($constants['user'])) {
            return; // Nothing to do; i.e. no user-defined constants.
        }
        foreach ($constants['user'] as $_constant => $_value) {
            if (mb_stripos($_constant, 'ZENCACHE_') !== 0) {
                continue; // Nothing to do here.
            } elseif (!($_constant_sub_name = mb_substr($_constant, 9))) {
                continue; // Nothing to do here.
            }
            if (!defined($uc_global_ns.'_'.$_constant_sub_name)) {
                define($uc_global_ns.'_'.$_constant_sub_name, $_value);
            } // Sets new const with the value from the old const.
        } // unset($_constant, $_value); // Just a little housekeeping.

        if (isset($_SERVER['ZENCACHE_ALLOWED']) && !isset($_SERVER[$uc_global_ns.'_ALLOWED'])) {
            $_SERVER[$uc_global_ns.'_ALLOWED'] = $_SERVER['ZENCACHE_ALLOWED'];
        } // Sets new super-global with the value from the old super-global key.
    }

    /**
     * Back compat. with constants.
     *
     * @since 160706 Renaming `*_ALLOW_BROWSER_CACHE` to `*_ALLOW_CLIENT_SIDE_CACHE`.
     * @since 17xxxx Polishing things up a little.
     * @since 17xxxx Making this more dynamic.
     */
    public static function browserCacheConstant()
    {
        $uc_global_ns = mb_strtoupper(GLOBAL_NS);

        if (defined($uc_global_ns.'_ALLOW_BROWSER_CACHE')) {
            define($uc_global_ns.'_ALLOW_CLIENT_SIDE_CACHE', constant($uc_global_ns.'_ALLOW_BROWSER_CACHE'));
        }
    }
}
