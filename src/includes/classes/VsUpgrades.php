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
        $this->fromLte150218();
    }

    /**
     * Upgrading from a version prior to our rewrite.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt110523()
    {
        if (version_compare($this->prev_version, '110523', '<')) {
            delete_option('ws_plugin__qcache_options'); // Ditch these.
            delete_option('ws_plugin__qcache_notices'); // Ditch these.
            delete_option('ws_plugin__qcache_configured'); // Ditch this too.

            wp_clear_scheduled_hook('ws_plugin__qcache_garbage_collector__schedule'); // Ditch old CRON job.
            wp_clear_scheduled_hook('ws_plugin__qcache_auto_cache_engine__schedule'); // Ditch old CRON job.

            $this->plugin->enqueueNotice(sprintf(__('<strong>%1$s:</strong> this version is a <strong>complete rewrite</strong> of Quick Cache :-) Please review your %1$s options carefully!', SLUG_TD), esc_html(NAME)));
        }
    }

    /**
     * Upgrading from a version prior to v140104 where we introduced feed caching.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140104()
    {
        if (version_compare($this->prev_version, '140104', '<')) {
            $this->plugin->enqueueNotice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s adds new options for Feed caching. Feed caching is now disabled by default. If you wish to enable feed caching, please visit the %1$s options panel.', SLUG_TD), esc_html(NAME)));
        }
    }

    /**
     * Upgrading from a version prior to v140605, where we introduced a branched cache structure.
     *
     * See <https://github.com/websharks/zencache/issues/147#issuecomment-42659131>
     *    We also also moved to a base directory layout.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140605()
    {
        if (version_compare($this->prev_version, '140605', '<')) {
            if ((is_multisite() && is_array($existing_options = get_site_option(GLOBAL_NS.'_options')))
                || is_array($existing_options = get_option(GLOBAL_NS.'_options'))
                || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
                || is_array($existing_options = get_option('quick_cache_options'))
            ) {
                if (!empty($existing_options['cache_dir'])) {
                    $wp_content_dir_relative = // We considered custom locations.
                        trim(str_replace(ABSPATH, '', WP_CONTENT_DIR), '\\/'." \t\n\r\0\x0B");

                    $this->plugin->options['base_dir'] = $existing_options['cache_dir'] = trim($existing_options['cache_dir'], '\\/'." \t\n\r\0\x0B");

                    if (!$this->plugin->options['base_dir'] || $this->plugin->options['base_dir'] === $wp_content_dir_relative.'/cache') {
                        $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];
                    }
                    if ($existing_options['cache_dir']) {
                        $this->plugin->wipeCache(false, ABSPATH.$existing_options['cache_dir']);
                    }
                    unset($this->plugin->options['cache_dir']); // Just to be sure.

                    update_option(GLOBAL_NS.'_options', $this->plugin->options);
                    if (is_multisite()) {
                        update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
                    }
                    $this->plugin->activate(); // Reactivate plugin w/ new options.
                }
                $this->plugin->enqueueNotice(sprintf(__('<strong>%1$s Feature Notice:</strong> This version of %1$s introduces a new <a href="http://zencache.com/r/kb-branched-cache-structure/" target="_blank">Branched Cache Structure</a> and several other <a href="http://www.websharks-inc.com/post/quick-cache-v140605-now-available/" target="_blank">new features</a>.', SLUG_TD), esc_html(NAME)));
            }
        }
    }

    /**
     * Upgrading from a version before we changed base directory from `ABSPATH` to `WP_CONTENT_DIR`.
     *
     * If so, we need to reset the cache location on sites
     * that have `wp-content` in their base directory.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt140612()
    {
        if (version_compare($this->prev_version, '140612', '<')) {
            if ((is_multisite() && is_array($existing_options = get_site_option(GLOBAL_NS.'_options')))
               || is_array($existing_options = get_option(GLOBAL_NS.'_options'))
               || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
               || is_array($existing_options = get_option('quick_cache_options'))
            ) {
                if (!empty($existing_options['base_dir']) && stripos($existing_options['base_dir'], basename(WP_CONTENT_DIR)) !== false) {
                    $this->plugin->wipeCache(false, ABSPATH.$existing_options['base_dir']);
                    $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];

                    update_option(GLOBAL_NS.'_options', $this->plugin->options);
                    if (is_multisite()) {
                        update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
                    }
                    $this->plugin->activate(); // Reactivate plugin w/ new options.

                    $this->plugin->enqueueNotice(
                        '<p>'.sprintf(__('<strong>%1$s Notice:</strong> This version of %1$s changes the default base directory that it uses, from <code>ABSPATH</code> to <code>WP_CONTENT_DIR</code>. This is for improved compatibility with installations that choose to use a custom <code>WP_CONTENT_DIR</code> location.', SLUG_TD), esc_html(NAME)).
                        ' '.sprintf(__('%1$s has detected that your previously configured cache directory may have been in conflict with this change. As a result, your %1$s configuration has been updated to the new default value; just to keep things running smoothly for you :-). If you would like to review this change, please see: <code>Dashboard ⥱ %1$s ⥱ Directory &amp; Expiration Time</code>; where you may customize it further if necessary.', SLUG_TD), esc_html(NAME)).'</p>'
                    );
                }
            }
        }
    }

    /**
     * Upgrading from a version before we removed the WordPress version number from the Auto-Cache Engine User-Agent string.
     *
     * If so, we need to update the User-Agent string.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt141001()
    {
        if (version_compare($this->prev_version, '141001', '<')) {
            $this->plugin->options['auto_cache_user_agent'] = $this->plugin->default_options['auto_cache_user_agent'];

            update_option(GLOBAL_NS.'_options', $this->plugin->options);
            if (is_multisite()) {
                update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
            }
        }
    }

    /**
     * Upgrading from a version before we changed several `cache_purge_*` options to `cache_clear_*`.
     *
     * If so, we need to use the existing options to fill the new keys.
     * And, of course, then we save the updated options.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLt141009()
    {
        if (version_compare($this->prev_version, '141009', '<')) {
            if ((is_multisite() && is_array($existing_options = get_site_option(GLOBAL_NS.'_options')))
               || is_array($existing_options = get_option(GLOBAL_NS.'_options'))
               || (is_multisite() && is_array($existing_options = get_site_option('quick_cache_options')))
               || is_array($existing_options = get_option('quick_cache_options'))
            ) {
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
                        $found_old_purge_options                                                  = true;
                        $this->plugin->options[str_replace('purge', 'clear', $_old_purge_option)] = $existing_options[$_old_purge_option][0];
                    }
                }
                unset($_old_purge_option); // Housekeeping.

                if (!empty($found_old_purge_options)) {
                    update_option(GLOBAL_NS.'_options', $this->plugin->options);
                    if (is_multisite()) {
                        update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
                    }
                }
            }
        }
    }

    /**
     * Upgrading from a version before we changed the name to ZenCache.
     *
     * If so, we need to uninstall and deactivate Quick Cache.
     *
     * @since 150422 Rewrite.
     */
    protected function fromQuickCache()
    {
        if ((is_multisite() && is_array($quick_cache_options = get_site_option('quick_cache_options')))
           || is_array($quick_cache_options = get_option('quick_cache_options'))
        ) {
            delete_option('quick_cache_options');
            if (is_multisite()) {
                delete_site_option('quick_cache_options');
            }
            delete_option('quick_cache_notices');
            delete_option('quick_cache_errors');

            wp_clear_scheduled_hook('_cron_quick_cache_auto_cache');
            wp_clear_scheduled_hook('_cron_quick_cache_cleanup');

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
            $this->plugin->removeBaseDir(); // Let's be extra sure that the old base directory is gone.

            $this->plugin->options['base_dir']    = $this->plugin->default_options['base_dir'];
            $this->plugin->options['crons_setup'] = $this->plugin->default_options['crons_setup'];

            update_option(GLOBAL_NS.'_options', $this->plugin->options);
            if (is_multisite()) {
                update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
            }
            $this->plugin->activate(); // Reactivate plugin w/ new options.

            $this->plugin->enqueueNotice(
                '<p>'.sprintf(__('<strong>Woohoo! %1$s activated.</strong> :-)', SLUG_TD), esc_html(NAME)).'</p>'.
                '<p>'.sprintf(__('NOTE: Your Quick Cache options were preserved by %1$s (for more details, visit the <a href="%2$s" target="_blank">Migration FAQ</a>).'.'', SLUG_TD), esc_html(NAME), esc_attr(IS_PRO ? 'http://zencache.com/r/quick-cache-pro-migration-faq/' : 'http://zencache.com/kb-article/how-to-migrate-from-quick-cache-lite-to-zencache-lite/')).'</p>'.
                '<p>'.sprintf(__('To review your configuration, please see: <a href="%2$s">%1$s ⥱ Plugin Options</a>.'.'', SLUG_TD), esc_html(NAME), esc_attr(add_query_arg(urlencode_deep(array('page' => GLOBAL_NS)), self_admin_url('/admin.php')))).'</p>'
            );
        }
    }

    /**
     * Upgrading from a version before we changed the CDN defaults for whitelist/blacklist.
     *
     * If so, we need to update the default CDN blacklisted extensions.
     *
     * @since 150422 Rewrite.
     */
    protected function fromLte150218()
    {
        if (version_compare($this->prev_version, '150218', '<=')) {
            if (!$this->plugin->options['cdn_whitelisted_extensions']) {
                $blacklisted_extensions = $this->plugin->options['cdn_blacklisted_extensions'];
                $blacklisted_extensions = trim(strtolower($blacklisted_extensions), "\r\n\t\0\x0B".' |;,');
                $blacklisted_extensions = preg_split('/[|;,\s]+/', $blacklisted_extensions, null, PREG_SPLIT_NO_EMPTY);

                $default_blacklisted_extensions = $this->plugin->default_options['cdn_blacklisted_extensions'];
                $default_blacklisted_extensions = trim(strtolower($default_blacklisted_extensions), "\r\n\t\0\x0B".' |;,');
                $default_blacklisted_extensions = preg_split('/[|;,\s]+/', $default_blacklisted_extensions, null, PREG_SPLIT_NO_EMPTY);

                $this->plugin->options['cdn_blacklisted_extensions'] = implode(',', array_unique(array_merge($blacklisted_extensions, $default_blacklisted_extensions)));

                update_option(GLOBAL_NS.'_options', $this->plugin->options);
                if (is_multisite()) {
                    update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
                }
            }
        }
    }
}
