<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait AcPluginUtils
{
    /**
     * Loads any advanced cache plugin files found inside `/wp-content/ac-plugins`.
     *
     * @since 150422 Rewrite.
     */
    public function loadAcPlugins()
    {
        if (!is_dir(WP_CONTENT_DIR.'/ac-plugins')) {
            return; // Nothing to do here.
        }
        $GLOBALS[GLOBAL_NS.'_advanced_cache']  = $this; // Self reference.
        $GLOBALS[GLOBAL_NS.'__advanced_cache'] = &$GLOBALS[GLOBAL_NS.'_advanced_cache'];
        if (!isset($GLOBALS['zencache__advanced_cache'])) {
            $GLOBALS['zencache_advanced_cache']  = &$GLOBALS[GLOBAL_NS.'_advanced_cache'];
            $GLOBALS['zencache__advanced_cache'] = &$GLOBALS[GLOBAL_NS.'_advanced_cache'];
        }
        foreach ((array) glob(WP_CONTENT_DIR.'/ac-plugins/*.php') as $_ac_plugin) {
            if (is_file($_ac_plugin)) {
                include_once $_ac_plugin;
            }
        }
        unset($_ac_plugin); // Houskeeping.
    }
}
