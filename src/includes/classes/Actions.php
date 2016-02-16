<?php
namespace WebSharks\ZenCache;

/**
 * Actions.
 *
 * @since 150422 Rewrite.
 */
class Actions extends AbsBase
{
    /**
     * Allowed actions.
     *
     * @since 150422 Rewrite.
     */
    protected $allowed_actions = array(
        'wipeCache',
        'clearCache',

        

        

        

        

        

        

        'saveOptions',
        'restoreDefaultOptions',

        

        

        'dismissNotice',
    );

    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        parent::__construct();

        if (empty($_REQUEST[GLOBAL_NS])) {
            return; // Not applicable.
        }
        foreach ((array) $_REQUEST[GLOBAL_NS] as $_action => $_args) {
            if (is_string($_action) && method_exists($this, $_action)) {
                if (in_array($_action, $this->allowed_actions, true)) {
                    $this->{$_action}($_args); // Do action!
                }
            }
        }
        unset($_action, $_args); // Housekeeping.
    }

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function wipeCache($args)
    {
        if (!is_multisite() || !$this->plugin->currentUserCanWipeCache()) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        $counter = $this->plugin->wipeCache(true);

        

        $redirect_to = self_admin_url('/admin.php');
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_cache_wiped' => '1');
        $redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

        wp_redirect($redirect_to).exit();
    }

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function clearCache($args)
    {
        if (!$this->plugin->currentUserCanClearCache()) {
            return; // Not allowed to clear.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        $counter = $this->plugin->clearCache(true);

        

        $redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_cache_cleared' => '1');
        $redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

        wp_redirect($redirect_to).exit();
    }

    

    

    

    

    

    

    

    

    

    

    

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function saveOptions($args)
    {
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        if (!empty($_FILES[GLOBAL_NS]['tmp_name']['import_options'])) {
            $import_file_contents = file_get_contents($_FILES[GLOBAL_NS]['tmp_name']['import_options']);
            unlink($_FILES[GLOBAL_NS]['tmp_name']['import_options']); // Deleted uploaded file.

            $args = wp_slash(json_decode($import_file_contents, true));

            unset($args['crons_setup']); // CANNOT be imported!
            unset($args['last_pro_update_check']); // CANNOT be imported!
            unset($args['last_pro_stats_log']); // CANNOT be imported!
        }
        $args = $this->plugin->trimDeep(stripslashes_deep((array) $args));
        $this->plugin->updateOptions($args); // Save/update options.

        // Ensures `autoCacheMaybeClearPrimaryXmlSitemapError()` always validates the XML Sitemap when saving options (when applicable)
        delete_transient(GLOBAL_NS.'-'.md5($this->plugin->options['auto_cache_sitemap_url']));

        $redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_updated' => '1');

        $this->plugin->autoWipeCache(); // May produce a notice.

        global $is_apache, $is_nginx;

        if ($this->plugin->options['enable']) {
            if (!($add_wp_cache_to_wp_config = $this->plugin->addWpCacheToWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_add_failure'] = '1';
            }
            if ($is_apache && !($add_wp_htaccess = $this->plugin->addWpHtaccess())) {
                $query_args[GLOBAL_NS.'_wp_htaccess_add_failure'] = '1';
            }
            if ($is_nginx && $this->plugin->applyWpFilters(GLOBAL_NS.'_wp_htaccess_nginx_notice', true) && (!isset($_SERVER['WP_NGINX_CONFIG']) || $_SERVER['WP_NGINX_CONFIG'] !== 'done')) {
                $query_args[GLOBAL_NS.'_wp_htaccess_nginx_notice'] = '1';
            }
            if (!($add_advanced_cache = $this->plugin->addAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_add_failure'] = $add_advanced_cache === null ? 'advanced-cache' : '1';
            }
            if (!$this->plugin->options['auto_cache_enable']) {
                $this->plugin->dismissMainNotice('allow_url_fopen_disabled'); // Dismiss and check again on `admin_init` via `autoCacheMaybeClearPhpIniError()`
            }
            if (!$this->plugin->options['auto_cache_enable'] || !$this->plugin->options['auto_cache_sitemap_url']) {
                $this->plugin->dismissMainNotice('xml_sitemap_missing'); // Dismiss and check again on `admin_init` via `autoCacheMaybeClearPrimaryXmlSitemapError()`
            }
            $this->plugin->updateBlogPaths(); // Multisite networks only.
        } else {
            if (!($remove_wp_cache_from_wp_config = $this->plugin->removeWpCacheFromWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'] = '1';
            }
            if ($is_apache && !($remove_wp_htaccess = $this->plugin->removeWpHtaccess())) {
                $query_args[GLOBAL_NS.'_wp_htaccess_remove_failure'] = '1';
            }
            if (!($remove_advanced_cache = $this->plugin->removeAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_remove_failure'] = '1';
            }
            $this->plugin->dismissMainNotice('xml_sitemap_missing'); // Dismiss notice when disabling plugin
            $this->plugin->dismissMainNotice('allow_url_fopen_disabled'); // Dismiss notice when disabling plugin
        }
        $redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

        wp_redirect($redirect_to).exit();
    }

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function restoreDefaultOptions($args)
    {
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
        }
        if (is_multisite() && !current_user_can($this->plugin->network_cap)) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        $this->plugin->restoreDefaultOptions(); // Restore defaults.

        $redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_restored' => '1');

        $this->plugin->autoWipeCache(); // May produce a notice.

        global $is_apache, $is_nginx;

        if ($this->plugin->options['enable']) {
            if (!($add_wp_cache_to_wp_config = $this->plugin->addWpCacheToWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_add_failure'] = '1';
            }
            if ($is_apache && !($add_wp_htaccess = $this->plugin->addWpHtaccess())) {
                $query_args[GLOBAL_NS.'_wp_htaccess_add_failure'] = '1';
            }
            if ($is_nginx && $this->plugin->applyWpFilters(GLOBAL_NS.'_wp_htaccess_nginx_notice', true) && (!isset($_SERVER['WP_NGINX_CONFIG']) || $_SERVER['WP_NGINX_CONFIG'] !== 'done')) {
                $query_args[GLOBAL_NS.'_wp_htaccess_nginx_notice'] = '1';
            }
            if (!($add_advanced_cache = $this->plugin->addAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_add_failure'] = $add_advanced_cache === null ? 'advanced-cache' : '1';
            }
            $this->plugin->updateBlogPaths(); // Multisite networks only.
        } else {
            if (!($remove_wp_cache_from_wp_config = $this->plugin->removeWpCacheFromWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'] = '1';
            }
            if ($is_apache && !($remove_wp_htaccess = $this->plugin->removeWpHtaccess())) {
                $query_args[GLOBAL_NS.'_wp_htaccess_remove_failure'] = '1';
            }
            if (!($remove_advanced_cache = $this->plugin->removeAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_remove_failure'] = '1';
            }
        }
        $redirect_to = add_query_arg(urlencode_deep($query_args), $redirect_to);

        wp_redirect($redirect_to).exit();
    }

    

    

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function dismissNotice($args)
    {
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        $args = $this->plugin->trimDeep(stripslashes_deep((array) $args));
        $this->plugin->dismissNotice($args['key']);

        wp_redirect(remove_query_arg(GLOBAL_NS)).exit();
    }
}
