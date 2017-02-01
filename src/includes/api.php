<?php
/**
 * API Classes.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

use WebSharks\CometCache\Classes;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
class_alias(__NAMESPACE__.'\\Classes\\ApiBase', GLOBAL_NS);

if (!class_exists('zencache')) {
    class_alias(__NAMESPACE__.'\\Classes\\ApiBase', 'zencache');
}
