<?php
namespace WebSharks\CometCache\Classes;

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
        $this->fromLte150807();
        $this->fromLte151107();
        $this->fromLte151114();
        $this->fromZenCache();
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
            if (!$this->plugin->findHtaccessMarker('Comet Cache')) {
                return; // Template blocks are already gone.
            }
            if ($htaccess = $this->plugin->readHtaccessFile($htaccess_file)) {
                if (is_dir($templates_dir = dirname(__DIR__).'/templates/htaccess/back-compat')) {
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

    /**
     * Before we changed the name to Comet Cache.
     *
     * If so, we need to uninstall and deactivate ZenCache.
     *
     * @since 160223 Rebranding.
     */
    protected function fromZenCache()
    {
        if (is_array($zencache_options = get_site_option('zencache_options'))) {
            delete_site_option('zencache_errors');
            delete_site_option('zencache_notices');
            delete_site_option('zencache_options');

            if (is_multisite()) { // Main site CRON jobs.
                switch_to_blog(get_current_site()->blog_id);
                wp_clear_scheduled_hook('_cron_zencache_auto_cache');
                wp_clear_scheduled_hook('_cron_zencache_cleanup');
                restore_current_blog(); // Restore.
            } else {
                wp_clear_scheduled_hook('_cron_zencache_auto_cache');
                wp_clear_scheduled_hook('_cron_zencache_cleanup');
            }
            deactivate_plugins(['zencache/zencache.php', 'zencache-pro/zencache-pro.php'], true);

            if (!empty($zencache_options['base_dir'])) {
                $this->plugin->deleteAllFilesDirsIn(WP_CONTENT_DIR.'/'.trim($zencache_options['base_dir'], '/'), true);
            }
            $this->plugin->deleteBaseDir(); // Let's be extra sure that the old base directory is gone.

            global $is_apache; // Remove htaccess rules added by ZenCache so that they can be re-added by Comet Cache
            if ($is_apache && $this->plugin->findHtaccessMarker('WmVuQ2FjaGU') && ($htaccess = $this->plugin->readHtaccessFile())) {
                $regex                     = '/#\s*BEGIN\s+ZenCache\s+WmVuQ2FjaGU.*?#\s*END\s+ZenCache\s+WmVuQ2FjaGU\s*/is';
                $htaccess['file_contents'] = preg_replace($regex, '', $htaccess['file_contents']);

                $this->plugin->writeHtaccessFile($htaccess, false);
            }

            $this->plugin->options['base_dir']    = $this->plugin->default_options['base_dir'];
            $this->plugin->options['crons_setup'] = $this->plugin->default_options['crons_setup'];

            $this->plugin->updateOptions($this->plugin->options); // Save/update options.
            $this->plugin->activate(); // Reactivate plugin w/ new options.

            $this->plugin->enqueueMainNotice(
                '<p>'.sprintf(__('<strong>Woohoo! %1$s activated.</strong> :-)', 'comet-cache'), esc_html(NAME)).'</p>'.
                '<p>'.sprintf(__('NOTE: Your ZenCache options were preserved by %1$s (for more details, visit the <a href="%2$s" target="_blank">Migration FAQ</a>).'.'', 'comet-cache'), esc_html(NAME), esc_attr(IS_PRO ? 'http://cometcache.com/r/zencache-pro-migration-faq/' : 'https://cometcache.com/r/zencache-migration-faq/')).'</p>'.
                '<p>'.sprintf(__('To review your configuration, please see: <a href="%2$s">%1$s → Plugin Options</a>.'.'', 'comet-cache'), esc_html(NAME), esc_attr(add_query_arg(urlencode_deep(['page' => GLOBAL_NS]), self_admin_url('/admin.php')))).'</p>'
            );
        }
    }
}
