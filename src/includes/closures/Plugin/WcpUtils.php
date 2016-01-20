<?php
namespace WebSharks\ZenCache;

/*
 * Used for temporarily storing the permalink for posts transitioning from
 *    `publish` or `private` post status to `pending` or `draft` post status.
 *
 * @since 150422 Rewrite.
 *
 * @type array An associative array with the Post ID as the named key containing
 *            the post permalink before the post has been transitioned.
 */
$self->pre_post_update_post_permalink = array();

/*
 * Wipes out all cache files.
 *
 * @since 150422 Rewrite.
 *
 * @param bool $manually TRUE if wiping is done manually.
 *
 * @throws \Exception If a wipe failure occurs.
 *
 * @return int Total files wiped by this routine.
 */
$self->wipeCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!$manually && $self->disableAutoWipeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    if (is_dir($cache_dir = $self->cacheDir())) {
        $regex = $self->assembleCachePathRegex('', '.+');
        $counter += $self->wipeFilesFromCacheDir($regex);
    }
    

    

    
    return $counter;
};
$self->wipe_cache = $self->wipeCache; // Back compat.

/*
 * Clears cache files (current blog).
 *
 * @since 150422 Rewrite.
 *
 * @param bool $manually TRUE if clearing is done manually.
 *
 * @throws \Exception If a clearing failure occurs.
 *
 * @return int Total files cleared by this routine.
 */
$self->clearCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!$manually && $self->disableAutoClearCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    if (is_dir($cache_dir = $self->cacheDir())) {
        $regex = $self->buildHostCachePathRegex('', '.+');
        $counter += $self->clearFilesFromHostCacheDir($regex);
    }
    

    

    
    return $counter;
};
$self->clear_cache = $self->clearCache; // Back compat.

/*
 * Purges expired cache files (current blog).
 *
 * @since 150422 Rewrite.
 *
 * @param bool $manually TRUE if purging is done manually.
 *
 * @throws \Exception If a purge failure occurs.
 *
 * @return int Total files purged by this routine.
 */
$self->purgeCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!$manually && $self->disableAutoPurgeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    if (is_dir($cache_dir = $self->cacheDir())) {
        $regex = $self->buildHostCachePathRegex('', '.+');
        $counter += $self->purgeFilesFromHostCacheDir($regex);
    }
    
    return $counter;
};
$self->purge_cache = $self->purgeCache; // Back compat.

/*
 * Wurges (purges) all expired cache files; like wipe, but expired files only.
 *
 * @since 151002 Look at entire cache directory.
 *
 * @param bool $manually TRUE if wurging is done manually.
 *
 * @throws \Exception If a wurge failure occurs.
 *
 * @return int Total files wurged by this routine.
 */
$self->wurgeCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!$manually && $self->disableAutoPurgeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    if (is_dir($cache_dir = $self->cacheDir())) {
        $regex = $self->assembleCachePathRegex('', '.+');
        $counter += $self->wurgeFilesFromCacheDir($regex);
    }
    
    return $counter;
};

/*
 * Automatically wipes out all cache files.
 *
 * @attaches-to Nothing at this time.
 *
 * @since 150422 Rewrite.
 *
 * @return int Total files wiped by this routine (if any).
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently attached to any hooks.
 *    This is called upon whenever options are saved and/or restored though.
 */
$self->autoWipeCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoWipeCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($self->disableAutoWipeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    $counter += $self->wipeCache();

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/wipe.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected significant changes. Found %2$s in the cache; auto-wiping.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Automatically clears all cache files (current host).
 *
 * @attaches-to `switch_theme` hook.
 *
 * @attaches-to `wp_create_nav_menu` hook.
 * @attaches-to `wp_update_nav_menu` hook.
 * @attaches-to `wp_delete_nav_menu` hook.
 *
 * @attaches-to `create_term` hook.
 * @attaches-to `edit_terms` hook.
 * @attaches-to `delete_term` hook.
 *
 * @attaches-to `add_link` hook.
 * @attaches-to `edit_link` hook.
 * @attaches-to `delete_link` hook.
 *
 * @since 150422 Rewrite.
 *
 * @return int Total files cleared by this routine (if any).
 *
 * @note This is also called upon during plugin activation.
 */
$self->autoClearCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoClearCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($self->disableAutoClearCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    $counter += $self->clearCache();

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes. Found %2$s in the cache for this site; auto-clearing.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Automatically purges all cache files (current host).
 *
 * @attaches-to Nothing at this time.
 *
 * @since 151002 While working on directory stats.
 *
 * @return int Total files purged by this routine.
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently attached to any hooks.
 */
$self->autoPurgeCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoPurgeCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($self->disableAutoPurgeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    $counter += $self->purgeCache();

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes. Found %2$s in the cache for this site that were expired; auto-purging.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Automatically wurges all cache files.
 *
 * @attaches-to Nothing at this time.
 *
 * @since 151002 While working on directory stats.
 *
 * @return int Total files wurged by this routine.
 *
 * @note Unlike many of the other `auto_` methods, this one is NOT currently attached to any hooks.
 */
$self->autoWurgeCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoWurgeCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if ($self->disableAutoPurgeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    $counter += $self->wurgeCache();

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes. Found %2$s in the cache that were expired; auto-purging.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Allows a site owner to disable the automatic cache wiping routines.
 *
 * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_wipe_cache_routines` to return TRUE,
 *    in which case this method returns TRUE, otherwise it returns FALSE.
 *
 * @since 150422 Rewrite.
 *
 * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
 */
$self->disableAutoWipeCacheRoutines = function () use ($self) {
    $is_disabled = (boolean) $self->applyWpFilters(GLOBAL_NS.'_disable_auto_wipe_cache_routines', false);

    if ($is_disabled && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueMainNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected significant changes that would normally trigger cache wiping routines. However, cache wiping routines have been disabled by a site administrator. [<a href="http://zencache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'zencache'), esc_html(NAME)));
    }
    return $is_disabled;
};

/*
 * Allows a site owner to disable the automatic cache clearing routines.
 *
 * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_clear_cache_routines` to return TRUE,
 *    in which case this method returns TRUE, otherwise it returns FALSE.
 *
 * @since 150422 Rewrite.
 *
 * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
 */
$self->disableAutoClearCacheRoutines = function () use ($self) {
    $is_disabled = (boolean) $self->applyWpFilters(GLOBAL_NS.'_disable_auto_clear_cache_routines', false);

    if ($is_disabled && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueMainNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes that would normally trigger cache clearing routines. However, cache clearing routines have been disabled by a site administrator. [<a href="http://zencache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'zencache'), esc_html(NAME)));
    }
    return $is_disabled;
};

/*
 * Allows a site owner to disable the automatic cache purging routines.
 *
 * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_purge_cache_routines` to return TRUE,
 *    in which case this method returns TRUE, otherwise it returns FALSE.
 *
 * @since 151002 While working on directory stats.
 *
 * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
 */
$self->disableAutoPurgeCacheRoutines = function () use ($self) {
    $is_disabled = (boolean) $self->applyWpFilters(GLOBAL_NS.'_disable_auto_purge_cache_routines', false);

    if ($is_disabled && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueMainNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes that would normally trigger cache purging routines. However, cache purging routines have been disabled by a site administrator. [<a href="http://zencache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'zencache'), esc_html(NAME)));
    }
    return $is_disabled;
};
