<?php
namespace WebSharks\ZenCache;

/*
 * Plugin activation hook.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to {@link \register_activation_hook()}
 */
$self->activate = function () use ($self) {
    $self->setup(); // Ensure setup is complete.

    if (!$self->options['enable']) {
        return; // Nothing to do.
    }
    $self->addWpCacheToWpConfig();
    $self->addAdvancedCache();
    $self->updateBlogPaths();
    $self->autoClearCache();
};

/*
 * Check current plugin version that installed in WP.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `admin_init` hook.
 */
$self->checkVersion = function () use ($self) {
    $prev_version    = $self->options['version'];
    $current_version = $self->options['version'];

    if (version_compare($current_version, VERSION, '>=')) {
        return; // Nothing to do; i.e., up-to-date.
    }
    $current_version = $self->options['version'] = VERSION;

    update_option(GLOBAL_NS.'_options', $self->options);
    if (is_multisite()) {
        update_site_option(GLOBAL_NS.'_options', $self->options);
    }
    new VsUpgrades($prev_version);

    if ($self->options['enable']) {
        $self->addWpCacheToWpConfig();
        $self->addAdvancedCache();
        $self->updateBlogPaths();
    }
    $self->wipeCache(); // Always wipe the cache; unless disabled by site owner; @see disableAutoWipeCacheRoutines()

    $self->enqueueNotice(sprintf(__('<strong>%1$s:</strong> detected a new version of itself. Recompiling w/ latest version... wiping the cache... all done :-)', SLUG_TD), esc_html(NAME)), '', true);
};

/*
 * Plugin deactivation hook.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to {@link \register_deactivation_hook()}
 */
$self->deactivate = function () use ($self) {
    $self->setup(); // Ensure setup is complete.

    $self->removeWpCacheFromWpConfig();
    $self->removeAdvancedCache();
    $self->clearCache();
};

/*
 * Plugin uninstall hook.
 *
 * @since 150422 Rewrite.
 */
$self->uninstall = function () use ($self) {
    $self->setup(); // Ensure setup is complete.

    if (!defined('WP_UNINSTALL_PLUGIN')) {
        return; // Disallow.
    }
    if (empty($GLOBALS[GLOBAL_NS.'_uninstalling'])) {
        return; // Not uninstalling.
    }
    if (!current_user_can($self->uninstall_cap)) {
        return; // Extra layer of security.
    }
    $self->removeWpCacheFromWpConfig();
    $self->removeAdvancedCache();
    $self->wipeCache();

    if (!$self->options['uninstall_on_deletion']) {
        return; // Nothing to do here.
    }
    $self->deleteAdvancedCache();
    $self->removeBaseDir();

    delete_option(GLOBAL_NS.'_options');
    if (is_multisite()) {
        delete_site_option(GLOBAL_NS.'_options');
    }
    delete_option(GLOBAL_NS.'_notices');
    delete_option(GLOBAL_NS.'_errors');

    wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_auto_cache');
    wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_cleanup');
};

/*
 * Adds `define('WP_CACHE', TRUE);` to the `/wp-config.php` file.
 *
 * @since 150422 Rewrite.
 *
 * @return string The new contents of the updated `/wp-config.php` file;
 *                else an empty string if unable to add the `WP_CACHE` constant.
 */
$self->addWpCacheToWpConfig = function () use ($self) {
    if (!$self->options['enable']) {
        return ''; // Nothing to do.
    }
    if (!($wp_config_file = $self->findWpConfigFile())) {
        return ''; // Unable to find `/wp-config.php`.
    }
    if (!is_readable($wp_config_file)) {
        return ''; // Not possible.
    }
    if (!($wp_config_file_contents = file_get_contents($wp_config_file))) {
        return ''; // Failure; could not read file.
    }
    if (preg_match('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[1-9][0-9\.]*|TRUE|([\'"])(?:[^0\'"]|[^\'"]{2,})\\2)\s*\)\s*;/i', $wp_config_file_contents)) {
        return $wp_config_file_contents; // It's already in there; no need to modify this file.
    }
    if (!($wp_config_file_contents = $self->removeWpCacheFromWpConfig())) {
        return ''; // Unable to remove previous value.
    }
    if (!($wp_config_file_contents = preg_replace('/^\s*(\<\?php|\<\?)\s+/i', '${1}'."\n"."define('WP_CACHE', TRUE);"."\n", $wp_config_file_contents, 1))) {
        return ''; // Failure; something went terribly wrong here.
    }
    if (strpos($wp_config_file_contents, "define('WP_CACHE', TRUE);") === false) {
        return ''; // Failure; unable to add; unexpected PHP code.
    }
    if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
        return ''; // We may NOT edit any files.
    }
    if (!is_writable($wp_config_file)) {
        return ''; // Not possible.
    }
    if (!file_put_contents($wp_config_file, $wp_config_file_contents)) {
        return ''; // Failure; could not write changes.
    }
    return $wp_config_file_contents;
};

/*
 * Removes `define('WP_CACHE', TRUE);` from the `/wp-config.php` file.
 *
 * @since 150422 Rewrite.
 *
 * @return string The new contents of the updated `/wp-config.php` file;
 *                else an empty string if unable to remove the `WP_CACHE` constant.
 */
$self->removeWpCacheFromWpConfig = function () use ($self) {
    if (!($wp_config_file = $self->findWpConfigFile())) {
        return ''; // Unable to find `/wp-config.php`.
    }
    if (!is_readable($wp_config_file)) {
        return ''; // Not possible.
    }
    if (!($wp_config_file_contents = file_get_contents($wp_config_file))) {
        return ''; // Failure; could not read file.
    }
    if (!preg_match('/([\'"])WP_CACHE\\1/i', $wp_config_file_contents)) {
        return $wp_config_file_contents; // Already gone.
    }
    if (preg_match('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:0|FALSE|NULL|([\'"])0?\\2)\s*\)\s*;/i', $wp_config_file_contents)) {
        return $wp_config_file_contents; // It's already disabled; no need to modify this file.
    }
    if (!($wp_config_file_contents = preg_replace('/define\s*\(\s*([\'"])WP_CACHE\\1\s*,\s*(?:\-?[0-9\.]+|TRUE|FALSE|NULL|([\'"])[^\'"]*\\2)\s*\)\s*;/i', '', $wp_config_file_contents))) {
        return ''; // Failure; something went terribly wrong here.
    }
    if (preg_match('/([\'"])WP_CACHE\\1/i', $wp_config_file_contents)) {
        return ''; // Failure; perhaps the `/wp-config.php` file contains syntax we cannot remove safely.
    }
    if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
        return ''; // We may NOT edit any files.
    }
    if (!is_writable($wp_config_file)) {
        return ''; // Not possible.
    }
    if (!file_put_contents($wp_config_file, $wp_config_file_contents)) {
        return ''; // Failure; could not write changes.
    }
    return $wp_config_file_contents;
};

/*
 * Checks to make sure the `zc-advanced-cache` file still exists;
 *    and if it doesn't, the `advanced-cache.php` is regenerated automatically.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `init` hook.
 *
 * @note This runs so that remote deployments which completely wipe out an
 *    existing set of website files (like the AWS Elastic Beanstalk does) will NOT cause ZenCache
 *    to stop functioning due to the lack of an `advanced-cache.php` file, which is generated by ZenCache.
 *
 *    For instance, if you have a Git repo with all of your site files; when you push those files
 *    to your website to deploy them, you most likely do NOT have the `advanced-cache.php` file.
 *    ZenCache creates this file on its own. Thus, if it's missing (and QC is active)
 *    we simply regenerate the file automatically to keep ZenCache running.
 */
$self->checkAdvancedCache = function () use ($self) {
    if (!$self->options['enable']) {
        return; // Nothing to do.
    }
    if (!empty($_REQUEST[GLOBAL_NS])) {
        return; // Skip on plugin actions.
    }
    $cache_dir           = $self->cacheDir();
    $advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

    // Fixes zero-byte advanced-cache.php bug related to migrating from Quick Cache
    //      See: <https://github.com/websharks/zencache/issues/432>

    // Also fixes a missing `define('WP_CACHE', TRUE)` bug related to migrating from Quick Cache
    //      See <https://github.com/websharks/zencache/issues/450>

    if (!is_file($cache_dir.'/zc-advanced-cache') || !is_file($advanced_cache_file) || filesize($advanced_cache_file) === 0) {
        $self->addAdvancedCache();
        $self->addWpCacheToWpConfig();
    }
};

/*
 * Creates and adds the `advanced-cache.php` file.
 *
 * @since 150422 Rewrite.
 *
 * @return bool|null `TRUE` on success. `FALSE` or `NULL` on failure.
 *                   A special `NULL` return value indicates success with a single failure
 *                   that is specifically related to the `zc-advanced-cache` file.
 */
$self->addAdvancedCache = function () use ($self) {
    if (!$self->removeAdvancedCache()) {
        return false; // Still exists.
    }
    $cache_dir               = $self->cacheDir();
    $advanced_cache_file     = WP_CONTENT_DIR.'/advanced-cache.php';
    $advanced_cache_template = dirname(dirname(dirname(__FILE__))).'/templates/advanced-cache.txt';

    if (is_file($advanced_cache_file) && !is_writable($advanced_cache_file)) {
        return false; // Not possible to create.
    }
    if (!is_file($advanced_cache_file) && !is_writable(dirname($advanced_cache_file))) {
        return false; // Not possible to create.
    }
    if (!is_file($advanced_cache_template) || !is_readable($advanced_cache_template)) {
        return false; // Template file is missing; or not readable.
    }
    if (!($advanced_cache_contents = file_get_contents($advanced_cache_template))) {
        return false; // Template file is missing; or is not readable.
    }
    $possible_advanced_cache_constant_key_values = array_merge(
        $self->options, // The following additional keys are dynamic.
        array('cache_dir' => $self->basePathTo($self->cache_sub_dir),
              
        )
    );
    foreach ($possible_advanced_cache_constant_key_values as $_option => $_value) {
        $_value = (string) $_value; // Force string.

        switch ($_option) {// Some values need tranformations.

            case 'exclude_uris': // Converts to regex (caSe insensitive).
            case 'exclude_refs': // Converts to regex (caSe insensitive).
            case 'exclude_agents': // Converts to regex (caSe insensitive).

            

                if (($_values = preg_split('/['."\r\n".']+/', $_value, null, PREG_SPLIT_NO_EMPTY))) {
                    $_value = '/(?:'.implode(
                        '|',
                        array_map(
                            function ($string) {
                                $string = preg_quote($string, '/');
                                return preg_replace('/\\\\\*/', '.*?', $string);

                            },
                            $_values
                        )
                    ).')/i';
                }
                $_value = "'".$self->escSq($_value)."'";

                break; // Break switch handler.

            

            default: // Default case handler.

                $_value = "'".$self->escSq($_value)."'";

                break; // Break switch handler.
        }
        $advanced_cache_contents = // Fill replacement codes.
            str_ireplace(
                array(
                    "'%%".GLOBAL_NS.'_'.$_option."%%'",
                    "'%%".GLOBAL_NS.'_'.preg_replace('/^cache_/i', '', $_option)."%%'",
                ),
                $_value,
                $advanced_cache_contents
            );
    }
    unset($_option, $_value, $_values, $_response); // Housekeeping.

    if (strpos(PLUGIN_FILE, WP_CONTENT_DIR) === 0) {
        $plugin_file = "WP_CONTENT_DIR.'".$self->escSq(str_replace(WP_CONTENT_DIR, '', PLUGIN_FILE))."'";
    } else {
        $plugin_file = "'".$self->escSq(PLUGIN_FILE)."'"; // Full absolute path.
    }
    // Make it possible for the `advanced-cache.php` handler to find the plugin directory reliably.
    $advanced_cache_contents = str_ireplace("'%%".GLOBAL_NS."_PLUGIN_FILE%%'", $plugin_file, $advanced_cache_contents);

    // Ignore; this is created by ZenCache; and we don't need to obey in this case.
    #if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
    #	return FALSE; // We may NOT edit any files.

    if (!file_put_contents($advanced_cache_file, $advanced_cache_contents)) {
        return false; // Failure; could not write file.
    }
    $cache_lock = $self->cacheLock(); // Lock cache.

    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0775, true);
    }
    if (is_writable($cache_dir) && !is_file($cache_dir.'/.htaccess')) {
        file_put_contents($cache_dir.'/.htaccess', $self->htaccess_deny);
    }
    if (!is_dir($cache_dir) || !is_writable($cache_dir) || !is_file($cache_dir.'/.htaccess') || !file_put_contents($cache_dir.'/zc-advanced-cache', time())) {
        $self->cacheUnlock($cache_lock); // Unlock cache.
        return; // Special return value (NULL) in this case.
    }
    $self->cacheUnlock($cache_lock);

    return true;
};

/*
 * Removes the `advanced-cache.php` file.
 *
 * @since 150422 Rewrite.
 *
 * @return bool `TRUE` on success. `FALSE` on failure.
 *
 * @note The `advanced-cache.php` file is NOT actually deleted by this routine.
 *    Instead of deleting the file, we simply empty it out so that it's `0` bytes in size.
 *
 *    The reason for this is to preserve any file permissions set by the site owner.
 *    If the site owner previously allowed this specific file to become writable, we don't want to
 *    lose that permission by deleting the file; forcing the site owner to do it all over again later.
 *
 *    An example of where this is useful is when a site owner deactivates the QC plugin,
 *    but later they decide that QC really is the most awesome plugin in the world and they turn it back on.
 */
$self->removeAdvancedCache = function () use ($self) {
    $advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

    if (!is_file($advanced_cache_file)) {
        return true; // Already gone.
    }
    if (is_readable($advanced_cache_file) && filesize($advanced_cache_file) === 0) {
        return true; // Already gone; i.e. it's empty already.
    }
    if (!is_writable($advanced_cache_file)) {
        return false; // Not possible.
    }
    // Ignore; this is created by ZenCache; and we don't need to obey in this case.
    #if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
    #	return FALSE; // We may NOT edit any files.

    /* Empty the file only. This way permissions are NOT lost in cases where
        a site owner makes this specific file writable for ZenCache. */
    if (file_put_contents($advanced_cache_file, '') !== 0) {
        return false; // Failure.
    }
    return true;
};

/*
 * Deletes the `advanced-cache.php` file.
 *
 * @since 150422 Rewrite.
 *
 * @return bool `TRUE` on success. `FALSE` on failure.
 *
 * @note The `advanced-cache.php` file is deleted by this routine.
 */
$self->deleteAdvancedCache = function () use ($self) {
    $advanced_cache_file = WP_CONTENT_DIR.'/advanced-cache.php';

    if (!is_file($advanced_cache_file)) {
        return true; // Already gone.
    }
    // Ignore; this is created by ZenCache; and we don't need to obey in this case.
    #if(defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS)
    #	return FALSE; // We may NOT edit any files.

    if (!is_writable($advanced_cache_file) || !unlink($advanced_cache_file)) {
        return false; // Not possible; or outright failure.
    }
    return true; // Deletion success.
};

/*
 * Checks to make sure the `zc-blog-paths` file still exists;
 *    and if it doesn't, the `zc-blog-paths` file is regenerated automatically.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `init` hook.
 *
 * @note This runs so that remote deployments which completely wipe out an
 *    existing set of website files (like the AWS Elastic Beanstalk does) will NOT cause ZenCache
 *    to stop functioning due to the lack of a `zc-blog-paths` file, which is generated by ZenCache.
 *
 *    For instance, if you have a Git repo with all of your site files; when you push those files
 *    to your website to deploy them, you most likely do NOT have the `zc-blog-paths` file.
 *    ZenCache creates this file on its own. Thus, if it's missing (and QC is active)
 *    we simply regenerate the file automatically to keep ZenCache running.
 */
$self->checkBlogPaths = function () use ($self) {
    if (!$self->options['enable']) {
        return; // Nothing to do.
    }
    if (!is_multisite()) {
        return; // N/A.
    }
    if (!empty($_REQUEST[GLOBAL_NS])) {
        return; // Skip on plugin actions.
    }
    $cache_dir = $self->cacheDir();

    if (!is_file($cache_dir.'/zc-blog-paths')) {
        $self->updateBlogPaths();
    }
};

/*
 * Creates and/or updates the `zc-blog-paths` file.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `enable_live_network_counts` filter.
 *
 * @param mixed $enable_live_network_counts Optional, defaults to a `NULL` value.
 *
 * @return mixed The value of `$enable_live_network_counts` (passes through).
 *
 * @note While this routine is attached to a WP filter, we also call upon it directly at times.
 */
$self->updateBlogPaths = function ($enable_live_network_counts = null) use ($self) {
    $value = // This hook actually rides on a filter.
        $enable_live_network_counts; // Filter value.

    if (!$self->options['enable']) {
        return $value; // Nothing to do.
    }
    if (!is_multisite()) {
        return $value; // N/A.
    }
    $cache_dir  = $self->cacheDir();
    $cache_lock = $self->cacheLock();

    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0775, true);
    }
    if (is_writable($cache_dir) && !is_file($cache_dir.'/.htaccess')) {
        file_put_contents($cache_dir.'/.htaccess', $self->htaccess_deny);
    }
    if (is_dir($cache_dir) && is_writable($cache_dir)) {
        $paths = // Collect child blog paths from the WordPress database.
            $self->wpdb()->get_col('SELECT `path` FROM `'.esc_sql($self->wpdb()->blogs)."` WHERE `deleted` <= '0'");

        foreach ($paths as &$_path) {
            // Strip base; these need to match `$host_dir_token`.
            $_path = '/'.ltrim(preg_replace('/^'.preg_quote($self->hostBaseToken(), '/').'/', '', $_path), '/');
        }
        unset($_path); // Housekeeping.

        file_put_contents($cache_dir.'/zc-blog-paths', serialize($paths));
    }
    $self->cacheUnlock($cache_lock); // Unlock.

    return $value; // Pass through untouched (always).
};

/*
 * Removes the entire base directory.
 *
 * @since 150422 Rewrite.
 *
 * @return int Total files removed by this routine (if any).
 */
$self->removeBaseDir = function () use ($self) {
    $counter = 0; // Initialize.

    @set_time_limit(1800); // @TODO When disabled, display a warning.

    return ($counter += $self->deleteAllFilesDirsIn($self->wpContentBaseDirTo(''), true));
};
