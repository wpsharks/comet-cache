<?php
namespace WebSharks\CometCache;

/*
 * Automatically clears all cache files for current blog when JetPack Custom CSS is saved.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `safecss_save_pre` hook.
 *
 * @param array $args Args passed in by hook.
 */
$self->autoClearCacheOnJetpackCustomCss = function ($args) use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoClearCacheOnJetpackCustomCss', $args))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (empty($args['is_preview']) && class_exists('\\Jetpack')) {
        $counter += $self->autoClearCache();
    }
};
