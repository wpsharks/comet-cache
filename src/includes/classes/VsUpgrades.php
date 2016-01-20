<?php
namespace WebSharks\ZenCache;

/**
 * Version-Specific Upgrades.
 *
 * @since 150422 Rewrite.
 */
class VsUpgrades extends AbsBase
{
    /**
     * @type string Version they are upgrading from.
     *
     * @since 150422 Rewrite.
     */
    protected $prev_version = '';

    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     *
     * @param string $prev_version Version they are upgrading from.
     */
    public function __construct($prev_version)
    {
        parent::__construct();

        $this->prev_version = (string) $prev_version;
        $this->runHandlers(); // Run upgrade(s).
    }

    /**
     * Runs upgrade handlers in the proper order.
     *
     * @since 150422 Rewrite.
     */
    protected function runHandlers()
    {
        $this->fromLt110523();
        $this->fromLt140104();
        $this->fromLt140605();
        $this->fromLt140612();
        $this->fromLt141001();
        $this->fromLt141009();
        $this->fromQuickCache();
        $this->fromLte150807();
        $this->fromLte151107();
        $this->fromLte151114();
    }

    /**
     * Before a total rewrite.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt110523()
    {
        if (version_compare($this->prev_version, '110523', '<')) {
            delete_site_option('ws_plugin__qcache_notices');
            delete_site_option('ws_plugin__qcache_options');
            delete_site_option('ws_plugin__qcache_configured');

            if (is_multisite()) { // Main site CRON jobs.
                switch_to_blog(get_current_site()->blog_id);
                wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule');
                wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule');
                restore_current_blog(); // Restore.
            } else {
                wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule');
                wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule');
            }
            $this->plugin->enqueueMainNotice(sprintf(__('<strong>%1$s:</strong> this version is a <strong>complete rewrite</strong> of Quick Cache :-) Please review your %1$s options carefully!', 'zencache'), esc_html(NAME)));
        }
    }

    /**
     * Before we introduced feed caching.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140104()
    {
        if (version_compare($this->prev_version, '140104', '<')) {
            $this->plugin->enqueueMainNotice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s adds new options for Feed caching. Feed caching is now disabled by default. If you wish to enable feed caching, please visit the %1$s options panel.', 'zencache'), esc_html(NAME)));
        }
    }

    /**
     * Before we introduced a branched cache structure.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140605()
    {
        if (version_compare($this->prev_version, '140605', '<')) {
            if (is_array($existing_options = get_site_option(GLOBAL_NS.'_options')) || is_array($existing_options = get_site_option('quick_cache_options'))) {
                if (!empty($existing_options['cache_dir'])) { // Old options have a `cache_dir` key which is no longer used in current version?
                    if (($existing_options['cache_dir'] = trim($existing_options['cache_dir'], '\\/'." \t\n\r\0\x0B"))) {
                        $this->plugin->deleteAllFilesDirsIn(ABSPATH.$existing_options['cache_dir'], true);
                    }
                    $this->plugin->options['base_dir'] = $existing_options['cache_dir']; // Use old directory.
                    $wp_content_dir_relative           = trim(str_replace(ABSPATH, '', WP_CONTENT_DIR), '\\/'." \t\n\r\0\x0B");
                    if (!$this->plugin->options['base_dir'] || $this->plugin->options['base_dir'] === $wp_content_dir_relative.'/cache') {
                        $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir']; // Just use the default base.
                    }
                    $this->plugin->updateOptions($this->plugin->options); // Save/update options.
                    $this->plugin->activate(); // Reactivate plugin w/ new options.
                }
                $this->plugin->enqueueMainNotice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s introduces a new <a href="http://zencache.com/r/kb-branched-cache-structure/" target="_blank">Branched Cache Structure</a> and several other <a href="http://www.websharks-inc.com/post/quick-cache-v140605-now-available/" target="_blank">new features</a>.', 'zencache'), esc_html(NAME)));
            }
        }
    }

    /**
     * Before we changed base directory from `ABSPATH` to `WP_CONTENT_DIR`.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140612()
    {
        if (version_compare($this->prev_version, '140612', '<')) {
            if (is_array($existing_options = get_site_option(GLOBAL_NS.'_options')) || is_array($existing_options = get_site_option('quick_cache_options'))) {
                if (!empty($existing_options['base_dir']) && stripos($existing_options['base_dir'], basename(WP_CONTENT_DIR)) !== false) {
                    $this->plugin->deleteAllFilesDirsIn(ABSPATH.$existing_options['base_dir'], true);

                    $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];
                    $this->plugin->updateOptions($this->plugin->options); // Save/update options.
                    $this->plugin->activate(); // Reactivate plugin w/ new options.

                    $this->plugin->enqueueMainNotice(
                        '<p>'.sprintf(__('<strong>%1$s Notice:</strong> This version of %1$s changes the default base directory that it uses, from <code>ABSPATH</code> to <code>WP_CONTENT_DIR</code>. This is for improved compatibility with installations that choose to use a custom <code>WP_CONTENT_DIR</code> location.', 'zencache'), esc_html(NAME)).
                        ' '.sprintf(__('%1$s has detected that your previously configured cache directory may have been in conflict with this change. As a result, your %1$s configuration has been updated to the new default value; just to keep things running smoothly for you :-). If you would like to review this change, please see: <code>Dashboard ⥱ %1$s ⥱ Directory &amp; Expiration Time</code>; where you may customize it further if necessary.', 'zencache'), esc_html(NAME)).'</p>'
                    );
                }
            }
        }
    }

    /**
     * Before we removed the WordPress version number from the Auto-Cache Engine User-Agent string.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt141001()
    {
        if (version_compare($this->prev_version, '141001', '<')) {
            $this->plugin->options['auto_cache_user_agent'] = $this->plugin->default_options['auto_cache_user_agent'];
            $this->plugin->updateOptions($this->plugin->options); // Save/update options.
        }
    }

    /**
     * Before we changed several `cache_purge_*` options to `cache_clear_*`.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt141009()
    {
        if (version_compare($this->prev_version, '141009', '<')) {
            if (is_array($existing_options = get_site_option(GLOBAL_NS.'_options')) || is_array($existing_options = get_site_option('quick_cache_options'))) {
                foreach (array('cache_purge_xml_feeds_enable',
                              'cache_purge_xml_sitemaps_enable',
                              'cache_purge_xml_sitemap_patterns',
                              'cache_purge_home_page_enable',
                              'cache_purge_posts_page_enable',
                              'cache_purge_custom_post_type_enable',
                              'cache_purge_author_page_enable',
                              'cache_purge_term_category_enable',
                              'cache_purge_term_post_tag_enable',
                              'cache_purge_term_other_enable',
                        ) as $_old_purge_option) {
                    if (isset($existing_options[$_old_purge_option][0])) {
                        $this->plugin->options[str_replace('purge', 'clear', $_old_purge_option)] = $existing_options[$_old_purge_option][0];
                    }
                } // ↑ Converts `purge` to `clear`.
                unset($_old_purge_option); // A little housekeeping.

                $this->plugin->updateOptions($this->plugin->options); // Save/update options.
            }
        }
    }

    /**
     * Before we changed the name to ZenCache.
     *
     * If so, we need to uninstall and deactivate Quick Cache.
     *
     * @since 150422 Rewrite.
     */
    protected function fromQuickCache()
    {
        if (is_array($quick_cache_options = get_site_option('quick_cache_options'))) {
            delete_site_option('quick_cache_errors');
            delete_site_option('quick_cache_notices');
            delete_site_option('quick_cache_options');

            if (is_multisite()) { // Main site CRON jobs.
                switch_to_blog(get_current_site()->blog_id);
                wp_clear_scheduled_hook('_cron_quick_cache_auto_cache');
                wp_clear_scheduled_hook('_cron_quick_cache_cleanup');
                restore_current_blog(); // Restore.
            } else {
                wp_clear_scheduled_hook('_cron_quick_cache_auto_cache');
                wp_clear_scheduled_hook('_cron_quick_cache_cleanup');
            }
            deactivate_plugins(array('quick-cache/quick-cache.php', 'quick-cache-pro/quick-cache-pro.php'), true);

            if (isset($quick_cache_options['update_sync_version_check'])) {
                $this->plugin->options['pro_update_check'] = $quick_cache_options['update_sync_version_check'];
            }
            if (isset($quick_cache_options['last_update_sync_version_check'])) {
                $this->plugin->options['last_pro_update_check'] = $quick_cache_options['last_update_sync_version_check'];
            }
            if (isset($quick_cache_options['update_sync_username'])) {
                $this->plugin->options['pro_update_username'] = $quick_cache_options['update_sync_username'];
            }
            if (isset($quick_cache_options['update_sync_password'])) {
                $this->plugin->options['pro_update_password'] = $quick_cache_options['update_sync_password'];
            }
            if (!empty($quick_cache_options['base_dir'])) {
                $this->plugin->deleteAllFilesDirsIn(WP_CONTENT_DIR.'/'.trim($quick_cache_options['base_dir'], '/'), true);
            }
            $this->plugin->deleteBaseDir(); // Let's be extra sure that the old base directory is gone.

            $this->plugin->options['base_dir']    = $this->plugin->default_options['base_dir'];
            $this->plugin->options['crons_setup'] = $this->plugin->default_options['crons_setup'];

            $this->plugin->updateOptions($this->plugin->options); // Save/update options.
            $this->plugin->activate(); // Reactivate plugin w/ new options.

            $this->plugin->enqueueMainNotice(
                '<p>'.sprintf(__('<strong>Woohoo! %1$s activated.</strong> :-)', 'zencache'), esc_html(NAME)).'</p>'.
                '<p>'.sprintf(__('NOTE: Your Quick Cache options were preserved by %1$s (for more details, visit the <a href="%2$s" target="_blank">Migration FAQ</a>).'.'', 'zencache'), esc_html(NAME), esc_attr(IS_PRO ? 'http://zencache.com/r/quick-cache-pro-migration-faq/' : 'http://zencache.com/kb-article/how-to-migrate-from-quick-cache-lite-to-zencache-lite/')).'</p>'.
                '<p>'.sprintf(__('To review your configuration, please see: <a href="%2$s">%1$s ⥱ Plugin Options</a>.'.'', 'zencache'), esc_html(NAME), esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS)), self_admin_url('/admin.php')))).'</p>'
            );
        }
    }

    /**
     * Before we changed errors and blog-specific storage on a MS network.
     *
     * @since 151002 Improving multisite compat.
     */
    protected function fromLte150807()
    {
        if (version_compare($this->prev_version, '150807', '<=')) {
            delete_site_option(GLOBAL_NS.'_errors'); // No longer necessary.

            if (is_multisite() && is_array($child_blogs = wp_get_sites())) {
                $current_site = get_current_site(); // Current site.
                foreach ($child_blogs as $_child_blog) {
                    switch_to_blog($_child_blog['blog_id']);

                    delete_option(GLOBAL_NS.'_errors');
                    delete_option(GLOBAL_NS.'_notices');
                    delete_option(GLOBAL_NS.'_options');
                    delete_option(GLOBAL_NS.'_apc_warning_bypass');

                    if ((integer) $_child_blog['blog_id'] !== (integer) $current_site->blog_id) {
                        wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_auto_cache');
                        wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_cleanup');
                    }
                    restore_current_blog(); // Restore current blog.
                }
                unset($_child_blog); // Housekeeping.
            }
            if (is_array($existing_options = get_site_option(GLOBAL_NS.'_options'))) {
                if (isset($existing_options['admin_bar_enable'])) {
                    $this->plugin->options['cache_clear_admin_bar_enable'] = $existing_options['admin_bar_enable'];
                    $this->plugin->updateOptions($this->plugin->options); // Save/update options.
                }
            }
        }
    }

    /**
     * Before we changed the CDN Blacklisted Extensions and implemented htaccess tweaks to fix CORS errors.
     *  Also, before we changed the watered-down regex syntax for exclusion patterns.
     *
     * @since 151114 Adding `.htaccess` tweaks.
     */
    protected function fromLte151107()
    {
        if (version_compare($this->prev_version, '151107', '<=')) {
            if (is_array($existing_options = get_site_option(GLOBAL_NS.'_options'))) {
                if (!empty($existing_options['cache_clear_xml_sitemap_patterns']) && strpos($existing_options['cache_clear_xml_sitemap_patterns'], '**') === false) {
                    $this->plugin->options['cache_clear_xml_sitemap_patterns'] = str_replace('*', '**', $existing_options['cache_clear_xml_sitemap_patterns']);
                }
                if (!empty($existing_options['exclude_uris']) && strpos($existing_options['exclude_uris'], '**') === false) {
                    $this->plugin->options['exclude_uris'] = str_replace('*', '**', $existing_options['exclude_uris']);
                }
                if (!empty($existing_options['exclude_refs']) && strpos($existing_options['exclude_refs'], '**') === false) {
                    $this->plugin->options['exclude_refs'] = str_replace('*', '**', $existing_options['exclude_refs']);
                }
                if (!empty($existing_options['exclude_agents']) && strpos($existing_options['exclude_agents'], '**') === false) {
                    $this->plugin->options['exclude_agents'] = str_replace('*', '**', $existing_options['exclude_agents']);
                }
                if (!empty($existing_options['htmlc_css_exclusions']) && strpos($existing_options['htmlc_css_exclusions'], '**') === false) {
                    $this->plugin->options['htmlc_css_exclusions'] = str_replace('*', '**', $existing_options['htmlc_css_exclusions']);
                }
                if (!empty($existing_options['htmlc_js_exclusions']) && strpos($existing_options['htmlc_js_exclusions'], '**') === false) {
                    $this->plugin->options['htmlc_js_exclusions'] = str_replace('*', '**', $existing_options['htmlc_js_exclusions']);
                }
                if ($existing_options['cdn_blacklisted_extensions'] === 'eot,ttf,otf,woff') {
                    // See: <https://github.com/websharks/zencache/issues/427#issuecomment-121777790>
                    $this->plugin->options['cdn_blacklisted_extensions'] = $this->plugin->default_options['cdn_blacklisted_extensions'];
                }
                if ($this->plugin->options !== $existing_options) {
                    $this->plugin->updateOptions($this->plugin->options); // Save/update options.
                    $this->plugin->activate(); // Reactivate plugin w/ new options.
                }
            }
        }
    }

    /**
     * Before we changed the htaccess comment blocks to contain a unique identifier.
     *
     * @since 151220 Improving `.htaccess` tweaks.
     */
    protected function fromLte151114()
    {
        if (version_compare($this->prev_version, '151114', '<=')) {
            global $is_apache;

            if (!$is_apache) {
                return; // Not running the Apache web server.
            }
            if (!($htaccess_file = $this->plugin->findHtaccessFile())) {
                return; // File does not exist.
            }
            if (!$this->plugin->findHtaccessMarker('ZenCache')) {
                return; // Template blocks are already gone.
            }
            if ($htaccess = $this->plugin->readHtaccessFile($htaccess_file)) {
                if (is_dir($templates_dir = dirname(dirname(__FILE__)).'/templates/htaccess/back-compat')) {
                    $htaccess['file_contents'] = str_replace(file_get_contents($templates_dir.'/v151114.txt'), '', $htaccess['file_contents']);
                    $htaccess['file_contents'] = str_replace(file_get_contents($templates_dir.'/v151114-2.txt'), '', $htaccess['file_contents']);
                    $htaccess['file_contents'] = trim($htaccess['file_contents']);

                    if (!$this->plugin->writeHtaccessFile($htaccess, false)) {
                        return; // Failure; could not write changes.
                    }
                }
            }
        }
    }
}
