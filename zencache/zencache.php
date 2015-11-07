<?php
/*
Version: 151107
Text Domain: zencache
Plugin Name: ZenCache
Network: true

Author: ZenCache / WebSharks, Inc.
Author URI: https://zencache.com/

Plugin URI: http://zencache.com/
Description: ZenCache is an advanced WordPress caching plugin inspired by simplicity. Speed up your site (BIG time!) with a reliable and fast WordPress cache.
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

$GLOBALS['wp_php_rv'] = '5.3.2'; // Require PHP v5.3.2+.

if(require(dirname(__FILE__).'/submodules/wp-php-rv/wp-php-rv.php')) {
    if (!empty($_REQUEST['zencache_min_php54_warning_bypass']) && is_admin()) {
        update_site_option('zencache_min_php54_warning_bypass', time());
    }
    if (!empty($_REQUEST['zencache_apc_deprecated_warning_bypass']) && is_admin()) {
        update_site_option('zencache_apc_deprecated_warning_bypass', time());
    }
    $_apc_enabled = (extension_loaded('apc') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN) && filter_var(ini_get('apc.cache_by_default'), FILTER_VALIDATE_BOOLEAN) && stripos((string)ini_get('apc.filters'), 'zencache') === false) ? true : false;
    if (!version_compare(PHP_VERSION, '5.4', '>=')) {
        if (empty($_REQUEST['zencache_min_php54_warning_bypass']) && is_admin()) {
            ${__FILE__}['php54_warning'] = '<h3 style="margin:.5em 0 .25em 0;">'.__('<strong>NOTICE: ZenCache Minimum PHP Version</strong></h3>', 'zencache');
            ${__FILE__}['php54_warning'] .= '<p style="margin-top:0;">'.sprintf(__('<strong>As of December 1st, 2015 ZenCache will require PHP 5.4 or higher.</strong> Your server is currently running PHP v%1$s. You will need to upgrade to PHP 5.4 or higher to run the next version of ZenCache.', 'zencache'), esc_html(PHP_VERSION)).'</p>';
            ${__FILE__}['php54_warning'] .= '<p style="margin-top:0;">'.__('Learn more about this upcoming change here: <a href="http://zencache.com/r/new-minimum-php-version-php-5-4/" target="_blank">New Minimum PHP Version: PHP 5.4</a>', 'zencache').'</p>';
            if ($_apc_enabled) {
                ${__FILE__}['php54_warning'] .= '<p style="margin-top:0;">'.__('Your server is also running the <strong>outdated PHP APC extension</strong>. Please see: <a href="http://zencache.com/r/php-apc-extension-no-longer-supported/" target="_blank">PHP APC Extension No Longer Supported</a>', 'zencache').'</p>';
            }
            ${__FILE__}['php54_warning'] .= '<p style="margin-bottom:.5em;">'.__('<a href="'.esc_attr(add_query_arg('zencache_min_php54_warning_bypass', '1')).'" onclick="if(!confirm(\'Are you sure? Press OK to continue, or Cancel to stop and read carefully.\')) return false;">Dismiss this notice.</a>', 'zencache').'</p>';

            add_action(
              'all_admin_notices', create_function(
                '', 'if(!current_user_can(\'activate_plugins\'))'.
                    '   return;'."\n".// User missing capability.

                    'echo \''.// Wrap `$notice` inside a WordPress error.

                    '<div class="error">'.
                    '      '.str_replace("'", "\\'", ${__FILE__}['php54_warning']).
                    '</div>'.

                    '\';'
              )
            );
        }
        unset(${__FILE__}); // Housekeeping.
    } else if (version_compare(PHP_VERSION, '5.4', '>=') && $_apc_enabled) {
        if (empty($_REQUEST['zencache_apc_deprecated_warning_bypass']) && is_admin()) {
            ${__FILE__}['apc_deprecated_warning'] = '<h3 style="margin:.5em 0 .25em 0;">'.__('<strong>NOTICE: ZenCache + PHP APC Extension</strong></h3>', 'zencache');
            ${__FILE__}['apc_deprecated_warning'] .= '<p style="margin-top:0;">'.sprintf(__('<strong>As of December 1st, 2015 ZenCache will no longer run with the outdated PHP APC extension.</strong> It appears that you\'re currently running PHP v%1$s with APC enabled. You will need to follow one of the actions below to run the next version of ZenCache.', 'zencache'), esc_html(PHP_VERSION)).'</p>';
            ${__FILE__}['apc_deprecated_warning'] .= __('<h4 style="margin:0 0 .5em 0; font-size:1.25em;"><span class="dashicons dashicons-lightbulb"></span> Options Available (Action Required):</h4>', 'zencache');
            ${__FILE__}['apc_deprecated_warning'] .= '<ul style="margin-left:2em; list-style:disc;">';
            ${__FILE__}['apc_deprecated_warning'] .= '  <li>'.__('Please add <code>ini_set(\'apc.cache_by_default\', false);</code> to the top of your <code>/wp-config.php</code> file. That will get rid of this message and allow ZenCache to run without issue.', 'zencache').'</li>';
            ${__FILE__}['apc_deprecated_warning'] .= '  <li>'.__('Or, contact your web hosting provider and ask about upgrading to PHP v5.5+; which includes the new <a href="http://zencache.com/r/php-opcache-extension/" target="_blank">OPcache extension for PHP</a>. The new OPcache extension replaces APC in modern versions of PHP.', 'zencache').'</li>';
            ${__FILE__}['apc_deprecated_warning'] .= '</ul>';
            ${__FILE__}['apc_deprecated_warning'] .= '<p style="margin-top:0;">'.__('To learn more about this upcoming change, please see the announcement: <a href="http://zencache.com/r/php-apc-extension-no-longer-supported/" target="_blank">PHP APC Extension No Longer Supported</a>', 'zencache').'</p>';
            ${__FILE__}['apc_deprecated_warning'] .= '<p style="margin-bottom:.5em;">'.__('<a href="'.esc_attr(add_query_arg('zencache_apc_deprecated_warning_bypass', '1')).'" onclick="if(!confirm(\'Are you sure? Press OK to continue, or Cancel to stop and read carefully.\')) return false;">Dismiss this notice.</a>', 'zencache').'</p>';

            add_action(
              'all_admin_notices', create_function(
                '', 'if(!current_user_can(\'activate_plugins\'))'.
                    '   return;'."\n".// User missing capability.

                    'echo \''.// Wrap `$notice` inside a WordPress error.

                    '<div class="error">'.
                    '      '.str_replace("'", "\\'", ${__FILE__}['apc_deprecated_warning']).
                    '</div>'.

                    '\';'
              )
            );
        }
        unset(${__FILE__}); // Housekeeping.
    } else {
        require_once dirname(__FILE__).'/zencache.inc.php';
    }
} else {
    wp_php_rv_notice('ZenCache');
}
