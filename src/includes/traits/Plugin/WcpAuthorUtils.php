<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpAuthorUtils
{
    /**
     * Clears cache files for the author page(s).
     *
     * @attaches-to `post_updated` hook.
     *
     * @since 150422 Rewrite.
     *
     * @param int      $post_id     A WordPress post ID.
     * @param \WP_Post $post_after  WP_Post object following the update.
     * @param \WP_Post $post_before WP_Post object before the update.
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     *
     * @note If the author for the post is being changed, both the previous author
     *       and current author pages are cleared, if the post status is applicable.
     */
    public function autoClearAuthorPageCache($post_id, \WP_Post $post_after, \WP_Post $post_before)
    {
        $authors = $authors_to_clear = []; // Initialize.
        $counter = $enqueued_notices = 0; // Initialize.

        if (!($post_id = (int) $post_id)) {
            return $counter; // Nothing to do.
        } elseif (($done = &$this->cacheKey('autoClearAuthorPageCache', [$post_id, $post_after->ID, $post_before->ID]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        } elseif (!$this->options['cache_clear_author_page_enable']) {
            return $counter; // Nothing to do.
        } elseif (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        /*
         * If we're changing the post author AND
         *    the previous post status was either 'published' or 'private'
         * then clear the author page for both authors.
         *
         * Else if the old post status was 'published' or 'private' OR
         *    the new post status is 'published' or 'private'
         * then clear the author page for the current author.
         *
         * Else return the counter; post status does not warrant clearing author page cache.
         */
        if ($post_after->post_author !== $post_before->post_author
                && ($post_before->post_status === 'publish' || $post_before->post_status === 'private')) {
            $authors[] = (int) $post_before->post_author;
            $authors[] = (int) $post_after->post_author;
        } elseif (($post_before->post_status === 'publish' || $post_before->post_status === 'private')
                || ($post_after->post_status === 'publish' || $post_after->post_status === 'private')) {
            $authors[] = (int) $post_after->post_author;
        }
        foreach ($authors as $_author_id) {
            $authors_to_clear[$_author_id]['posts_url']    = get_author_posts_url($_author_id);
            $authors_to_clear[$_author_id]['display_name'] = get_the_author_meta('display_name', $_author_id);
        } // unset($_author_id); // Housekeeping.

        if (!$authors_to_clear) {
            return $counter; // Nothing to do.
        }
        foreach ($authors_to_clear as $_author) {
            $_author_regex   = $this->buildHostCachePathRegex($_author['posts_url']);
            $_author_counter = $this->clearFilesFromHostCacheDir($_author_regex);
            $counter += $_author_counter; // Add to overall counter.

            if ($_author_counter && $enqueued_notices < 100 && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
                $this->enqueueNotice(sprintf(__('Found %1$s in the cache for Author Page: <code>%2$s</code>; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($_author_counter)), esc_html($_author['display_name'])), ['combinable' => true]);
                ++$enqueued_notices; // Increment enqueued notices counter.
            }
        } // unset($_author, $_author_regex, $_author_counter); // Housekeeping.

        $counter += $this->autoClearXmlFeedsCache('blog');
        $counter += $this->autoClearXmlFeedsCache('post-authors', $post_id);

        return $counter;
    }

    /**
     * Clears cache files for the author page(s).
     *
     * @attaches-to `remove_user_from_blog` hook.
     * @attaches-to `delete_user` hook.
     *
     * @since 161221 Adding support for user deletions.
     *
     * @param int $user_id     A WordPress user ID.
     * @param int $rat_user_id User ID (reassign via `delete_user` hook).
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     */
    public function autoClearAuthorPageCacheOnUserDeletion($user_id, $rat_user_id = 0)
    {
        $authors_to_clear = []; // Initialize.
        $rat_user_id      = (int) $rat_user_id;
        $counter          = $enqueued_notices          = 0;

        if (!($user_id = (int) $user_id)) {
            return $counter; // Nothing to do.
        } elseif (($done = &$this->cacheKey('autoClearAuthorPageCacheOnUserDeletion', [$user_id, $rat_user_id]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        } elseif (!$this->options['cache_clear_author_page_enable']) {
            return $counter; // Nothing to do.
        } elseif (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        if (($WP_User = new \WP_User($user_id)) && $WP_User->exists() && $WP_User->has_cap('edit_posts')) {
            $authors_to_clear[$WP_User->ID]['posts_url']    = get_author_posts_url($WP_User->ID);
            $authors_to_clear[$WP_User->ID]['display_name'] = get_the_author_meta('display_name', $WP_User->ID);
        }
        if ($rat_user_id && ($rat_WP_User = new \WP_User($rat_user_id)) && $rat_WP_User->exists() && $rat_WP_User->has_cap('edit_posts')) {
            $authors_to_clear[$rat_WP_User->ID]['posts_url']    = get_author_posts_url($rat_WP_User->ID);
            $authors_to_clear[$rat_WP_User->ID]['display_name'] = get_the_author_meta('display_name', $rat_WP_User->ID);
        }
        if (!$authors_to_clear) {
            return $counter; // Nothing to do.
        }
        foreach ($authors_to_clear as $_author) {
            $_author_regex   = $this->buildHostCachePathRegex($_author['posts_url']);
            $_author_counter = $this->clearFilesFromHostCacheDir($_author_regex);
            $counter += $_author_counter; // Add to overall counter.

            if ($_author_counter && $enqueued_notices < 100 && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
                $this->enqueueNotice(sprintf(__('Found %1$s in the cache for Author Page: <code>%2$s</code>; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($_author_counter)), esc_html($_author['display_name'])), ['combinable' => true]);
                ++$enqueued_notices; // Increment enqueued notices counter.
            }
            // @TODO Consider clearing other cached locations here too.
        } // unset($_author, $_author_regex, $_author_counter); // Housekeeping.

        return $counter;
    }
}
