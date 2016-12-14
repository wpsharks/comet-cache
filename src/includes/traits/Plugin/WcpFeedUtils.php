<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpFeedUtils
{
    /**
     * Automatically clears cache files related to XML feeds.
     *
     * @since 150422 Rewrite.
     *
     * @param string $type    Type of feed(s) to auto-clear.
     * @param int    $post_id A Post ID (when applicable).
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     *
     * @note Unlike many of the other `auto_` methods, this one is NOT currently
     *    attached to any hooks. However, it is called upon by other routines attached to hooks.
     */
    public function autoClearXmlFeedsCache($type, $post_id = 0)
    {
        $counter = 0; // Initialize.

        if (!($type = (string) $type)) {
            return $counter; // Nothing we can do.
        }
        $post_id = (int) $post_id; // Force integer.

        if (($done = &$this->cacheKey('autoClearXmlFeedsCache', [$type, $post_id]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        } elseif (!$this->options['feeds_enable']) {
            return $counter; // Nothing to do.
        } elseif (!$this->options['cache_clear_xml_feeds_enable']) {
            return $counter; // Nothing to do.
        } elseif (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        $utils      = new Classes\FeedUtils(); // Feed utilities.
        $variations = $variation_regex_frags = []; // Initialize.

        switch ($type) { // Handle clearing based on the `$type`.

            case 'blog': // The blog feed; i.e. `/feed/` on most WP installs.
                $variations = array_merge($variations, $utils->feedLinkVariations());
                break; // Break switch handler.

            case 'blog-comments': // The blog comments feed; i.e. `/comments/feed/` on most WP installs.
                $variations = array_merge($variations, $utils->feedLinkVariations('comments_'));
                break; // Break switch handler.

            case 'post-comments': // Feeds related to comments that a post has.
                if (!$post_id) {
                    break; // Break switch handler.
                }
                if (!($post = get_post($post_id))) {
                    break; // Break switch handler.
                }
                $variations = array_merge($variations, $utils->postCommentsFeedLinkVariations($post));
                break; // Break switch handler.

            case 'post-authors': // Feeds related to authors that a post has.
                if (!$post_id) {
                    break; // Break switch handler.
                }
                if (!($post = get_post($post_id))) {
                    break; // Break switch handler.
                }
                $variations = array_merge($variations, $utils->postAuthorFeedLinkVariations($post));
                break; // Break switch handler.

            case 'post-terms': // Feeds related to terms that a post has.
                if (!$post_id) {
                    break; // Break switch handler.
                }
                if (!($post = get_post($post_id))) {
                    break; // Break switch handler.
                }
                $variations = array_merge($variations, $utils->postTermFeedLinkVariations($post, true));
                break; // Break switch handler.

            case 'custom-post-type': // Feeds related to a custom post type archive view.
                if (!$post_id) {
                    break; // Break switch handler.
                }
                if (!($post = get_post($post_id))) {
                    break; // Break switch handler.
                }
                $variations = array_merge($variations, $utils->postTypeArchiveFeedLinkVariations($post));
                break; // Break switch handler.

            // @TODO Possibly consider search-related feeds in the future.
            //    See: <http://codex.wordpress.org/WordPress_Feeds#Categories_and_Tags>
        }
        if (!($variation_regex_frags = $utils->convertVariationsToHostCachePathRegexFrags($variations))) {
            return $counter; // Nothing to do here.
        }
        $in_sets_of = $this->applyWpFilters(GLOBAL_NS.'_autoClearXmlFeedsCache_in_sets_of', 10, get_defined_vars());

        for ($_i = 0; $_i < count($variation_regex_frags); $_i = $_i + $in_sets_of) {
            $_variation_regex_frags = array_slice($variation_regex_frags, $_i, $in_sets_of);
            $_regex                 = '/^\/(?:'.implode('|', $_variation_regex_frags).')\./i';
            $counter += $this->clearFilesFromHostCacheDir($_regex);
        } // unset($_i, $_variation_regex_frags, $_regex); // Housekeeping.

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Found %1$s in the cache, for XML feeds of type: <code>%2$s</code>; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($counter)), esc_html($type)), ['combinable' => true]);
        }
        return $counter;
    }
}
