<?php
// @codingStandardsIgnoreFile
/**
 * Stub.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
require_once dirname(__DIR__).'/vendor/autoload.php';
require_once __DIR__.'/functions/i18n-utils.php';

${__FILE__}['version'] = '170220'; //version//
${__FILE__}['plugin']  = dirname(dirname(__DIR__));
${__FILE__}['plugin'] .= '/'.basename(${__FILE__}['plugin']).'.php';
${__FILE__}['ns_path'] = str_replace('\\', '/', __NAMESPACE__); // To dir/path.
${__FILE__}['is_pro']  = mb_strtolower(basename(${__FILE__}['ns_path'])) === 'pro';

define(__NAMESPACE__.'\\SHORT_NAME', 'CC');
define(__NAMESPACE__.'\\NAME', 'Comet Cache');
define(__NAMESPACE__.'\\DOMAIN', 'cometcache.com');
define(__NAMESPACE__.'\\GLOBAL_NS', 'comet_cache');
define(__NAMESPACE__.'\\SLUG_TD', 'comet-cache');
define(__NAMESPACE__.'\\VERSION', ${__FILE__}['version']);
define(__NAMESPACE__.'\\PLUGIN_FILE', ${__FILE__}['plugin']);
define(__NAMESPACE__.'\\IS_PRO', ${__FILE__}['is_pro']);

foreach (['Classes', 'Traits\\Shared', 'Traits\\Ac', 'Traits\\Plugin', 'Interfaces\\Shared'] as ${__FILE__}['_sub_namespace']) {
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\SHORT_NAME', SHORT_NAME);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\NAME', NAME);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\DOMAIN', DOMAIN);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\GLOBAL_NS', GLOBAL_NS);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\SLUG_TD', 'comet-cache');
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\VERSION', VERSION);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\PLUGIN_FILE', PLUGIN_FILE);
    define(__NAMESPACE__.'\\'.${__FILE__}['_sub_namespace'].'\\IS_PRO', IS_PRO);
}
unset(${__FILE__}); // Housekeeping.

// Fixes PHP Fatal error with upgrades from v160211
class_alias(__NAMESPACE__.'\\Classes\\AdvCacheBackCompat', 'WebSharks\\Comet_Cache\\AdvCacheBackCompat');
class_alias(__NAMESPACE__.'\\Classes\\AdvancedCache', 'WebSharks\\Comet_Cache\\AdvancedCache');


// Fixes PHP Fatal error with upgrades from v160227
class_alias(__NAMESPACE__.'\\Classes\\AdvCacheBackCompat', 'WebSharks\\CometCache\\AdvCacheBackCompat');
class_alias(__NAMESPACE__.'\\Classes\\AdvancedCache', 'WebSharks\\CometCache\\AdvancedCache');

