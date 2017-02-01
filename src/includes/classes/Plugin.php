<?php
namespace WebSharks\CometCache\Classes;

use WebSharks\CometCache\Classes;
use WebSharks\CometCache\Traits;

/**
 * Comet Cache Plugin.
 *
 * @since 150422 Rewrite.
 */
class Plugin extends AbsBaseAp
{
    /*[.build.php-auto-generate-use-Traits]*/
    use Traits\Plugin\ActionUtils;
    use Traits\Plugin\AdminBarUtils;
    use Traits\Plugin\BbPressUtils;
    use Traits\Plugin\CleanupUtils;
    use Traits\Plugin\CondUtils;
    use Traits\Plugin\CronUtils;
    use Traits\Plugin\DbUtils;
    use Traits\Plugin\DirUtils;
    use Traits\Plugin\HtaccessUtils;
    use Traits\Plugin\InstallUtils;
    use Traits\Plugin\MenuPageUtils;
    use Traits\Plugin\NoticeUtils;
    use Traits\Plugin\OptionUtils;
    use Traits\Plugin\PostUtils;
    use Traits\Plugin\UrlUtils;
    use Traits\Plugin\UserUtils;
    use Traits\Plugin\WcpAuthorUtils;
    use Traits\Plugin\WcpCommentUtils;
    use Traits\Plugin\WcpDateArchiveUtils;
    use Traits\Plugin\WcpFeedUtils;
    use Traits\Plugin\WcpHomeBlogUtils;
    use Traits\Plugin\WcpJetpackUtils;
    use Traits\Plugin\WcpOpcacheUtils;
    use Traits\Plugin\WcpPluginUtils;
    use Traits\Plugin\WcpPostTypeUtils;
    use Traits\Plugin\WcpPostUtils;
    use Traits\Plugin\WcpSettingUtils;
    use Traits\Plugin\WcpSitemapUtils;
    use Traits\Plugin\WcpTermUtils;
    use Traits\Plugin\WcpUpdaterUtils;
    use Traits\Plugin\WcpUtils;
    use Traits\Plugin\WcpWooCommerceUtils;
    /*[/.build.php-auto-generate-use-Traits]*/

    /**
     * Enable plugin hooks?
     *
     * @since 150422 Rewrite.
     *
     * @type bool If `FALSE`, run without hooks.
     */
    public $enable_hooks = true;

    /**
     * Pro-only option keys.
     *
     * @since 150422 Rewrite.
     *
     * @type array Pro-only option keys.
     */
    public $pro_only_option_keys = [];

    /**
     * Default options.
     *
     * @since 150422 Rewrite.
     *
     * @type array Default options.
     */
    public $default_options = [];

    /**
     * Configured options.
     *
     * @since 150422 Rewrite.
     *
     * @type array Configured options.
     */
    public $options = [];

    /**
     * WordPress capability.
     *
     * @since 150422 Rewrite.
     *
     * @type string WordPress capability.
     */
    public $cap = 'activate_plugins';

    /**
     * WordPress capability.
     *
     * @since 150422 Rewrite.
     *
     * @type string WordPress capability.
     */
    public $update_cap = 'update_plugins';

    /**
     * WordPress capability.
     *
     * @since 150422 Rewrite.
     *
     * @type string WordPress capability.
     */
    public $network_cap = 'manage_network_plugins';

    /**
     * WordPress capability.
     *
     * @since 150422 Rewrite.
     *
     * @type string WordPress capability.
     */
    public $uninstall_cap = 'delete_plugins';

    

    

    /**
     * Cache directory.
     *
     * @since 150422 Rewrite.
     *
     * @type string Cache directory; relative to the configured base directory.
     */
    public $cache_sub_dir = 'cache';

    

    

    

    /**
     * Plugin constructor.
     *
     * @since 150422 Rewrite.
     *
     * @param bool $enable_hooks Defaults to `TRUE`.
     */
    public function __construct($enable_hooks = true)
    {
        parent::__construct();

        /* -------------------------------------------------------------- */
        if (!($this->enable_hooks = (bool) $enable_hooks)) {
            return; // Stop here; construct without hooks.
        }
        /* -------------------------------------------------------------- */

        add_action('plugins_loaded', [$this, 'setup']);
        register_activation_hook(PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(PLUGIN_FILE, [$this, 'deactivate']);
    }

    /**
     * Plugin Setup.
     *
     * @since 150422 Rewrite.
     */
    public function setup()
    {
        if (!is_null($setup = &$this->cacheKey(__FUNCTION__))) {
            return; // Already setup.
        }
        $setup = -1; // Flag as having been setup.

        if ($this->enable_hooks) {
            $this->doWpAction('before_'.GLOBAL_NS.'_'.__FUNCTION__, get_defined_vars());
        }
        /* -------------------------------------------------------------- */

        load_plugin_textdomain(SLUG_TD); // Text domain.

        $this->pro_only_option_keys = [
            'cache_max_age_disable_if_load_average_is_gte',

            'change_notifications_enable',

            'cache_clear_admin_bar_options_enable',
            'cache_clear_admin_bar_roles_caps',

            'cache_clear_cdn_enable',
            'cache_clear_opcache_enable',
            'cache_clear_s2clean_enable',
            'cache_clear_eval_code',
            'cache_clear_urls',

            'ignore_get_request_vars',
            'cache_nonce_values_when_logged_in',

            'when_logged_in',

            'version_salt',
            'mobile_adaptive_salt',
            'mobile_adaptive_salt_enable',
            'ua_info_last_data_update',

            'htmlc_enable',
            'htmlc_css_exclusions',
            'htmlc_js_exclusions',
            'htmlc_uri_exclusions',
            'htmlc_cache_expiration_time',
            'htmlc_compress_combine_head_body_css',
            'htmlc_compress_combine_head_js',
            'htmlc_compress_combine_footer_js',
            'htmlc_compress_combine_remote_css_js',
            'htmlc_compress_inline_js_code',
            'htmlc_compress_css_code',
            'htmlc_compress_js_code',
            'htmlc_compress_html_code',
            'htmlc_amp_exclusions_enable',
            'htmlc_when_logged_in',

            'auto_cache_enable',
            'auto_cache_max_time',
            'auto_cache_delay',
            'auto_cache_sitemap_url',
            'auto_cache_ms_children_too',
            'auto_cache_other_urls',
            'auto_cache_user_agent',

            'htaccess_browser_caching_enable',
            'htaccess_enforce_exact_host_name',
            'htaccess_enforce_canonical_urls',
            'htaccess_access_control_allow_origin',

            'cdn_enable',
            'cdn_host',
            'cdn_hosts',
            'cdn_invalidation_var',
            'cdn_invalidation_counter',
            'cdn_over_ssl',
            'cdn_when_logged_in',
            'cdn_whitelisted_extensions',
            'cdn_blacklisted_extensions',
            'cdn_whitelisted_uri_patterns',
            'cdn_blacklisted_uri_patterns',

            'stats_enable',
            'stats_admin_bar_enable',
            'stats_admin_bar_roles_caps',

            'dir_stats_history_days',
            'dir_stats_refresh_time',
            'dir_stats_auto_refresh_max_resources',

            'pro_update_check',
            'pro_update_check_stable',
            'last_pro_update_check',

            'latest_pro_version',
            'latest_pro_package',

            'pro_update_username',
            'pro_update_password',

            'pro_auto_update_enable',

            'last_pro_stats_log',
        ];
        $this->default_options = [
            /* Core/systematic plugin options. */

            'version'  => VERSION,
            'welcomed' => '0', // `0|1` welcomed yet?

            'crons_setup'                             => '0', // A timestamp when last set up.
            'crons_setup_on_namespace'                => '', // The namespace on which they were set up.
            'crons_setup_with_cache_cleanup_schedule' => '', // The cleanup schedule selected by site owner during last setup.
            'crons_setup_on_wp_with_schedules'        => '', // A sha1 hash of `wp_get_schedules()`

            /* Primary switch; enable? */

            'enable' => '0', // `0|1`.

            /* Related to debugging. */

            'debugging_enable' => '1',
            // `0|1|2` // 2 indicates greater debugging detail.

            /* Related to cache directory. */

            'base_dir'                                     => 'cache/comet-cache', // Relative to `WP_CONTENT_DIR`.
            'cache_max_age'                                => '7 days', // `strtotime()` compatible.
            'cache_max_age_disable_if_load_average_is_gte' => '', // Load average; server-specific.
            'cache_cleanup_schedule'                       => 'hourly', // `every15m`, `hourly`, `twicedaily`, `daily`

            /* Related to cache clearing. */

            'change_notifications_enable' => '1', // `0|1`.

            'cache_clear_admin_bar_enable'         => '1', // `0|1`.
            'cache_clear_admin_bar_options_enable' => '1', // `0|1|2`.
            'cache_clear_admin_bar_roles_caps'     => '', // Comma-delimited list of roles/caps.

            'cache_clear_cdn_enable'        => '0', // `0|1`.
            'cache_clear_opcache_enable'    => '0', // `0|1`.
            'cache_clear_s2clean_enable'    => '0', // `0|1`.
            'cache_clear_eval_code'         => '', // PHP code.
            'cache_clear_urls'              => '', // Line-delimited list of URLs.
            'cache_clear_transients_enable' => '0', // `0|1`

            'cache_clear_xml_feeds_enable' => '1', // `0|1`.

            'cache_clear_xml_sitemaps_enable'  => '1', // `0|1`.
            'cache_clear_xml_sitemap_patterns' => '/sitemap**.xml',
            // Empty string or line-delimited patterns.

            'cache_clear_home_page_enable'  => '1', // `0|1`.
            'cache_clear_posts_page_enable' => '1', // `0|1`.

            'cache_clear_custom_post_type_enable' => '1', // `0|1`.
            'cache_clear_author_page_enable'      => '1', // `0|1`.

            'cache_clear_term_category_enable' => '1', // `0|1`.
            'cache_clear_term_post_tag_enable' => '1', // `0|1`.
            'cache_clear_term_other_enable'    => '1', // `0|1`.

            'cache_clear_date_archives_enable' => '1', // `0|1|2|3`.
            // 0 = No, don't clear any associated Date archive views.
            // 1 = Yes, if any single Post is cleared/reset, also clear the associated Date archive views.
            // 2 = Yes, but only clear the associated Day and Month Date archive views.
            // 3 = Yes, but only clear the associated Day Date archive view.

            /* Misc. cache behaviors. */

            'allow_client_side_cache'           => '0', // `0|1`.
            'when_logged_in'                    => '0', // `0|1|postload`.
            'get_requests'                      => '0', // `0|1`.
            'ignore_get_request_vars'           => 'utm_*', // Empty string or line-delimited patterns.
            'feeds_enable'                      => '0', // `0|1`.
            'cache_404_requests'                => '0', // `0|1`.
            'cache_nonce_values'                => '0', // `0|1`.
            'cache_nonce_values_when_logged_in' => '1', // `0|1`.

            /* Related to exclusions. */

            'exclude_hosts'            => '', // Empty string or line-delimited patterns.
            'exclude_uris'             => '', // Empty string or line-delimited patterns.
            'exclude_client_side_uris' => '', // Line-delimited list of URIs.
            'exclude_refs'             => '', // Empty string or line-delimited patterns.
            'exclude_agents'           => 'w3c_validator', // Empty string or line-delimited patterns.

            /* Related to version salt. */

            'version_salt' => '', // Any string value as a cache path component.

            // This should be set to a `+` delimited string containing any of these tokens: `os.name + device.type + browser.name + browser.version.major`.
            // There is an additional token (`browser.version`) that contains both the major and minor versions, but this token is not recommended due to many permutations.
            // There is an additional token (`device.is_mobile`) that can be used stand-alone; i.e., to indicate that being mobile is the only factor worth considering.
            'mobile_adaptive_salt'        => 'os.name + device.type + browser.name',
            'mobile_adaptive_salt_enable' => '0', // `0|1` Enable the mobile adaptive salt?
            'ua_info_last_data_update'    => '0', // Timestamp.

            /* Related to HTML compressor. */

            'htmlc_enable' => '0', // Enable HTML compression?

            'htmlc_css_exclusions' => "id='rs-plugin-settings-inline-css'", // Empty string or line-delimited patterns.
            // This defaults to an exclusion rule that handles compatibility with RevSlider. See: <https://github.com/websharks/comet-cache/issues/614>

            'htmlc_js_exclusions'         => '.php?', // Empty string or line-delimited patterns.
            'htmlc_uri_exclusions'        => '', // Empty string or line-delimited patterns.
            'htmlc_cache_expiration_time' => '14 days', // `strtotime()` compatible.

            'htmlc_compress_combine_head_body_css' => '1', // `0|1`.
            'htmlc_compress_combine_head_js'       => '1', // `0|1`.
            'htmlc_compress_combine_footer_js'     => '1', // `0|1`.
            'htmlc_compress_combine_remote_css_js' => '1', // `0|1`.
            'htmlc_compress_inline_js_code'        => '1', // `0|1`.
            'htmlc_compress_css_code'              => '1', // `0|1`.
            'htmlc_compress_js_code'               => '1', // `0|1`.
            'htmlc_compress_html_code'             => '1', // `0|1`.
            'htmlc_amp_exclusions_enable'          => '1', // `0|1`.
            'htmlc_when_logged_in'                 => '0', // `0|1`; enable when logged in?

            /* Related to auto-cache engine. */

            'auto_cache_enable'          => '0', // `0|1`.
            'auto_cache_max_time'        => '900', // In seconds.
            'auto_cache_delay'           => '500', // In milliseconds.
            'auto_cache_sitemap_url'     => 'sitemap.xml', // Relative to `site_url()`.
            'auto_cache_ms_children_too' => '0', // `0|1`. Try child blogs too?
            'auto_cache_other_urls'      => '', // A line-delimited list of any other URLs.
            'auto_cache_user_agent'      => 'WordPress',

            /* Related to .htaccess tweaks. */

            'htaccess_browser_caching_enable'      => '0', // `0|1`; enable browser caching?
            'htaccess_gzip_enable'                 => '0', // `0|1`; enable GZIP compression?
            'htaccess_enforce_exact_host_name'     => '0', // `0|1`; enforce exact hostname?
            'htaccess_enforce_canonical_urls'      => '0', // `0|1`; enforce canonical URLs?
            'htaccess_access_control_allow_origin' => '0', // `0|1`; send Access-Control-Allow-Origin header?

            /* Related to CDN functionality. */

            'cdn_enable' => '0', // `0|1`; enable CDN filters?

            'cdn_host'  => '', // e.g., `d1v41qemfjie0l.cloudfront.net`
            'cdn_hosts' => '', // e.g., line-delimited list of CDN hosts.

            'cdn_invalidation_var'     => 'iv', // A query string variable name.
            'cdn_invalidation_counter' => '1', // Current version counter.

            'cdn_over_ssl'       => '0', // `0|1`; enable SSL compat?
            'cdn_when_logged_in' => '0', // `0|1`; enable when logged in?

            'cdn_whitelisted_extensions' => '', // Whitelisted extensions.
            // This is a comma-delimited list. Delimiters may include of these: `[|;,\s]`.
            // Defaults to all extensions supported by the WP media library; i.e. `wp_get_mime_types()`.

            'cdn_blacklisted_extensions' => '', // Blacklisted extensions.
            // This is a comma-delimited list. Delimiters may include of these: `[|;,\s]`.

            'cdn_whitelisted_uri_patterns' => '', // A line-delimited list of inclusion patterns.
            // Wildcards `*` are supported here. Matched against local file URIs.

            'cdn_blacklisted_uri_patterns' => '', // A line-delimited list of exclusion patterns.
            // Wildcards `*` are supported here. Matched against local file URIs.

            /* Related to statistics/charts. */

            'stats_enable'               => is_multisite() && wp_is_large_network() ? '0' : '1',
            'stats_admin_bar_enable'     => '1', // `0|1`; enable stats in admin bar?
            'stats_admin_bar_roles_caps' => '', // Comma-delimited list of roles/caps.

            'dir_stats_auto_refresh_max_resources' => '1500', // Don't use cache if less than this.
            'dir_stats_refresh_time'               => '15 minutes', // `strtotime()` compatible.
            'dir_stats_history_days'               => '30', // Numeric; number of days.

            /* Related to automatic pro updates. */

            'pro_update_check'        => '1', // `0|1`; enable?
            'pro_update_check_stable' => '1', // `0` for beta/RC checks.
            'last_pro_update_check'   => '0', // Timestamp.

            'latest_pro_version' => VERSION, // Latest version.
            'latest_pro_package' => '', // Latest package URL.

            'pro_update_username' => '', // Username.
            'pro_update_password' => '', // Password or license key.

            'pro_auto_update_enable' => '0', // `0|1`; enable?

            /* Related to stats logging. */

            'last_pro_stats_log' => '0', // Timestamp.

            /* Related to uninstallation routines. */

            'uninstall_on_deletion' => '0', // `0|1`.
        ];
        $this->default_options = $this->applyWpFilters(GLOBAL_NS.'_default_options', $this->default_options);
        $this->options         = $this->getOptions(); // Filters, validates, and returns plugin options.

        $this->cap           = $this->applyWpFilters(GLOBAL_NS.'_cap', $this->cap);
        $this->update_cap    = $this->applyWpFilters(GLOBAL_NS.'_update_cap', $this->update_cap);
        $this->network_cap   = $this->applyWpFilters(GLOBAL_NS.'_network_cap', $this->network_cap);
        $this->uninstall_cap = $this->applyWpFilters(GLOBAL_NS.'_uninstall_cap', $this->uninstall_cap);
        
        /* -------------------------------------------------------------- */

        if (!$this->enable_hooks || strcasecmp(PHP_SAPI, 'cli') === 0) {
            return; // Stop here; setup without hooks.
        }
        /* -------------------------------------------------------------- */

        add_action('init', [$this, 'checkVersion']);
        add_action('init', [$this, 'checkAdvancedCache']);
        add_action('init', [$this, 'checkBlogPaths']);
        add_action('init', [$this, 'checkCronSetup'], PHP_INT_MAX);

        add_action('wp_loaded', [$this, 'actions']);

        

        

        

        

        add_action('admin_bar_menu', [$this, 'adminBarMenu']);
        add_action('wp_head', [$this, 'adminBarMetaTags'], 0);
        add_action('wp_enqueue_scripts', [$this, 'adminBarStyles']);
        add_action('wp_enqueue_scripts', [$this, 'adminBarScripts']);

        add_action('admin_head', [$this, 'adminBarMetaTags'], 0);
        add_action('admin_enqueue_scripts', [$this, 'adminBarStyles']);
        add_action('admin_enqueue_scripts', [$this, 'adminBarScripts']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);

        add_action('admin_menu', [$this, 'addMenuPages']);
        add_action('network_admin_menu', [$this, 'addNetworkMenuPages']);

        add_action('all_admin_notices', [$this, 'allAdminNotices']);

        add_filter('plugin_action_links_'.plugin_basename(PLUGIN_FILE), [$this, 'addSettingsLink']);

        add_filter('enable_live_network_counts', [$this, 'updateBlogPaths']);

        add_action('admin_init', [$this, 'autoClearCacheOnSettingChanges']);

        add_action('safecss_save_pre', [$this, 'autoClearCacheOnJetpackCustomCss'], 10, 1);

        add_action('activated_plugin', [$this, 'autoClearOnPluginActivationDeactivation'], 10, 2);
        add_action('deactivated_plugin', [$this, 'autoClearOnPluginActivationDeactivation'], 10, 2);

        add_action('upgrader_process_complete', [$this, 'autoClearOnUpgraderProcessComplete'], 10, 2);
        add_action('upgrader_process_complete', [$this, 'wipeOpcacheByForce'], PHP_INT_MAX);

        add_action('switch_theme', [$this, 'autoClearCache']);
        add_action('wp_create_nav_menu', [$this, 'autoClearCache']);
        add_action('wp_update_nav_menu', [$this, 'autoClearCache']);
        add_action('wp_delete_nav_menu', [$this, 'autoClearCache']);
        add_action('update_option_sidebars_widgets', [$this, 'autoClearCache']);

        add_action('save_post', [$this, 'autoClearPostCache']);
        add_action('delete_post', [$this, 'autoClearPostCache']);
        add_action('clean_post_cache', [$this, 'autoClearPostCache']);
        add_action('post_updated', [$this, 'autoClearAuthorPageCache'], 10, 3);
        add_action('pre_post_update', [$this, 'autoClearPostCacheTransition'], 10, 2);

        add_action('woocommerce_product_set_stock', [$this, 'autoClearPostCacheOnWooCommerceSetStock'], 10, 1);
        add_action('woocommerce_product_set_stock_status', [$this, 'autoClearPostCacheOnWooCommerceSetStockStatus'], 10, 1);
        add_action('update_option_comment_mail_options', [$this, 'autoClearCache']);

        add_action('added_term_relationship', [$this, 'autoClearPostTermsCache'], 10, 1);
        add_action('delete_term_relationships', [$this, 'autoClearPostTermsCache'], 10, 1);

        add_action('trackback_post', [$this, 'autoClearCommentPostCache']);
        add_action('pingback_post', [$this, 'autoClearCommentPostCache']);
        add_action('comment_post', [$this, 'autoClearCommentPostCache']);
        add_action('transition_comment_status', [$this, 'autoClearCommentPostCacheTransition'], 10, 3);

        add_action('create_term', [$this, 'autoClearCache']);
        add_action('edit_terms', [$this, 'autoClearCache']);
        add_action('delete_term', [$this, 'autoClearCache']);

        add_action('add_link', [$this, 'autoClearCache']);
        add_action('edit_link', [$this, 'autoClearCache']);
        add_action('delete_link', [$this, 'autoClearCache']);

        

        add_action('delete_user', [$this, 'autoClearAuthorPageCacheOnUserDeletion'], 10, 2);
        add_action('remove_user_from_blog', [$this, 'autoClearAuthorPageCacheOnUserDeletion'], 10, 1);

        if ($this->options['enable'] && $this->applyWpFilters(GLOBAL_NS.'_disable_akismet_comment_nonce', true)) {
            add_filter('akismet_comment_nonce', function () {
                return 'disabled-by-'.SLUG_TD; // MUST return a string literal that is not 'true' or '' (an empty string). See <http://bit.ly/1YItpdE>
            }); // See also why the Akismet nonce should be disabled: <http://jas.xyz/1R23f5c>
        }
        
        
        
        /* -------------------------------------------------------------- */

        if (!is_multisite() || is_main_site()) { // Main site only.
            add_filter('cron_schedules', [$this, 'extendCronSchedules']);
            add_action('_cron_'.GLOBAL_NS.'_cleanup', [$this, 'cleanupCache']);

            
        }
        /* -------------------------------------------------------------- */

        $this->doWpAction('after_'.GLOBAL_NS.'_'.__FUNCTION__, get_defined_vars());
        $this->doWpAction(GLOBAL_NS.'_'.__FUNCTION__.'_complete', get_defined_vars());
    }
}
