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
    if (!($post_type = get_post_type($post_id))) {
        return $counter; // Nothing to do.
    }
    $post_statuses             = $self->postStatuses();
    $unpublished_post_statuses = array_diff($post_statuses, array('publish'));
    $is_bbpress_post_type      = in_array($post_type, $self->bbPressPostTypes(), true);

    if (!empty($self->pre_post_update_post_permalink[$post_id])) {
        $permalink                                      = $self->pre_post_update_post_permalink[$post_id];
        $self->pre_post_update_post_permalink[$post_id] = ''; // Reset; only used for post status transitions.
    } elseif (!($permalink = get_permalink($post_id))) {
        return $counter; // Nothing we can do.
    }
    if (!($post_status = get_post_status($post_id))) {
        return $counter; // Nothing to do.
    }
    if ($post_status === 'draft' && isset($GLOBALS['pagenow'], $_POST['publish'])
       && is_admin() && $GLOBALS['pagenow'] === 'post.php' && current_user_can('publish_posts')
       && strpos(wp_get_referer(), '/post-new.php') !== false
    ) {
        $post_status = 'publish'; // A new post being published now.
    }
    if (in_array($post_status, array('inherit', 'auto-draft'), true)) {
        return $counter; // Nothing to do. Note: `inherit` = revision.
    }
    if (in_array($post_status, array('draft', 'pending', 'future', 'trash'), true) && !$force) {
        return $counter; // Nothing to do; i.e., NOT forcing in this case.
    }
    if (($post_type_obj = get_post_type_object($post_type)) && !empty($post_type_obj->labels->singular_name)) {
        $post_type_singular_name = $post_type_obj->labels->singular_name; // Singular name for the post type.
    } else {
        $post_type_singular_name = __('Post', 'zencache'); // Default value.
    }
    $regex = $self->buildHostCachePathRegex($permalink);
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for %3$s ID: <code>%4$s</code>; auto-clearing.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter)), esc_html($post_type_singular_name), esc_html($post_id)));
    }
    $counter += $self->autoClearXmlFeedsCache('blog');
    $counter += $self->autoClearXmlFeedsCache('post-terms', $post_id);
    $counter += $self->autoClearXmlFeedsCache('post-authors', $post_id);

    $counter += $self->autoClearXmlSitemapsCache();
    $counter += $self->autoClearHomePageCache();
    $counter += $self->autoClearPostsPageCache();
    $counter += $self->autoClearPostTermsCache($post_id, $force);
    $counter += $self->autoClearCustomPostTypeArchiveCache($post_id);
    

    if ($post_type !== 'page' && ($parent_post_id = wp_get_post_parent_id($post_id))) {
        // Recursion: i.e., nested post types like bbPress forums/topic/replies.
        $counter += $self->autoClearPostCache($parent_post_id, $force);
    }
    return $counter;
};
$self->auto_clear_post_cache = $self->autoClearPostCache; // Back compat.

/*
 * Handles post status transitioning.
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
     * See also: <https://github.com/websharks/zencache/issues/441>
     */
    if (in_array($new_status, array('pending', 'draft'), true)) {
        $self->pre_post_update_post_permalink[$post_id] = get_permalink($post_id);
    }
    // Begin post status transition sub-routine now.

    if (!is_null($done = &$self->cacheKey('autoClearPostCacheTransition', array($old_status, $new_status, $post_id)))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($new_status === $old_status) {
        return $counter; // Nothing to do.
    }
    if (!($post_type = get_post_type($post_id))) {
        return $counter; // Nothing to do.
    }
    $post_statuses             = $self->postStatuses();
    $unpublished_post_statuses = array_diff($post_statuses, array('publish'));
    $is_bbpress_post_type      = in_array($post_type, $self->bbPressPostTypes(), true);

    if ($is_bbpress_post_type) {
        if (in_array($old_status, array('publish', 'private', 'closed', 'spam', 'hidden'), true)) {
            if (in_array($new_status, $unpublished_post_statuses, true)) {
                $counter += $self->autoClearPostCache($post_id, true);
            }
        }
    } elseif (in_array($old_status, array('publish', 'private'), true)) {
        if (in_array($new_status, $unpublished_post_statuses, true)) {
            $counter += $self->autoClearPostCache($post_id, true);
        }
    }
    return $counter;
};
