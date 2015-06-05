<?php
namespace WebSharks\ZenCache;

/*
 * Registers a shutdown flag.
 *
 * @since 140605 Improving output buffer.
 *
 * @note In `/wp-settings.php`, ZenCache is loaded before WP registers its own shutdown function.
 * Therefore, this flag is set before {@link shutdown_action_hook()} fires, and thus before {@link wp_ob_end_flush_all()}.
 *
 * @see http://www.php.net/manual/en/function.register-shutdown-function.php
 */
$self->registerShutdownFlag = function () use ($self) {
    register_shutdown_function(function () {
        $GLOBALS[GLOBAL_NS.'_shutdown_flag'] = -1;
    });
};
