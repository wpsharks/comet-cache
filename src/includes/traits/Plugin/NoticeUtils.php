<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait NoticeUtils
{
    /*
     * Notice queue handlers.
     */

    /**
     * Enqueue an administrative notice.
     *
     * @since 150422 Rewrite. Improved 151002.
     *
     * @param string $notice  HTML markup containing the notice itself.
     * @param array  $args    Any additional arguments supported by the notice API in this plugin.
     * @param int    $blog_id Optional. Defaults to the current blog ID. Use any value `< 0` to indicate the main site.
     *
     * @return string A unique key generated for this notice.
     */
    public function enqueueNotice($notice, array $args = [], $blog_id = 0)
    {
        $notice  = trim((string) $notice);
        $blog_id = (int) $blog_id;

        if (!$notice) {
            return; // Nothing to do.
        }
        $notice = ['notice' => $notice];
        $notice = $this->normalizeNotice($notice, $args);
        $key    = sha1(serialize($notice)); // Prevent dupes.

        $notices = $this->getNotices($blog_id);

        if ($notice['push_to_top']) {
            $notices = [$key => $notice] + $notices;
        } else {
            $notices[$key] = $notice; // Default behavior.
        }
        $this->updateNotices($notices, $blog_id);

        return $key; // For dismissals.
    }

    /**
     * Dismiss an administrative notice.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param string $key_to_dismiss A unique key which identifies a particular notice.
     *                               Or, a persistent key which identifies one or more persistent notices.
     * @param int    $blog_id        The blog ID from which to dismiss the notice.
     *
     * @return array All remaining notices.
     */
    public function dismissNotice($key_to_dismiss, $blog_id = 0)
    {
        $key_to_dismiss = trim((string) $key_to_dismiss);
        $blog_id        = (int) $blog_id; // For multisite compat.
        $notices        = $enqueued_notices        = $this->getNotices($blog_id);

        if (!$key_to_dismiss) {
            return $notices; // Nothing to do.
        }
        foreach ($notices as $_key => $_notice) {
            if ($_key === $key_to_dismiss) {
                unset($notices[$_key]); // A specific key.
            } elseif ($_notice['persistent_key'] === $key_to_dismiss) {
                unset($notices[$_key]); // All matching keys.
            }
        } // ↑ Dismisses all matching keys.
        unset($_key, $_notice); // Housekeeping.

        if ($notices !== $enqueued_notices) { // Something changed?
            $this->updateNotices($notices, $blog_id); // Update.
        }
        return $notices; // All remaining notices.
    }

    /**
     * Enqueue an administrative error notice.
     *
     * @since 150422 Rewrite. Improved 151002.
     */
    public function enqueueError($notice, array $args = [], $blog_id = 0)
    {
        return $this->enqueueNotice($notice, array_merge($args, ['class' => 'error']), $blog_id);
    }

    /**
     * Enqueue an administrative notice (main site).
     *
     * @since 151002. Improving multisite compat.
     */
    public function enqueueMainNotice($notice, array $args = [])
    {
        return $this->enqueueNotice($notice, $args, -1);
    }

    /**
     * Enqueue an administrative error notice (main site).
     *
     * @since 151002. Improving multisite compat.
     */
    public function enqueueMainError($notice, array $args = [])
    {
        return $this->enqueueNotice($notice, array_merge($args, ['class' => 'error']), -1);
    }

    /**
     * Dismiss an administrative notice (main site).
     *
     * @since 151002 Improving multisite compat.
     */
    public function dismissMainNotice($key_to_dismiss)
    {
        return $this->dismissNotice($key_to_dismiss, -1);
    }

    /*
     * Notice display handler.
     */

    /**
     * Render admin notices.
     *
     * @since 150422 Rewrite. Improved 151002.
     *
     * @attaches-to `all_admin_notices` hook.
     */
    public function allAdminNotices()
    {
        $notices          = $enqueued_notices          = $this->getNotices();
        $combined_notices = []; // Initialize

        foreach ($notices as $_key => $_notice) {
            # Always dismiss all non-persistent transients.

            if ($_notice['is_transient'] && !$_notice['persistent_key']) {
                unset($notices[$_key]); // Dismiss.
            }
            # Current user can see this notice?

            if (!current_user_can($this->cap)) {
                continue; // Current user unable to see.
            }
            if ($_notice['cap_required'] && !current_user_can($_notice['cap_required'])) {
                continue; // Current user unable to see this notice.
            }
            # Current URI matches a limited scope/context for this notice?

            if ($_notice['only_on_uris'] && (empty($_SERVER['REQUEST_URI']) || !@preg_match($_notice['only_on_uris'], $_SERVER['REQUEST_URI']))) {
                continue; // Not in the right context at the moment; i.e., does not regex.
            }
            # If persistent, allow a site owner to dismiss.

            $_dismiss = ''; // Initialize
            if ($_notice['persistent_key'] && $_notice['dismissable']) { // See above. The `dismissNotice()` action requires `$this->cap` always.
                $_dismiss = add_query_arg(urlencode_deep([GLOBAL_NS => ['dismissNotice' => ['key' => $_key]], '_wpnonce' => wp_create_nonce()]));
                $_dismiss = '<a href="'.esc_attr($_dismiss).'"><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Dismiss this notice.', 'comet-cache').'</span></button></a>';
            }
            # Display this notice, or save for displaying compacted later. If not persistent, we can dismiss it too.

            if ($_notice['combinable'] && !$_notice['persistent_key']) {
                $combined_notices[] = $_notice['notice']; // Save this for displaying as part of a single, combined notice.
            } else {
                $_notice['notice'] = trim($_notice['notice']);
                if (!preg_match('/^\<(?:p|div|form|h[1-6]|ul|ol)[\s>]/ui', $_notice['notice'])) {
                    $_notice['notice'] = '<p>'.$_notice['notice'].'</p>'; // Add `<p>` tag.
                }
                echo '<div class="'.esc_attr($_notice['class']).'" style="clear:both; padding-right:38px; position: relative;">'.$_notice['notice'].$_dismiss.'</div>';
            }
            if (!$_notice['persistent_key']) { // If not persistent, dismiss.
                unset($notices[$_key]); // Dismiss; this notice has been displayed now.
            }
        }
        unset($_key, $_notice, $_dismiss); // Housekeeping.

        if (!empty($combined_notices)) { // Display a single notice with details hidden by default.
            $_line_items = ''; // Initialize
            foreach ($combined_notices as $_item) {
                $_line_items .= '<p><span class="dashicons dashicons-yes"></span> '.$_item.'</p>'."\n";
            }
            $_see_details  = __('See details.', 'comet-cache');
            $_hide_details = __('Hide details.', 'comet-cache');

            $_combined = '<div class="updated notice is-dismissible" style="clear:both; padding-right:38px; position: relative;">';
            $_combined .= '<p><img src="'.esc_attr($this->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />';
            $_combined .= sprintf(__('<strong>%1$s</strong> detected changes and intelligently cleared the cache to keep your site up-to-date.', 'comet-cache'), esc_html(NAME)).' <a href="#" id="'.SLUG_TD.'-toggle-notices" style="text-decoration:none; border-bottom:1px dotted;" onclick="jQuery(\'#'.SLUG_TD.'-combined-notices\').toggle(); if (jQuery(\'#comet-cache-combined-notices\').is(\':visible\')) { jQuery(this).text(\''.$_hide_details.'\'); } else { jQuery(this).text(\''.$_see_details.'\'); }">'.$_see_details.'</a></p>';
            $_combined .= '<div id="'.SLUG_TD.'-combined-notices" style="display: none;">'.$_line_items.'</div>';
            $_combined .= '<button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Dismiss this notice.', 'comet-cache').'</span></button>';
            $_combined .= '</div>';

            echo $_combined;

            unset($_item, $_line_item, $_combined); // Housekeeping.
        }
        # Update notices if something changed above.

        if ($notices !== $enqueued_notices) { // Something changed?
            $this->updateNotices($notices); // Update.
        }
    }

    /*
     * Notice getter/setter.
     */

    /**
     * Get admin notices.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param int $blog_id Optional. Defaults to the current blog ID.
     *                     Use any value `< 0` to indicate the main site.
     *
     * @return array All notices.
     */
    public function getNotices($blog_id = 0)
    {
        if (is_multisite()) {
            if (!($blog_id = (int) $blog_id)) {
                $blog_id = (int) get_current_blog_id();
            }
            if ($blog_id < 0) { // Blog for main site.
                $blog_id = (int) get_current_site()->blog_id;
            }
            $blog_suffix = '_'.$blog_id; // Site option suffix.
            $notices     = get_site_option(GLOBAL_NS.$blog_suffix.'_notices');
        } else {
            $notices = get_site_option(GLOBAL_NS.'_notices');
        }
        if (!is_array($notices)) {
            $notices = []; // Force array.
            // Prevent multiple DB queries by adding this key.
            $this->updateNotices($notices, $blog_id);
        }
        foreach ($notices as $_key => &$_notice) {
            if (!is_string($_key) || !is_array($_notice) || empty($_notice['notice'])) {
                unset($notices[$_key]); // Old notice.
                continue; // Bypass; i.e., do not normalize.
            }
            $_notice = $this->normalizeNotice($_notice);
        } // ↑ Typecast/normalized each of the array elements.
        unset($_key, $_notice); // Housekeeping.

        return $notices;
    }

    /**
     * Update admin notices.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param array $notices New array of notices.
     * @param int   $blog_id Optional. Defaults to the current blog ID.
     *                       Use any value `< 0` to indicate the main site.
     *
     * @return array All notices.
     */
    public function updateNotices(array $notices, $blog_id = 0)
    {
        if (is_multisite()) {
            if (!($blog_id = (int) $blog_id)) {
                $blog_id = (int) get_current_blog_id();
            }
            if ($blog_id < 0) { // Blog for main site.
                $blog_id = (int) get_current_site()->blog_id;
            }
            $blog_suffix = '_'.$blog_id; // Site option suffix.
            update_site_option(GLOBAL_NS.$blog_suffix.'_notices', $notices);
        } else {
            update_site_option(GLOBAL_NS.'_notices', $notices);
        }
        return $notices;
    }

    /*
     * Notice property utilities.
     */

    /**
     * Normalize notice elements.
     *
     * @since 151002 Improving multisite compat.
     *
     * @param array $notice Notice array elements.
     * @param array $args   Any additional array elements.
     *
     * @return array Normalized notice array elements.
     */
    public function normalizeNotice(array $notice, array $args = [])
    {
        $notice_defaults = [
            'notice'         => '',
            'only_on_uris'   => '',
            'persistent_key' => '',
            'combinable'     => false,
            'dismissable'    => true,
            'is_transient'   => true,
            'push_to_top'    => false,
            'class'          => 'updated',
            'cap_required'   => '', // `$this->cap` always.
            // i.e., this cap is in addition to `$this->cap`.
        ];
        $notice = array_merge($notice_defaults, $notice, $args);
        $notice = array_intersect_key($notice, $notice_defaults);

        foreach ($notice as $_key => &$_value) {
            switch ($_key) {
                case 'notice':
                case 'only_on_uris':
                case 'persistent_key':
                    $_value = trim((string) $_value);
                    break; // Stop here.

                case 'is_transient':
                case 'push_to_top':
                case 'combinable':
                case 'dismissable':
                    $_value = (bool) $_value;
                    break; // Stop here.

                case 'class':
                case 'cap_required':
                    $_value = trim((string) $_value);
                    break; // Stop here.
            }
        } // ↑ Typecast each of the array elements.
        unset($_key, $_value); // A little housekeeping.

        ksort($notice); // For more accurate comparison in other routines.

        return $notice; // Normalized.
    }
}
