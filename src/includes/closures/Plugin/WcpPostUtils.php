<?php
namespace WebSharks\ZenCache;

/*
 * Automatically clears cache files for a particular post.
 *
 * @attaches-to `save_post` hook.
 * @attaches-to `delete_post` hook.
 * @attaches-to `clean_post_cache` hook.
 *
 * @since 150422 Rewrite.
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
 * @note This is also called upon by other routines which listen for
 *    events that are indirectly associated with a post ID.
 */
$self->autoClearPostCache = function ($post_id, $force = false) use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($allow = &$self->cacheKey('autoClearPostCache_allow'))) {
        if ($allow === false) { // Disallow?
            $allow = true; // Reset flag.
            return $counter;
        }
    }
    if (!($post_id = (integer) $post_id)) {
        return $counter; // Nothing to do.
    }
    if (!is_null($done = &$self->cacheKey('autoClearPostCache', array($post_id, $force)))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    if (!empty($self->pre_post_update_post_permalink[$post_id]) && ($permalink = $self->pre_post_update_post_permalink[$post_id])) {
        $self->pre_post_update_post_permalink[$post_id] = ''; // Reset; only used for post status transitions
    } elseif (!($permalink = get_permalink($post_id))) {
        return $counter; // Nothing we can do.
    }
    if (!($post_status = get_post_status($post_id))) {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'auto-draft') {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'draft' && !$force) {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'pending' && !$force) {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'future' && !$force) {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'trash' && !$force) {
        return $counter; // Nothing to do.
    }
    if (($type = get_post_type($post_id)) && ($type = get_post_type_object($type)) && !empty($type->labels->singular_name)) {
        $type_singular_name = $type->labels->singular_name; // Singular name for the post type.
    } else {
        $type_singular_name = __('Post', SLUG_TD); // Default value.
    }
    $regex = $self->buildHostCachePathRegex($permalink);
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for %3$s ID: <code>%4$s</code>; auto-clearing.', SLUG_TD), esc_html(NAME), esc_html($self->i18nFiles($counter)), esc_html($type_singular_name), esc_html($post_id)));
    }
    $counter += $self->autoClearXmlFeedsCache('blog');
    $counter += $self->autoClearXmlFeedsCache('post-terms', $post_id);
    $counter += $self->autoClearXmlFeedsCache('post-authors', $post_id);

    $counter += $self->autoClearXmlSitemapsCache();
    $counter += $self->autoClearHomePageCache();
    $counter += $self->autoClearPostsPageCache();
    $counter += $self->autoClearPostTermsCache($post_id, $force);
    $counter += $self->autoClearCustomPostTypeArchiveCache($post_id);

    return $counter;
};

/*
 * Automatically clears cache files for a particular post when transitioning
 *    from `publish` or `private` post status to `draft`, `future`, `private`, or `trash`.
 *
 * @attaches-to `pre_post_update` hook.
 *
 * @since 150422 Rewrite.
 *
 * @param int   $post_id Post ID.
 * @param array $data    Array of unslashed post data.
 *
 * @throws \Exception If a clear failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note This is also called upon by other routines which listen for
 *    events that are indirectly associated with a post ID.
 */
$self->autoClearPostCacheTransition = function ($post_id, $data) use ($self) {
    $counter = 0; // Initialize.

    $old_status = (string) get_post_status($post_id);
    $new_status = (string) $data['post_status'];
    /*
     * When a post has a status of `pending` or `draft`, the `get_permalink()` function
     * does not return a friendly permalink and therefore `autoClearPostCache()` will
     * have no way of building a path to the cache file that should be cleared as part of
     * this post status transition. To get around this, we temporarily store the permalink
     * in $self->pre_post_update_post_permalink for `autoClearPostCache()` to use.
     *
     * See also: https://github.com/websharks/zencache/issues/441
     */
    if ($old_status === 'publish' && in_array($new_status, array('pending', 'draft'), true)) {
        $self->pre_post_update_post_permalink[$post_id] = get_permalink($post_id);
    }
    if (!is_null($done = &$self->cacheKey('autoClearPostCacheTransition', array($old_status, $new_status, $post_id)))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($old_status !== 'publish' && $old_status !== 'private') {
        return $counter; // MUST be transitioning FROM one of these statuses.
    }
    if (in_array($new_status, array('draft', 'future', 'pending', 'private', 'trash'), true)) {
        $counter += $self->autoClearPostCache($post_id, true);
    }
    return $counter;
};
