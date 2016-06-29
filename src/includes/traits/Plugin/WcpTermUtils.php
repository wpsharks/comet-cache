<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpTermUtils
{
    /**
     * Automatically clears cache files for terms associated with a post.
     *
     * @attaches-to `added_term_relationship` hook.
     * @attaches-to `delete_term_relationships` hook.
     *
     * @since 150422 Rewrite.
     *
     * @param int  $post_id A WordPress post ID.
     * @param bool $force   Defaults to a `FALSE` value.
     *                      Pass as TRUE if clearing should be done for `draft`, `pending`,
     *                      or `future` post statuses.
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     *
     * @note In addition to the hooks this is attached to, it is also
     *    called upon by {@link autoClearPostCache()}.
     */
    public function autoClearPostTermsCache($post_id, $force = false)
    {
        $counter          = 0; // Initialize.
        $enqueued_notices = 0; // Initialize.

        if (!($post_id = (integer) $post_id)) {
            return $counter; // Nothing to do.
        }
        if (!is_null($done = &$this->cacheKey('autoClearPostTermsCache', [$post_id, $force]))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $counter; // Nothing to do.
        }
        if (!$this->options['cache_clear_term_category_enable'] && !$this->options['cache_clear_term_post_tag_enable'] && !$this->options['cache_clear_term_other_enable']) {
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
        /*
         * Build an array of available taxonomies for this post (as taxonomy objects).
         */
        $taxonomies = get_object_taxonomies(get_post($post_id), 'objects');

        if (!is_array($taxonomies)) {
            return $counter; // Nothing to do.
        }
        /*
         * Build an array of terms associated with this post for each taxonomy.
         * Also save taxonomy label information for Dashboard messaging later.
         */
        $terms           = [];
        $taxonomy_labels = [];

        foreach ($taxonomies as $_taxonomy) {
            if (// Check if this is a taxonomy/term that we should clear.
                ($_taxonomy->name === 'category' && !$this->options['cache_clear_term_category_enable'])
                || ($_taxonomy->name === 'post_tag' && !$this->options['cache_clear_term_post_tag_enable'])
                || ($_taxonomy->name !== 'category' && $_taxonomy->name !== 'post_tag' && !$this->options['cache_clear_term_other_enable'])
            ) {
                continue; // Continue; nothing to do for this taxonomy.
            }
            if (is_array($_terms = wp_get_post_terms($post_id, $_taxonomy->name))) {
                $terms = array_merge($terms, $_terms);
                if (empty($_taxonomy->labels->singular_name) || $_taxonomy->labels->singular_name === '') {
                    $taxonomy_labels[$_taxonomy->name] = $_taxonomy->name;
                } else {
                    $taxonomy_labels[$_taxonomy->name] = $_taxonomy->labels->singular_name;
                }
            }
        }
        unset($_taxonomy, $_terms);

        if (empty($terms)) {
            return $counter; // Nothing to do.
        }
        /*
         * Build an array of terms with term names,
         * permalinks, and associated taxonomy labels.
         */
        $terms_to_clear = [];
        $_i             = 0;

        foreach ($terms as $_term) {
            if (($_link = get_term_link($_term))) {
                $terms_to_clear[$_i]['permalink'] = $_link;
                $terms_to_clear[$_i]['term_name'] = $_term->name;
                if (!empty($taxonomy_labels[$_term->taxonomy])) {
                    $terms_to_clear[$_i]['taxonomy_label'] = $taxonomy_labels[$_term->taxonomy];
                } else {
                    $terms_to_clear[$_i]['taxonomy_label'] = $_term->taxonomy;
                }
            }
            ++$_i; // Array index counter.
        }
        unset($_term, $_link, $_i);

        if (empty($terms_to_clear)) {
            return $counter; // Nothing to do.
        }
        foreach ($terms_to_clear as $_term) {
            $_term_regex   = $this->buildHostCachePathRegex($_term['permalink']);
            $_term_counter = $this->clearFilesFromHostCacheDir($_term_regex);
            $counter += $_term_counter; // Add to overall counter.

            if ($_term_counter && $enqueued_notices < 100 && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
                $this->enqueueNotice(sprintf(__('Found %1$s in the cache for %2$s: <code>%3$s</code>; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($_term_counter)), esc_html($_term['taxonomy_label']), esc_html($_term['term_name'])), ['combinable' => true]);
                ++$enqueued_notices; // Increment enqueued notices counter.
            }
        }
        unset($_term, $_term_regex, $_term_counter); // Housekeeping.

        $counter += $this->autoClearXmlFeedsCache('post-terms', $post_id);

        return $counter;
    }
}
