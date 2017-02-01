<?php
/**
 * Uninstaller.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

use WebSharks\CometCache\Classes;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
require_once __DIR__.'/stub.php';

$GLOBALS[GLOBAL_NS.'_uninstalling'] = true; // Needs to be set before calling Conflicts class

if (!Classes\Conflicts::check()) {
    $GLOBALS[GLOBAL_NS] = new Classes\Plugin(false);
    $GLOBALS[GLOBAL_NS]->uninstall();
}
