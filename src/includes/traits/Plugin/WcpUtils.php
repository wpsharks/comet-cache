<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpUtils
{
    /**
     * Used for temporarily storing the permalink for posts transitioning from
     *    `publish` or `private` post status to `pending` or `draft` post status.
     *
     * @since 150422 Rewrite.
     *
     * @type array An associative array with the Post ID as the named key containing
     *            the post permalink before the post has been transitioned.
     */
    public $pre_post_update_post_permalink = [];

    /**
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
    public function wipeCache($manually = false)
    {
        $counter = 0; // Initialize.

        if (!$manually && $this->disableAutoWipeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        @set_time_limit(1800); // @TODO Display a warning.

        if (is_dir($cache_dir = $this->cacheDir())) {
            $regex = $this->assembleCachePathRegex('', '.+');
            $counter += $this->wipeFilesFromCacheDir($regex);
        }
        

        

        
        return $counter;
    }

    // @codingStandardsIgnoreStart
    /*
    * Back compat. alias for autoClearUserCache()
    */
    public function wipe_cache()
    { // @codingStandardsIgnoreEnd
        return call_user_func_array([$this, 'wipeCache'], func_get_args());
    }

    /**
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
    public function clearCache($manually = false)
    {
        $counter = 0; // Initialize.

        if (!$manually && $this->disableAutoClearCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        @set_time_limit(1800); // @TODO Display a warning.

        if (is_dir($cache_dir = $this->cacheDir())) {
            $regex = $this->buildHostCachePathRegex('', '.+');
            $counter += $this->clearFilesFromHostCacheDir($regex);
        }
        

        

        
        return $counter;
    }

    // @codingStandardsIgnoreStart
    /*
    * Back compat. alias for clearCache()
    */
    public function clear_cache()
    { // @codingStandardsIgnoreEnd
        return call_user_func_array([$this, 'clearCache'], func_get_args());
    }

    /**
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
    public function purgeCache($manually = false)
    {
        $counter = 0; // Initialize.

        if (!$manually && $this->disableAutoPurgeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        @set_time_limit(1800); // @TODO Display a warning.

        if (is_dir($cache_dir = $this->cacheDir())) {
            $regex = $this->buildHostCachePathRegex('', '.+');
            $counter += $this->purgeFilesFromHostCacheDir($regex);
        }
        
        return $counter;
    }

    // @codingStandardsIgnoreStart
    /*
    * Back compat. alias for purgeCache()
    */
    public function purge_cache()
    { // @codingStandardsIgnoreEnd
        return call_user_func_array([$this, 'purgeCache'], func_get_args());
    }

    /**
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
    public function wurgeCache($manually = false)
    {
        $counter = 0; // Initialize.

        if (!$manually && $this->disableAutoPurgeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        @set_time_limit(1800); // @TODO Display a warning.

        if (is_dir($cache_dir = $this->cacheDir())) {
            $regex = $this->assembleCachePathRegex('', '.+');
            $counter += $this->wurgeFilesFromCacheDir($regex);
        }
        
        return $counter;
    }

    /**
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
    public function autoWipeCache()
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoWipeCache'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if ($this->disableAutoWipeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        $counter += $this->wipeCache();

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Detected significant changes that require a full wipe of the cache. Found %1$s in the cache; auto-wiping.', 'comet-cache'), esc_html($this->i18nFiles($counter))), ['combinable' => true]);
        }
        return $counter;
    }

    /**
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
    public function autoClearCache()
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoClearCache'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if ($this->disableAutoClearCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        $counter += $this->clearCache();

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Detected important site changes that affect the entire cache. Found %1$s in the cache for this site; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($counter))), ['combinable' => true]);
        }
        return $counter;
    }

    /**
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
    public function autoPurgeCache()
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoPurgeCache'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if ($this->disableAutoPurgeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        $counter += $this->purgeCache();

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Detected important site changes. Found %1$s in the cache for this site that were expired; auto-purging.', 'comet-cache'), esc_html($this->i18nFiles($counter))), ['combinable' => true]);
        }
        return $counter;
    }

    /**
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
    public function autoWurgeCache()
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoWurgeCache'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if ($this->disableAutoPurgeCacheRoutines()) {
            return $counter; // Nothing to do.
        }
        $counter += $this->wurgeCache();

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Detected important site changes. Found %1$s in the cache that were expired; auto-purging.', 'comet-cache'), esc_html($this->i18nFiles($counter))), ['combinable' => true]);
        }
        return $counter;
    }

    /**
     * Allows a site owner to disable the automatic cache wiping routines.
     *
     * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_wipe_cache_routines` to return TRUE,
     *    in which case this method returns TRUE, otherwise it returns FALSE.
     *
     * @since 150422 Rewrite.
     *
     * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
     */
    public function disableAutoWipeCacheRoutines()
    {
        $is_disabled = (boolean) $this->applyWpFilters(GLOBAL_NS.'_disable_auto_wipe_cache_routines', false);

        if ($is_disabled && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueMainNotice(
                '<img src="'.esc_attr($this->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                sprintf(__('<strong>%1$s:</strong> detected significant changes that would normally trigger cache wiping routines. However, cache wiping routines have been disabled by a site administrator. [<a href="http://cometcache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'comet-cache'), esc_html(NAME))
            );
        }
        return $is_disabled;
    }

    /**
     * Allows a site owner to disable the automatic cache clearing routines.
     *
     * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_clear_cache_routines` to return TRUE,
     *    in which case this method returns TRUE, otherwise it returns FALSE.
     *
     * @since 150422 Rewrite.
     *
     * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
     */
    public function disableAutoClearCacheRoutines()
    {
        $is_disabled = (boolean) $this->applyWpFilters(GLOBAL_NS.'_disable_auto_clear_cache_routines', false);

        if ($is_disabled && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueMainNotice(
                '<img src="'.esc_attr($this->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                sprintf(__('<strong>%1$s:</strong> detected important site changes that would normally trigger cache clearing routines. However, cache clearing routines have been disabled by a site administrator. [<a href="http://cometcache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'comet-cache'), esc_html(NAME))
            );
        }
        return $is_disabled;
    }

    /**
     * Allows a site owner to disable the automatic cache purging routines.
     *
     * This is done by filtering `'.__GLOBAL_NS__.'_disable_auto_purge_cache_routines` to return TRUE,
     *    in which case this method returns TRUE, otherwise it returns FALSE.
     *
     * @since 151002 While working on directory stats.
     *
     * @return bool `TRUE` if disabled; and this also creates a dashboard notice in some cases.
     */
    public function disableAutoPurgeCacheRoutines()
    {
        $is_disabled = (boolean) $this->applyWpFilters(GLOBAL_NS.'_disable_auto_purge_cache_routines', false);

        if ($is_disabled && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueMainNotice(
                '<img src="'.esc_attr($this->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                sprintf(__('<strong>%1$s:</strong> detected important site changes that would normally trigger cache purging routines. However, cache purging routines have been disabled by a site administrator. [<a href="http://cometcache.com/r/kb-clear-and-wipe-cache-routines/" target="_blank">?</a>]', 'comet-cache'), esc_html(NAME))
            );
        }
        return $is_disabled;
    }
}
