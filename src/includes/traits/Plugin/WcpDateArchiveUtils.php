<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpDateArchiveUtils
{
    /**
     * Automatically clears date archives.
     *
     * @since 170220 Date archive clearing.
     *
     * @param int  $post_id A WordPress post ID.
     * @param bool $force   Defaults to a `FALSE` value.
     *                      Pass as TRUE if clearing should be done for `draft`, `pending`,
     *                      `future`, or `trash` post statuses.
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     *
     * @note This is only called upon by other routines which listen for
     *    events that are indirectly associated with a post ID.
     */
    public function autoClearDateArchiveCache($post_id, $force = false)
    {
        $counter          = 0; // Initialize.
        $enqueued_notices = 0; // Initialize.

        if (!($post_id = (int) $post_id)) {
            return $counter; // Nothing to do.
        }
        if (!is_null($done = &$this->cacheKey('autoClearDateArchiveCache', [$post_id, $force]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $counter; // Nothing to do.
        }
        if (!$this->options['cache_clear_date_archives_enable']) {
            return $counter; // Nothing to do.
        }
        if (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        $post_status = get_post_status($post_id); // Cache this.

        if ($post_status === 'draft' && isset($GLOBALS['pagenow'], $_POST['publish'])
            && is_admin() && $GLOBALS['pagenow'] === 'post.php' && current_user_can('publish_posts')
            && mb_strpos(wp_get_referer(), '/post-new.php') !== false
        ) {
            $post_status = 'publish'; // A new post being published now.
        }
        if (in_array($post_status, ['inherit', 'auto-draft'], true)) {
            return $counter; // Nothing to do. Note: `inherit` = revision.
        }
        if (in_array($post_status, ['draft', 'pending', 'future'], true) && !$force) {
            return $counter; // Nothing to do; i.e., NOT forcing in this case.
        }
        $date_archive_urls = []; // Initialize archive urls.
        $publish_time      = get_post_time('U', true, $post_id);

        $Y = date('Y', $publish_time);
        $m = date('m', $publish_time);
        $j = date('j', $publish_time);

        if ($this->options['cache_clear_date_archives_enable'] === '1') {
            $date_archive_urls[sprintf(__('%1$s Date Archive', 'comet-cache'), $Y)]                   = get_year_link($Y);
            $date_archive_urls[sprintf(__('%1$s/%2$s Date Archive', 'comet-cache'), $Y, $m)]          = get_month_link($Y, $m);
            $date_archive_urls[sprintf(__('%1$s/%2$s/%3$s Date Archive', 'comet-cache'), $Y, $m, $j)] = get_day_link($Y, $m, $j);
        } elseif ($this->options['cache_clear_date_archives_enable'] === '2') {
            $date_archive_urls[sprintf(__('%1$s/%2$s Date Archive', 'comet-cache'), $Y, $m)]          = get_month_link($Y, $m);
            $date_archive_urls[sprintf(__('%1$s/%2$s/%3$s Date Archive', 'comet-cache'), $Y, $m, $j)] = get_day_link($Y, $m, $j);
        } else { // Assume $this->options['cache_clear_date_archives_enable'] === '3'
            $date_archive_urls[sprintf(__('%1$s/%2$s/%3$s Date Archive', 'comet-cache'), $Y, $m, $j)] = get_day_link($Y, $m, $j);
        }
        foreach ($date_archive_urls as $_label => $_url) {
            $_url_regex   = $this->buildHostCachePathRegex($_url);
            $_url_counter = $this->clearFilesFromHostCacheDir($_url_regex);
            $counter += $_url_counter; // Add to overall counter.

            if ($_url_counter && $enqueued_notices < 100 && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
                $this->enqueueNotice(sprintf(__('Found %1$s in the cache for %2$s; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($_url_counter)), esc_html($_label)), ['combinable' => true]);
                ++$enqueued_notices; // Increment enqueued notices counter.
            }
        } // unset($_label, $_url, $_url_regex, $_url_counter); // Housekeeping.

        return $counter;
    }
}
