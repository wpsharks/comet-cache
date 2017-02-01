<?php
/**
 * Plugin.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

use WebSharks\CometCache\Classes;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
require_once __DIR__.'/stub.php';

if (!Classes\Conflicts::check()) {
    $GLOBALS[GLOBAL_NS]     = new Classes\Plugin();
    $GLOBALS['zencache']    = $GLOBALS[GLOBAL_NS]; // Back compat.
    $GLOBALS['quick_cache'] = $GLOBALS[GLOBAL_NS]; // Back compat.

    add_action('plugins_loaded', function () {
        require_once __DIR__.'/api.php';
    });
}
