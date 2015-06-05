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
        'dismissError',
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
                    $this->{$_action}($_args);
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
        if (!current_user_can($this->plugin->network_cap)) {
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
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
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
            unlink($_FILES[GLOBAL_NS]['tmp_name']['import_options']);
            $args = wp_slash(json_decode($import_file_contents, true));
            unset($args['crons_setup']); // Unset; CANNOT be imported.
        }
        $args = array_map('trim', stripslashes_deep((array) $args));

        if (!IS_PRO) { // Do not save lite option keys.
            $args = array_diff_key($args, $this->plugin->pro_only_option_keys);
        }
        if (isset($args['base_dir'])) {
            $args['base_dir'] = trim($args['base_dir'], '\\/'." \t\n\r\0\x0B");
        }
        $this->plugin->options = array_merge($this->plugin->default_options, $this->plugin->options, $args);
        $this->plugin->options = array_intersect_key($this->plugin->options, $this->plugin->default_options);

        if (!trim($this->plugin->options['base_dir'], '\\/'." \t\n\r\0\x0B") || strpos(basename($this->plugin->options['base_dir']), 'wp-') === 0) {
            $this->plugin->options['base_dir'] = $this->plugin->default_options['base_dir'];
        }
        update_option(GLOBAL_NS.'_options', $this->plugin->options);
        if (is_multisite()) {
            update_site_option(GLOBAL_NS.'_options', $this->plugin->options);
        }
        $redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_updated' => '1');

        $this->plugin->autoWipeCache(); // May produce a notice.

        if ($this->plugin->options['enable']) {
            if (!($add_wp_cache_to_wp_config = $this->plugin->addWpCacheToWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_add_failure'] = '1';
            }
            if (!($add_advanced_cache = $this->plugin->addAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_add_failure'] = $add_advanced_cache === null ? 'zc-advanced-cache' : '1';
            }
            $this->plugin->updateBlogPaths();
        } else {
            if (!($remove_wp_cache_from_wp_config = $this->plugin->removeWpCacheFromWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'] = '1';
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
    protected function restoreDefaultOptions($args)
    {
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        delete_option(GLOBAL_NS.'_options');
        if (is_multisite()) {
            delete_site_option(GLOBAL_NS.'_options');
        }
        $this->plugin->options = $this->plugin->default_options;

        $redirect_to = self_admin_url('/admin.php'); // Redirect preparations.
        $query_args  = array('page' => GLOBAL_NS, GLOBAL_NS.'_restored' => '1');

        $this->plugin->autoWipeCache(); // May produce a notice.

        if ($this->plugin->options['enable']) {
            if (!($add_wp_cache_to_wp_config = $this->plugin->addWpCacheToWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_add_failure'] = '1';
            }
            if (!($add_advanced_cache = $this->plugin->addAdvancedCache())) {
                $query_args[GLOBAL_NS.'_advanced_cache_add_failure'] = $add_advanced_cache === null ? 'zc-advanced-cache' : '1';
            }
            $this->plugin->updateBlogPaths();
        } else {
            if (!($remove_wp_cache_from_wp_config = $this->plugin->removeWpCacheFromWpConfig())) {
                $query_args[GLOBAL_NS.'_wp_config_wp_cache_remove_failure'] = '1';
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
        $args = array_map('trim', stripslashes_deep((array) $args));
        if (empty($args['key'])) {
            return; // Nothing to dismiss.
        }
        $notices = is_array($notices = get_option(GLOBAL_NS.'_notices')) ? $notices : array();
        unset($notices[$args['key']]); // Dismiss this notice.
        update_option(GLOBAL_NS.'_notices', $notices);

        wp_redirect(remove_query_arg(GLOBAL_NS)).exit();
    }

    /**
     * Action handler.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed Input action argument(s).
     */
    protected function dismissError($args)
    {
        if (!current_user_can($this->plugin->cap)) {
            return; // Nothing to do.
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'])) {
            return; // Unauthenticated POST data.
        }
        $args = array_map('trim', stripslashes_deep((array) $args));
        if (empty($args['key'])) {
            return; // Nothing to dismiss.
        }
        $errors = is_array($errors = get_option(GLOBAL_NS.'_errors')) ? $errors : array();
        unset($errors[$args['key']]); // Dismiss this error.
        update_option(GLOBAL_NS.'_errors', $errors);

        wp_redirect(remove_query_arg(GLOBAL_NS)).exit();
    }
}
