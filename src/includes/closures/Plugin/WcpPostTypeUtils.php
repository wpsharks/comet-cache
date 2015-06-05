<?php
namespace WebSharks\ZenCache;

/*
 * Automatically clears cache files for a custom post type archive view.
 *
 * @since 150422 Rewrite.
 *
 * @param int $post_id A WordPress post ID.
 *
 * @throws \Exception If a clear failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently
 *    attached to any hooks. However, it is called upon by {@link autoClearPostCache()}.
 */
$self->autoClearCustomPostTypeArchiveCache = function ($post_id) use ($self) {
    $counter = 0; // Initialize.

    if (!($post_id = (integer) $post_id)) {
        return $counter; // Nothing to do.
    }
    if (!is_null($done = &$self->cacheKey('autoClearCustomPostTypeArchiveCache', $post_id))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!$self->options['cache_clear_custom_post_type_enable']) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    if (!($post_type = get_post_type($post_id))) {
        return $counter; // Nothing to do.
    }
    if (!($all_custom_post_types = get_post_types(array('_builtin' => false)))) {
        return $counter; // No custom post types.
    }
    if (!in_array($post_type, array_keys($all_custom_post_types), true)) {
        return $counter; // This is NOT a custom post type.
    }
    if (!($custom_post_type = get_post_type_object($post_type))) {
        return $counter; // Unable to retrieve post type.
    }
    if (empty($custom_post_type->labels->name) || !($custom_post_type_name = $custom_post_type->labels->name)) {
        $custom_post_type_name = __('Untitled', SLUG_TD);
    }
    if (!($custom_post_type_archive_link = get_post_type_archive_link($post_type))) {
        return $counter; // Nothing to do; no link to work from in this case.
    }
    $regex = $self->buildHostCachePathRegex($custom_post_type_archive_link);
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for Custom Post Type: <code>%3$s</code>; auto-clearing.', SLUG_TD), esc_html(NAME), esc_html($self->i18nFiles($counter)), esc_html($custom_post_type_name)));
    }
    $counter += $self->autoClearXmlFeedsCache('custom-post-type', $post_id);

    return $counter;
};
