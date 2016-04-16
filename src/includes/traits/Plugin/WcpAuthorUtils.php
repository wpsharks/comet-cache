<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpAuthorUtils
{
    /**
     * Automatically clears cache files for the author page(s).
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
        $counter          = 0; // Initialize.
        $enqueued_notices = 0; // Initialize.
        $authors          = []; // Initialize.
        $authors_to_clear = []; // Initialize.

        if (!($post_id = (integer) $post_id)) {
            return $counter; // Nothing to do.
        }
        if (!is_null($done = &$this->cacheKey('autoClearAuthorPageCache', [$post_id, $post_after->ID, $post_before->ID]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if (!$this->options['cache_clear_author_page_enable']) {
            return $counter; // Nothing to do.
        }
        if (!is_dir($cache_dir = $this->cacheDir())) {
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
        if ($post_after->post_author !== $post_before->post_author &&
            ($post_before->post_status === 'publish' || $post_before->post_status === 'private')
        ) {
            $authors[] = (integer) $post_before->post_author;
            $authors[] = (integer) $post_after->post_author;
        } elseif (($post_before->post_status === 'publish' || $post_before->post_status === 'private') ||
                  ($post_after->post_status === 'publish' || $post_after->post_status === 'private')
        ) {
            $authors[] = (integer) $post_after->post_author;
        }
        if (!$authors) {
            return $counter; // Nothing to do.
        }
        foreach ($authors as $_author_id) {
            $authors_to_clear[$_author_id]['posts_url']    = get_author_posts_url($_author_id);
            $authors_to_clear[$_author_id]['display_name'] = get_the_author_meta('display_name', $_author_id);
        }
        unset($_author_id); // Housekeeping.

        foreach ($authors_to_clear as $_author) {
            $_author_regex   = $this->buildHostCachePathRegex($_author['posts_url']);
            $_author_counter = $this->clearFilesFromHostCacheDir($_author_regex);
            $counter += $_author_counter; // Add to overall counter.

            if ($_author_counter && $enqueued_notices < 100 && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
                $this->enqueueNotice(sprintf(__('Found %1$s in the cache for Author Page: <code>%2$s</code>; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($_author_counter)), esc_html($_author['display_name'])), ['combinable' => true]);
                ++$enqueued_notices; // Increment enqueued notices counter.
            }
        }
        unset($_author, $_author_regex, $_author_counter); // Housekeeping.

        $counter += $this->autoClearXmlFeedsCache('blog');
        $counter += $this->autoClearXmlFeedsCache('post-authors', $post_id);

        return $counter;
    }
}
