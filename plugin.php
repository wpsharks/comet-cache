<?php
if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
$GLOBALS['wp_php_rv']['rv'] = '5.3.2'; //php-required-version// // Leaving this at v5.3.2 so that we can have more control over Dashboard messages below.
$GLOBALS['wp_php_rv']['re'] = array('mbstring');

if (require(dirname(__FILE__).'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    if (!empty($_REQUEST['comet_cache_mbstring_deprecated_warning_bypass']) && is_admin()) {
        update_site_option('comet_cache_mbstring_deprecated_warning_bypass', time());
    }

    ${__FILE__}['apc_enabled'] = (extension_loaded('apc') && filter_var(ini_get('apc.enabled'), FILTER_VALIDATE_BOOLEAN) && filter_var(ini_get('apc.cache_by_default'), FILTER_VALIDATE_BOOLEAN) && mb_stripos((string) ini_get('apc.filters'), 'comet-cache') === false) ? true : false;

    if ((!version_compare(PHP_VERSION, '5.4', '>=') || ${__FILE__}['apc_enabled'])) { // If PHP <= 5.4 or APC is enabled

        if (!version_compare(PHP_VERSION, '5.4', '>=') && is_admin()) {
            ${__FILE__}['php54_notice'] = '<h3 style="margin:.5em 0 .25em 0;">'.__('<strong>NOTICE: Comet Cache Minimum PHP Version</strong></h3>', 'comet-cache');
            ${__FILE__}['php54_notice'] .= '<p style="margin-top:0;">'.sprintf(__('<strong>As of December 1st, 2015 Comet Cache requires PHP 5.4 or higher.</strong> Your server is currently running PHP v%1$s. You will need to upgrade to PHP 5.4 or higher to run this version of Comet Cache.', 'comet-cache'), esc_html(PHP_VERSION)).'</p>';
            ${__FILE__}['php54_notice'] .= '<p style="margin-top:0;">'.__('Learn more about this change here: <a href="http://cometcache.com/r/new-minimum-php-version-php-5-4/" target="_blank">New Minimum PHP Version: PHP 5.4</a>', 'comet-cache').'</p>';
            if (${__FILE__}['apc_enabled']) {
                ${__FILE__}['php54_notice'] .= '<p style="margin-top:0;">'.__('Your server is also running the <strong>outdated PHP APC extension</strong>. Please see: <a href="http://cometcache.com/r/php-apc-extension-no-longer-supported/" target="_blank">PHP APC Extension No Longer Supported</a>', 'comet-cache').'</p>';
            }

            add_action(
                'all_admin_notices',
                create_function(
                    '',
                    'if(!current_user_can(\'activate_plugins\'))'.
                    '   return;'."\n".// User missing capability.

                    'echo \''.// Wrap `$notice` inside a WordPress error.

                    '<div class="error">'.
                    '      '.str_replace("'", "\\'", ${__FILE__}['php54_notice']).
                    '</div>'.

                    '\';'
                )
            );
        } elseif (${__FILE__}['apc_enabled'] && is_admin()) {
            ${__FILE__}['apc_deprecated_notice'] = '<h3 style="margin:.5em 0 .25em 0;">'.__('<strong>NOTICE: Comet Cache + PHP APC Extension</strong></h3>', 'comet-cache');
            ${__FILE__}['apc_deprecated_notice'] .= '<p style="margin-top:0;">'.sprintf(__('<strong>As of December 1st, 2015 Comet Cache no longer runs with the outdated PHP APC extension.</strong> It appears that you\'re currently running PHP v%1$s with APC enabled. You will need to follow one of the actions below to run this version of Comet Cache.', 'comet-cache'), esc_html(PHP_VERSION)).'</p>';
            ${__FILE__}['apc_deprecated_notice'] .= __('<h4 style="margin:0 0 .5em 0; font-size:1.25em;"><span class="dashicons dashicons-lightbulb"></span> Options Available (Action Required):</h4>', 'comet-cache');
            ${__FILE__}['apc_deprecated_notice'] .= '<ul style="margin-left:2em; list-style:disc;">';
            ${__FILE__}['apc_deprecated_notice'] .= '  <li>'.__('Please add <code>ini_set(\'apc.cache_by_default\', false);</code> to the top of your <code>/wp-config.php</code> file. That will get rid of this message and allow Comet Cache to run without issue.', 'comet-cache').'</li>';
            ${__FILE__}['apc_deprecated_notice'] .= '  <li>'.__('Or, contact your web hosting provider and ask about upgrading to PHP v5.5+; which includes the new <a href="http://cometcache.com/r/php-opcache-extension/" target="_blank">OPcache extension for PHP</a>. The new OPcache extension replaces APC in modern versions of PHP.', 'comet-cache').'</li>';
            ${__FILE__}['apc_deprecated_notice'] .= '</ul>';
            ${__FILE__}['apc_deprecated_notice'] .= '<p style="margin-top:0;">'.__('To learn more about this change, please see the announcement: <a href="http://cometcache.com/r/php-apc-extension-no-longer-supported/" target="_blank">PHP APC Extension No Longer Supported</a>', 'comet-cache').'</p>';

            add_action(
                'all_admin_notices',
                create_function(
                    '',
                    'if(!current_user_can(\'activate_plugins\'))'.
                    '   return;'."\n".// User missing capability.

                    'echo \''.// Wrap `$notice` inside a WordPress error.

                    '<div class="error">'.
                    '      '.str_replace("'", "\\'", ${__FILE__}['apc_deprecated_notice']).
                    '</div>'.

                    '\';'
                )
            );
        }
    } else { // Load the plugin

        if (!extension_loaded('mbstring') && !get_site_option('comet_cache_mbstring_deprecated_warning_bypass') && is_admin()) {
            ${__FILE__}['mbstring_deprecated_warning'] = '<h3 style="margin:.5em 0 .25em 0;">'.__('<strong>NOTICE: Comet Cache Will Require the PHP <code>mbstring</code> Extension</strong></h3>', 'comet-cache');
            ${__FILE__}['mbstring_deprecated_warning'] .= '<p style="margin-top:0;">'.sprintf(__('<strong>After March 1st, 2016 Comet Cache will require PHP Multibyte String support.</strong> It appears that your site is currently running PHP v%1$s <strong>without</strong> the <code>mbstring</code> extension enabled. You will need to contact your web hosting company and have them enable the PHP <code>mbstring</code> extension if you want to run the next version of Comet Cache.', 'comet-cache'), esc_html(PHP_VERSION)).'</p>';
            ${__FILE__}['mbstring_deprecated_warning'] .= '<p style="margin-top:0;">'.__('The <code>mbstring</code> extension provides Multibyte String support to PHP and is required to properly handle UTF-8 characters, which many sites now use. Without Multibyte String support, Comet Cache will be unstable. For that reason we are requiring the <code>mbstring</code> extension to improve reliablity when caching and to prevent your site from experiencing unforeseen issues in the future.', 'comet-cache').'</p>';
            ${__FILE__}['mbstring_deprecated_warning'] .= '<p style="margin-bottom:.5em;">'.__('<a href="'.esc_attr(add_query_arg('comet_cache_mbstring_deprecated_warning_bypass', '1')).'" onclick="if(!confirm(\'Are you sure? Press OK to continue, or Cancel to stop and read carefully.\')) return false;">Dismiss this notice.</a>', 'comet-cache').'</p>';
            add_action(
                'all_admin_notices',
                create_function(
                    '',
                    'if(!current_user_can(\'activate_plugins\'))'.
                        '   return;'."\n".// User missing capability.
                        'echo \''.// Wrap `$notice` inside a WordPress error.
                        '<div class="notice notice-warning">'.
                        '      '.str_replace("'", "\\'", ${__FILE__}['mbstring_deprecated_warning']).
                        '</div>'.
                        '\';'
                )
            );
        }

        require_once dirname(__FILE__).'/src/includes/plugin.php';
    }
} else {
    wp_php_rv_notice('Comet Cache');
}

unset(${__FILE__}); // Housekeeping.
