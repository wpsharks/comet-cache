<?php
/**
 * PHP vX.x Handlers.
 *
 * @since 141004 First documented version.
 *
 * @copyright WebSharks, Inc. <http://www.websharks-inc.com>
 * @license GNU General Public License, version 3
 */

/**
 * This server is running PHP vX.x+ w/ required extensions?
 *
 * @return bool `TRUE` if running PHP vX.x+ w/ required extensions.
 */
function wp_php_rv()
{
    if (isset($GLOBALS['wp_php_rv'])) {
        ___wp_php_rv_initialize();
    }
    if (!version_compare(PHP_VERSION, $GLOBALS['___wp_php_rv']['rv'], '>=')) {
        return false;
    }
    foreach ($GLOBALS['___wp_php_rv']['re'] as $_extension) {
        if ($_extension && is_string($_extension) && !extension_loaded($_extension)) {
            return false; // Missing extension!
        }
    }
    unset($_extension); // Housekeeping.

    return true; // Good-to-go!
}

/**
 * What's missing; i.e., version and/or extensions?
 *
 * @return array Missing required version and/or extensions.
 *               An empty array if nothing is missing.
 */
function wp_php_rv_missing()
{
    if (isset($GLOBALS['wp_php_rv'])) {
        ___wp_php_rv_initialize();
    }
    $missing_rv = // Missing required PHP version?
        !version_compare(PHP_VERSION, $GLOBALS['___wp_php_rv']['rv'], '>=');
    $missing_rv = $missing_rv ? $GLOBALS['___wp_php_rv']['rv'] : '';

    $missing_re      = array(); // Missing extensions.
    $missing_re_list = ''; // Initialize default value.

    foreach ($GLOBALS['___wp_php_rv']['re'] as $_extension) {
        if ($_extension && is_string($_extension) && !extension_loaded($_extension)) {
            $missing_re[] = $_extension; // Missing extension!
        }
    }
    unset($_extension); // Housekeeping.

    if ($missing_re) {
        foreach ($missing_re as $_re) {
            $missing_re_list .= '<code><a href="http://php.net/manual/en/book.'.urlencode($_re).'.php" target="_blank">'.esc_html($_re).'</a></code>, ';
        }
        $missing_re_list = trim($missing_re_list, ', ');
        unset($_re); // Housekeeping.
    }
    if ($missing_rv || $missing_re) {
        return array('rv' => $missing_rv, 're' => $missing_re, 're_list' => $missing_re_list);
    }
    return array(); // Nothing missing.
}

/**
 * Creates a WP Dashboard notice regarding PHP requirements.
 *
 * @param string $software_name        Optional. Name of the calling theme/plugin.
 * @param string $software_text_domain Optional i18n text domain. Defaults to slugified `$software_name`.
 * @param string $notice_cap           Optional. Capability to view notice. Defaults to `activate_plugins`.
 * @param string $notice_action        Optional. Action hook. Defaults to `all_admin_notices`.
 * @param string $notice               Optional. Custom notice HTML instead of default markup.
 */
function wp_php_rv_notice($software_name = '', $software_text_domain = '', $notice_cap = '', $notice_action = '', $notice = '')
{
    if (isset($GLOBALS['wp_php_rv'])) {
        ___wp_php_rv_initialize();
    }
    if (!($missing = wp_php_rv_missing()) || (!$missing['rv'] && !$missing['re'])) {
        return; // Not missing anything. Stop here.
    }
    $software_name        = trim((string) $software_name);
    $software_text_domain = trim((string) $software_text_domain);
    $notice_cap           = trim((string) $notice_cap);
    $notice_action        = trim((string) $notice_action);
    $notice               = trim((string) $notice);

    if (!$notice_cap) {
        $notice_cap = 'activate_plugins';
    }
    if (!$notice_action) {
        $notice_action = 'all_admin_notices';
    }
    if (!$software_name) {
        $software_name = 'This Software'; // Default generic value.
        // Let's try to do better! We can use the basedir of the calling file.
        if (($_debug_backtrace = @debug_backtrace()) && !empty($_debug_backtrace[1]['file'])) {
            if (($_calling_file_basedir = strtolower(basename(dirname($_debug_backtrace[1]['file']))))) {
                $software_name = ucwords(trim(preg_replace('/[^a-z0-9]+/i', ' ', $_calling_file_basedir)));
            }
        }
        unset($_debug_backtrace, $_calling_file_basedir); // Housekeeping.
    }
    if (!$software_text_domain) {
        $software_text_domain = trim(preg_replace('/[^a-z0-9\-]/i', '-', strtolower($software_name)), '-');
    }
    if (!$notice) {
        // Use the default notice? This will amost always suffice.
        $extensions_i18n = _n('extension', 'extensions', count($missing['re']), $software_text_domain); // Singular|plural?
        $php_icon_markup = '<a href="http://php.net/" target="_blank" title="PHP.net">'.// PHP icon; linked up with the manual @ PHP.net.
                           '<img src="//cdn.websharks-inc.com/media/images/php-icon.png" style="width:60px; float:left; margin:0 10px 0 0;" alt="PHP" />'.
                           '</a>'; // PHP icon served from the WebSharks™ CDN. Supports both `http://` and `https://`.

        if ($missing['rv']) {
            $notice = $php_icon_markup; // Start with the floated PHP icon markup.
            $notice .= sprintf(__('<strong>%1$s is NOT active. It requires PHP v%2$s+.</strong>', $software_text_domain), esc_html($software_name), esc_html($missing['rv'])).'<br />';
            $notice .= sprintf(__('&#8627; You\'re currently running an older copy of PHP v%1$s.', $software_text_domain), esc_html(PHP_VERSION)).'<br />';

            if ($missing['re'] && $missing['re_list']) {
                $notice .= sprintf(__('&#8627; You are also missing the following required PHP %1$s: %2$s.', $software_text_domain), esc_html($extensions_i18n), $missing['re_list']).'<br />';
            }
            $notice .= __('<em>A simple update is necessary. Please ask your hosting company to help resolve this quickly.</em>', $software_text_domain).'<br />';
            $notice .= sprintf(__('<em>To remove this message, please upgrade PHP. Or, remove %1$s from WordPress.</em>', $software_text_domain), esc_html($software_name));
        } elseif ($missing['re'] && $missing['re_list']) {
            $notice = $php_icon_markup; // Start with the floated PHP icon markup.
            $notice .= sprintf(__('<strong>%1$s is NOT active. PHP %2$s missing.</strong>', $software_text_domain), esc_html($software_name), esc_html($extensions_i18n)).'<br />';
            $notice .= sprintf(__('&#8627; You are missing the following required PHP %1$s: %2$s.', $software_text_domain), esc_html($extensions_i18n), $missing['re_list']).'<br />';
            $notice .= __('<em>A simple update is necessary. Please ask your hosting company to help resolve this quickly.</em>', $software_text_domain).'<br />';
            $notice .= sprintf(__('<em>To remove this message, please install the required PHP %1$s. Or, remove %2$s from WordPress.</em>', $software_text_domain), esc_html($extensions_i18n), esc_html($software_name));
        }
    }
    if ($notice_action && $notice) {
        // Only if there is a notice obviously; i.e., don't show an empty error messsage.
        add_action($notice_action, create_function('', 'if(!current_user_can(\''.str_replace("'", "\\'", $notice_cap).'\'))'.
                                                       '   return;'."\n".// User missing capability.

                                                       'echo \''.// Wrap `$notice` inside a WordPress error.

                                                       '<div class="error">'.
                                                       '   <p>'.
                                                       '      '.str_replace("'", "\\'", $notice).
                                                       '   </p>'.
                                                       '</div>'.

                                                       '\';'));
    }
}

/**
 * Creates a WP Dashboard notice regarding PHP requirements.
 *
 * @param string $notice        Optional. Custom notice HTML instead of default markup.
 * @param string $notice_cap    Optional. Capability to view notice. Defaults to `activate_plugins`.
 * @param string $notice_action Optional. Action hook. Defaults to `all_admin_notices`.
 */
function wp_php_rv_custom_notice($notice = '', $notice_cap = '', $notice_action = '')
{
    if (isset($GLOBALS['wp_php_rv'])) {
        ___wp_php_rv_initialize();
    }
    wp_php_rv_notice('', '', $notice_cap, $notice_action, $notice);
}

/**
 * Initializes each instance; unsets `$GLOBALS['wp_php_rv']`.
 *
 * @note `$GLOBALS['wp_php_rv']` is for the API, we use a different variable internally.
 *    The internal global is defined here: `$GLOBALS['___wp_php_rv']`.
 */
function ___wp_php_rv_initialize() // For internal use only.
{
    /*
     * Internal array of PHP requirements.
     *
     * @var array Internal array of PHP requirements.
     *    NOTE: this is for internal use only.
     */
    $GLOBALS['___wp_php_rv'] = array('rv' => '5.3', 're' => array());

    if (!empty($GLOBALS['wp_php_rv']) && is_string($GLOBALS['wp_php_rv'])) {
        /*
         * Required PHP version (public API).
         * @var string Required PHP version; via string.
         */
        $GLOBALS['___wp_php_rv']['rv'] = $GLOBALS['wp_php_rv'];
    } elseif (!empty($GLOBALS['wp_php_rv']) && is_array($GLOBALS['wp_php_rv'])) {
        if (!empty($GLOBALS['wp_php_rv']['rv']) && is_string($GLOBALS['wp_php_rv']['rv'])) {
            /*
             * Required PHP version (public API).
             * @var string Required PHP version; via array.
             */
            $GLOBALS['___wp_php_rv']['rv'] = $GLOBALS['wp_php_rv']['rv'];
        }
        if (!empty($GLOBALS['wp_php_rv']['re']) && is_array($GLOBALS['wp_php_rv']['re'])) {
            /*
             * Required PHP extension(s) (public API).
             * @var string Required PHP extension(s); via array.
             */
            $GLOBALS['___wp_php_rv']['re'] = $GLOBALS['wp_php_rv']['re'];
        }
    }
    unset($GLOBALS['wp_php_rv']); // Unset each time to avoid theme/plugin conflicts.
}
