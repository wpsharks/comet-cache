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
 * Wipes out all cache files in the cache directory.
 *
 * @since 150422 Rewrite.
 *
 * @param bool   $manually      Defaults to a `FALSE` value.
 *                              Pass as TRUE if the wipe is done manually by the site owner.
 * @param string $also_wipe_dir Defaults to an empty string; i.e., only wipe {@link $cache_sub_dir} files.
 *
 * @throws \Exception If a wipe failure occurs.
 *
 * @return int Total files wiped by this routine (if any).
 */
$self->wipeCache = function ($manually = false, $also_wipe_dir = '') use ($self) {
    $counter = 0; // Initialize.

    $also_wipe_dir = trim((string) $also_wipe_dir);

    if (!$manually && $self->disableAutoWipeCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    if (is_dir($cache_dir = $self->cacheDir())) {
        $counter += $self->deleteAllFilesDirsIn($cache_dir);
    }
    if ($also_wipe_dir && is_dir($also_wipe_dir)) {
        $counter += $self->deleteAllFilesDirsIn($also_wipe_dir);
    }
    

    return $counter;
};

/*
 * Clears cache files for the current host|blog.
 *
 * @since 150422 Rewrite.
 *
 * @param bool $manually Defaults to a `FALSE` value.
 *                       Pass as TRUE if the clearing is done manually by the site owner.
 *
 * @throws \Exception If a clearing failure occurs.
 *
 * @return int Total files cleared by this routine (if any).
 */
$self->clearCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!$manually && $self->disableAutoClearCacheRoutines()) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return ($counter += $self->clearHtmlCCache($manually));
    }
    @set_time_limit(1800); // @TODO Display a warning.

    $regex = $self->buildHostCachePathRegex('', '.+');
    $counter += $self->clearFilesFromHostCacheDir($regex);

    

    return $counter;
};

/*
 * Purges expired cache files for the current host|blog.
 *
 * @since 150422 Rewrite.
 *
 * @param bool $manually Defaults to a `FALSE` value.
 *                       Pass as TRUE if the purging is done manually by the site owner.
 *
 * @throws \Exception If a purge failure occurs.
 *
 * @return int Total files purged by this routine (if any).
 *
 * @attaches-to `'_cron_'.__GLOBAL_NS__.'_cleanup'` via CRON job.
 */
$self->purgeCache = function ($manually = false) use ($self) {
    $counter = 0; // Initialize.

    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    @set_time_limit(1800); // @TODO Display a warning.

    $regex = $self->buildHostCachePathRegex('', '.+');
    $counter += $self->purgeFilesFromHostCacheDir($regex);

    return $counter;
};

/*
 * Automatically wipes out all cache files in the cache directory.
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
                              sprintf(__('<strong>%1$s:</strong> detected significant changes. Found %2$s in the cache; auto-wiping.', SLUG_TD), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Automatically clears all cache files for the current blog.
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
                              sprintf(__('<strong>%1$s:</strong> detected important site changes. Found %2$s in the cache for this site; auto-clearing.', SLUG_TD), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};

/*
 * Allows a site owner to disable the wipe cache routines.
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
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected significant changes that would normally trigger a wipe cache routine, however wipe cache routines have been disabled by a site administrator. [<a href="http://zencache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', SLUG_TD), esc_html(NAME)));
    }
    return $is_disabled;
};

/*
 * Allows a site owner to disable the clear and wipe cache routines.
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
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected important site changes that would normally trigger a clear cache routine. However, clear cache routines have been disabled by a site administrator. [<a href="http://zencache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', SLUG_TD), esc_html(NAME)));
    }
    return $is_disabled;
};
