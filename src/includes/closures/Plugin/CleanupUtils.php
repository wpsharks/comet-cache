<?php
namespace WebSharks\CometCache;

/*
 * Runs cleanup routine via CRON job.
 *
 * @since 151002 While working on directory stats.
 *
 * @attaches-to `'_cron_'.__GLOBAL_NS__.'_cleanup'`
 */
$self->cleanupCache = function () use ($self) {
    if (!$self->options['enable']) {
        return; // Nothing to do.
    }
    

    
    $self->wurgeCache(); // Purge now.
};
