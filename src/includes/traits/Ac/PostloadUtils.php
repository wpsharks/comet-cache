<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait PostloadUtils
{
    /**
     * Have we caught the main WP loaded being loaded yet?
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if main query has been loaded; else `FALSE`.
     *
     * @see wpMainQueryPostload()
     */
    public $is_wp_loaded_query = false;

    /**
     * Is the current request a WordPress 404 error?
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if is a 404 error; else `FALSE`.
     *
     * @see wpMainQueryPostload()
     */
    public $is_404 = false;

    /**
     * Last HTTP status code passed through {@link \status_header}.
     *
     * @since 150422 Rewrite.
     *
     * @type int Last HTTP status code (if applicable).
     *
     * @see maybeFilterStatusHeaderPostload()
     */
    public $http_status = 0;

    /**
     * Is the current request a WordPress content type?
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if is a WP content type.
     *
     * @see wpMainQueryPostload()
     */
    public $is_a_wp_content_type = false;

    /**
     * Current WordPress {@link \content_url()}.
     *
     * @since 150422 Rewrite.
     *
     * @type string Current WordPress {@link \content_url()}.
     *
     * @see wpMainQueryPostload()
     */
    public $content_url = '';

    /**
     * Flag for {@link \is_user_loged_in()}.
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if {@link \is_user_loged_in()} else `FALSE`.
     *
     * @see wpMainQueryPostload()
     */
    public $is_user_logged_in = false;

    /**
     * Flag for {@link \is_maintenance()}.
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if {@link \is_maintenance()} else `FALSE`.
     *
     * @see wpMainQueryPostload()
     */
    public $is_maintenance = false;

    /**
     * Array of data targeted at the postload phase.
     *
     * @since 150422 Rewrite.
     *
     * @type array Data and/or flags that work with various postload handlers.
     */
    public $postload = [
        
        'filter_status_header' => true,
        'wp_main_query'        => true,
        'set_debug_info'       => COMET_CACHE_DEBUGGING_ENABLE,
    ];

    

    

    

    

    /**
     * Filters WP {@link \status_header()} (if applicable).
     *
     * @since 150422 Rewrite.
     */
    public function maybeFilterStatusHeaderPostload()
    {
        if (empty($this->postload['filter_status_header'])) {
            return; // Nothing to do in this case.
        }

        add_filter(
            'status_header',
            function ($status_header, $status_code) {
                if ($status_code > 0) {
                    $this->http_status = (int) $status_code;
                }
                return $status_header;
            },
            PHP_INT_MAX,
            2
        );
    }

    /**
     * Hooks `NC_DEBUG_` info into the WordPress `shutdown` phase (if applicable).
     *
     * @since 150422 Rewrite.
     */
    public function maybeSetDebugInfoPostload()
    {
        if (!COMET_CACHE_DEBUGGING_ENABLE) {
            return; // Nothing to do.
        }
        if (empty($this->postload['set_debug_info'])) {
            return; // Nothing to do in this case.
        }
        if (is_admin()) {
            return; // Not applicable.
        }
        if (strcasecmp(PHP_SAPI, 'cli') === 0) {
            return; // Let's not run the risk here.
        }
        add_action('shutdown', [$this, 'maybeEchoNcDebugInfo'], PHP_INT_MAX - 10);
    }

    /**
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
    public function wpMainQueryPostload()
    {
        if (empty($this->postload['wp_main_query'])) {
            return; // Nothing to do in this case.
        }
        if ($this->is_wp_loaded_query || is_admin()) {
            return; // Nothing to do.
        }
        if (!is_main_query()) {
            return; // Not main query.
        }
        $this->is_wp_loaded_query = true;
        $this->is_404             = is_404();
        $this->is_user_logged_in  = is_user_logged_in();
        $this->content_url        = rtrim(content_url(), '/');
        $this->is_maintenance     = $this->functionIsPossible('is_maintenance') && is_maintenance();

        add_action(
            'template_redirect',
            function () {
                $this->is_a_wp_content_type = $this->is_404 || $this->is_maintenance
                                               || is_front_page() // See <https://core.trac.wordpress.org/ticket/21602#comment:7>
                                               || is_home() || is_singular() || is_archive() || is_post_type_archive() || is_tax() || is_search() || is_feed();
            },
            11
        );
    }
}
