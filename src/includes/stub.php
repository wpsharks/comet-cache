<?php
/**
 * Stub.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\CometCache;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
require_once dirname(__FILE__).'/functions/i18n-utils.php';

${__FILE__}['version'] = '160223.1'; //version//
${__FILE__}['plugin']  = dirname(dirname(dirname(__FILE__)));
${__FILE__}['plugin'] .= '/'.basename(${__FILE__}['plugin']).'.php';
${__FILE__}['ns_path'] = str_replace('\\', '/', __NAMESPACE__); // To dir/path.
${__FILE__}['is_pro']  = strtolower(basename(${__FILE__}['ns_path'])) === 'pro';

define(__NAMESPACE__.'\\SHORT_NAME', 'CC');
define(__NAMESPACE__.'\\NAME', 'Comet Cache');
define(__NAMESPACE__.'\\DOMAIN', 'cometcache.com');
define(__NAMESPACE__.'\\GLOBAL_NS', 'comet_cache');
define(__NAMESPACE__.'\\SLUG_TD', 'comet-cache');
define(__NAMESPACE__.'\\VERSION', ${__FILE__}['version']);
define(__NAMESPACE__.'\\PLUGIN_FILE', ${__FILE__}['plugin']);
define(__NAMESPACE__.'\\IS_PRO', ${__FILE__}['is_pro']);

unset(${__FILE__}); // Housekeeping.

// Fixes PHP Fatal error with upgrades from v160211
class_alias(__NAMESPACE__.'\\AdvCacheBackCompat', 'WebSharks\\Comet_Cache\\AdvCacheBackCompat');
class_alias(__NAMESPACE__.'\\AdvancedCache', 'WebSharks\\Comet_Cache\\AdvancedCache');

