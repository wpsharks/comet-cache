<?php
/**
 * Stub.
 *
 * @since 150422 Rewrite.
 */
namespace WebSharks\ZenCache;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';
require_once dirname(__FILE__).'/functions/i18n-utils.php';

${__FILE__}['version'] = '150605'; //version//
${__FILE__}['plugin']  = dirname(dirname(dirname(__FILE__)));
${__FILE__}['plugin'] .= '/'.basename(${__FILE__}['plugin']).'.php';
${__FILE__}['ns_path'] = str_replace('\\', '/', __NAMESPACE__); // To dir/path.
${__FILE__}['is_pro']  = strtolower(basename(${__FILE__}['ns_path'])) === 'pro';

define(__NAMESPACE__.'\\SHORT_NAME', 'ZC');
define(__NAMESPACE__.'\\NAME', 'ZenCache');
define(__NAMESPACE__.'\\DOMAIN', 'zencache.com');
define(__NAMESPACE__.'\\GLOBAL_NS', 'zencache');
define(__NAMESPACE__.'\\SLUG_TD', 'zencache');
define(__NAMESPACE__.'\\VERSION', ${__FILE__}['version']);
define(__NAMESPACE__.'\\PLUGIN_FILE', ${__FILE__}['plugin']);
define(__NAMESPACE__.'\\IS_PRO', ${__FILE__}['is_pro']);

$GLOBALS[GLOBAL_NS] = null;

unset(${__FILE__}); // Housekeeping.
