<?php
namespace WebSharks\CometCache;

/*
 * Plugin action handler.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `wp_loaded` hook.
 */
$self->actions = function () use ($self) {
    if (!empty($_REQUEST[GLOBAL_NS])) {
        new Actions();
    }
    
};
