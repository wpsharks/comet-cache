<?php
use WebSharks\CometCache as Plugin;

/**
 * Postload event handler; overrides core WP function.
 *
 * @since 140422 First documented version.
 *
 * @note See `/wp-settings.php` around line #226.
 */
function wp_cache_postload()
{
    $GLOBAL_NS      = Plugin\GLOBAL_NS;
    $advanced_cache = $GLOBALS[$GLOBAL_NS.'_advanced_cache'];

    if (!$advanced_cache->is_running) {
        return; // Not applicable.
    }
    $advanced_cache->doWpAction('before_'.$GLOBAL_NS.'_'.__FUNCTION__, get_defined_vars());

    
    if (!empty($advanced_cache->postload['filter_status_header'])) {
        $advanced_cache->maybeFilterStatusHeaderPostload();
    }
    if (!empty($advanced_cache->postload['set_debug_info'])) {
        $advanced_cache->maybeSetDebugInfoPostload();
    }
    if (!empty($advanced_cache->postload['wp_main_query'])) {
        add_action('wp', [$advanced_cache, 'wpMainQueryPostload'], PHP_INT_MAX);
    }
    $advanced_cache->doWpAction('after_'.$GLOBAL_NS.'_'.__FUNCTION__, get_defined_vars());
    $advanced_cache->doWpAction($GLOBAL_NS.'_'.__FUNCTION__.'_complete', get_defined_vars());
}
