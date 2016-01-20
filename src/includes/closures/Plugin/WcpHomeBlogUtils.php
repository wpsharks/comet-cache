<?php
namespace WebSharks\ZenCache;

/*
 * Automatically clears cache files for the home page.
 *
 * @since 150422 Rewrite.
 *
 * @throws \Exception If a clear failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently
 *    attached to any hooks. However, it is called upon by {@link autoClearPostCache()}.
 */
$self->autoClearHomePageCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoClearHomePageCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!$self->options['cache_clear_home_page_enable']) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    $regex = $self->buildHostCachePathRegex(home_url('/'));
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for the designated "Home Page"; auto-clearing.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    $counter += $self->autoClearXmlFeedsCache('blog');

    return $counter;
};

/*
 * Automatically clears cache files for the posts page.
 *
 * @since 150422 Rewrite.
 *
 * @throws \Exception If a clear failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently
 *    attached to any hooks. However, it is called upon by {@link autoClearPostCache()}.
 */
$self->autoClearPostsPageCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoClearPostsPageCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!$self->options['cache_clear_posts_page_enable']) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    $show_on_front  = get_option('show_on_front');
    $page_for_posts = get_option('page_for_posts');

    if (!in_array($show_on_front, array('posts', 'page'), true)) {
        return $counter; // Nothing we can do in this case.
    }
    if ($show_on_front === 'page' && !$page_for_posts) {
        return $counter; // Nothing we can do.
    }
    if ($show_on_front === 'posts') {
        $posts_page = home_url('/');
    } elseif ($show_on_front === 'page') {
        $posts_page = get_permalink($page_for_posts);
    }
    if (empty($posts_page)) {
        return $counter; // Nothing we can do.
    }
    $regex = $self->buildHostCachePathRegex($posts_page);
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for the designated "Posts Page"; auto-clearing.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    $counter += $self->autoClearXmlFeedsCache('blog');

    return $counter;
};
