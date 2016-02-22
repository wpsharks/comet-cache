<?php
namespace WebSharks\CometCache;

/*
 * Have we caught the main WP loaded being loaded yet?
 *
 * @since 150422 Rewrite.
 *
 * @type bool `TRUE` if main query has been loaded; else `FALSE`.
 *
 * @see wpMainQueryPostload()
 */
$self->is_wp_loaded_query = false;

/*
 * Is the current request a WordPress 404 error?
 *
 * @since 150422 Rewrite.
 *
 * @type bool `TRUE` if is a 404 error; else `FALSE`.
 *
 * @see wpMainQueryPostload()
 */
$self->is_404 = false;

/*
 * Last HTTP status code passed through {@link \status_header}.
 *
 * @since 150422 Rewrite.
 *
 * @type int Last HTTP status code (if applicable).
 *
 * @see maybeFilterStatusHeaderPostload()
 */
$self->http_status = 0;

/*
 * Is the current request a WordPress content type?
 *
 * @since 150422 Rewrite.
 *
 * @type bool `TRUE` if is a WP content type.
 *
 * @see wpMainQueryPostload()
 */
$self->is_a_wp_content_type = false;

/*
 * Current WordPress {@link \content_url()}.
 *
 * @since 150422 Rewrite.
 *
 * @type string Current WordPress {@link \content_url()}.
 *
 * @see wpMainQueryPostload()
 */
$self->content_url = '';

/*
 * Flag for {@link \is_user_loged_in()}.
 *
 * @since 150422 Rewrite.
 *
 * @type bool `TRUE` if {@link \is_user_loged_in()}; else `FALSE`.
 *
 * @see wpMainQueryPostload()
 */
$self->is_user_logged_in = false;

/*
 * Flag for {@link \is_maintenance()}.
 *
 * @since 150422 Rewrite.
 *
 * @type bool `TRUE` if {@link \is_maintenance()}; else `FALSE`.
 *
 * @see wpMainQueryPostload()
 */
$self->is_maintenance = false;

/*
 * Array of data targeted at the postload phase.
 *
 * @since 150422 Rewrite.
 *
 * @type array Data and/or flags that work with various postload handlers.
 */
$self->postload = array(
    
    'filter_status_header' => true,
    'wp_main_query'        => true,
    'set_debug_info'       => COMET_CACHE_DEBUGGING_ENABLE,
);









/*
 * Filters WP {@link \status_header()} (if applicable).
 *
 * @since 150422 Rewrite.
 */
$self->maybeFilterStatusHeaderPostload = function () use ($self) {
    if (empty($self->postload['filter_status_header'])) {
        return; // Nothing to do in this case.
    }
    $_self = $self; // Reference needed below.

    add_filter(
        'status_header',
        function ($status_header, $status_code) use ($_self) {
            if ($status_code > 0) {
                $_self->http_status = (integer) $status_code;
            }
            return $status_header;
        },
        PHP_INT_MAX,
        2
    );
};

/*
 * Hooks `NC_DEBUG_` info into the WordPress `shutdown` phase (if applicable).
 *
 * @since 150422 Rewrite.
 */
$self->maybeSetDebugInfoPostload = function () use ($self) {
    if (!COMET_CACHE_DEBUGGING_ENABLE) {
        return; // Nothing to do.
    }
    if (empty($self->postload['set_debug_info'])) {
        return; // Nothing to do in this case.
    }
    if (is_admin()) {
        return; // Not applicable.
    }
    if (strcasecmp(PHP_SAPI, 'cli') === 0) {
        return; // Let's not run the risk here.
    }
    add_action('shutdown', array($self, 'maybeEchoNcDebugInfo'), PHP_INT_MAX - 10);
};

/*
 * Grab details from WP and the Comet Cache plugin itself,
 *    after the main query is loaded (if at all possible).
 *
 * This is where we have a chance to grab any values we need from WordPress; or from the CC plugin.
 *    It is EXTREMEMLY important that we NOT attempt to grab any object references here.
 *    Anything acquired in this phase should be stored as a scalar value.
 *    See {@link outputBufferCallbackHandler()} for further details.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `wp` hook.
 */
$self->wpMainQueryPostload = function () use ($self) {
    if (empty($self->postload['wp_main_query'])) {
        return; // Nothing to do in this case.
    }
    if ($self->is_wp_loaded_query || is_admin()) {
        return; // Nothing to do.
    }
    if (!is_main_query()) {
        return; // Not main query.
    }
    $self->is_wp_loaded_query = true;
    $self->is_404             = is_404();
    $self->is_user_logged_in  = is_user_logged_in();
    $self->content_url        = rtrim(content_url(), '/');
    $self->is_maintenance     = $self->functionIsPossible('is_maintenance') && is_maintenance();
    $_self                    = $self; // Reference for the closure below.

    add_action(
        'template_redirect',
        function () use ($_self) {
            $_self->is_a_wp_content_type = $_self->is_404 || $_self->is_maintenance
            || is_front_page() // See <https://core.trac.wordpress.org/ticket/21602#comment:7>
            || is_home() || is_singular() || is_archive() || is_post_type_archive() || is_tax() || is_search() || is_feed();
        },
        11
    );
};
