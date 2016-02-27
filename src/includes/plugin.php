<?php
/**
 * Plugin.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require_once dirname(__FILE__).'/stub.php';

if (!Conflicts::check()) {
    $GLOBALS[GLOBAL_NS]  = new Plugin();
    $GLOBALS['zencache'] = $GLOBALS[GLOBAL_NS]; // Back compat.
    $GLOBALS['quick_cache'] = $GLOBALS[GLOBAL_NS]; // Back compat.

    add_action('plugins_loaded', function() {
        require_once dirname(__FILE__).'/api.php';
    });
}
