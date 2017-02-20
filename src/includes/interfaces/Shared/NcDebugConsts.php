<?php
namespace WebSharks\CometCache\Interfaces\Shared;

interface NcDebugConsts
{
    /**
     * No-cache because of the current {@link \PHP_SAPI}.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_PHP_SAPI_CLI = 'nc_debug_php_sapi_cli';

    /**
     * No-cache because of a missing http host.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_NO_SERVER_HTTP_HOST = 'nc_debug_no_server_http_host';

    /**
     * No-cache because of a missing `$_SERVER['REQUEST_URI']`.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_NO_SERVER_REQUEST_URI = 'nc_debug_no_server_request_uri';

    /**
     * No-cache because the {@link \COMET_CACHE_ALLOWED} constant says not to.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_COMET_CACHE_ALLOWED_CONSTANT = 'nc_debug_comet_cache_allowed_constant';

    /**
     * No-cache because the `$_SERVER['COMET_CACHE_ALLOWED']` environment variable says not to.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_COMET_CACHE_ALLOWED_SERVER_VAR = 'nc_debug_comet_cache_allowed_server_var';

    /**
     * No-cache because the {@link \DONOTCACHEPAGE} constant says not to.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_DONOTCACHEPAGE_CONSTANT = 'nc_debug_donotcachepage_constant';

    /**
     * No-cache because the `$_SERVER['DONOTCACHEPAGE']` environment variable says not to.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_DONOTCACHEPAGE_SERVER_VAR = 'nc_debug_donotcachepage_server_var';

    /**
     * No-cache because it's an `XMLRPC_REQUEST`.
     *
     * @since 170220 Enhancing compatibility with API requests.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_XMLRPC_REQUEST_CONSTANT = 'nc_debug_xmlrpc_request_constant';

    /**
     * No-cache because it's a `REST_REQUEST`.
     *
     * @since 170220 Enhancing compatibility with API requests.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_REST_REQUEST_CONSTANT = 'nc_debug_rest_request_constant';

    /**
     * No-cache because the current request includes the `?[SHORT_NAME]AC=0` parameter.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_AC_GET_VAR = 'nc_debug_ac_get_var';

    /**
     * No-cache because the current request method is `POST|PUT|DELETE`.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_UNCACHEABLE_REQUEST = 'nc_debug_post_put_del_request';

    /**
     * No-cache because the current request originated from the server itself.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_SELF_SERVE_REQUEST = 'nc_debug_self_serve_request';

    /**
     * No-cache because the current request is for a feed.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_FEED_REQUEST = 'nc_debug_feed_request';

    /**
     * No-cache because the current request is systematic.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_WP_SYSTEMATICS = 'nc_debug_wp_systematics';

    /**
     * No-cache because the current request is for an administrative area.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_WP_ADMIN = 'nc_debug_wp_admin';

    /**
     * No-cache because the current request is multisite `/files/`.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_MS_FILES = 'nc_debug_ms_files';

    /**
     * No-cache because the current user is like a logged-in user.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_IS_LIKE_LOGGED_IN_USER = 'nc_debug_is_like_logged_in_user';

    /**
     * No-cache because the current user is logged into the site.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_IS_LOGGED_IN_USER = 'nc_debug_is_logged_in_user';

    /**
     * No-cache because the current user is logged into the site and the current page contains an `nonce`.
     *
     * @since 151220 Enhancing logged-in user caching support.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_IS_LOGGED_IN_USER_NONCE = 'nc_debug_is_logged_in_user_nonce';

    /**
     * No-cache because the current page contains an `nonce`.
     *
     * @since 151220 Enhancing `nonce` detection.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_PAGE_CONTAINS_NONCE = 'nc_debug_page_contains_nonce';

    /**
     * No-cache because it was not possible to acquire a user token.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_NO_USER_TOKEN = 'nc_debug_no_user_token';

    /**
     * No-cache because the current request contains a query string.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_GET_REQUEST_QUERIES = 'nc_debug_get_request_queries';

    /**
     * No-cache because it's a preview.
     *
     * @since 151114 Adding support for preview detection.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_PREVIEW = 'nc_debug_preview';

    /**
     * No-cache because the current request is excluded by its hostname.
     *
     * @since 160706 Host exclusions.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_EXCLUDED_HOSTS = 'nc_debug_excluded_hosts';

    /**
     * No-cache because the current request excluded by its URI.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_EXCLUDED_URIS = 'nc_debug_excluded_uris';

    /**
     * No-cache because the current user-agent is excluded.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_EXCLUDED_AGENTS = 'nc_debug_excluded_agents';

    /**
     * No-cache because the current HTTP referrer is excluded.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_EXCLUDED_REFS = 'nc_debug_excluded_refs';

    /**
     * No-cache because the current request is a 404 error.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_404_REQUEST = 'nc_debug_404_request';

    /**
     * No-cache because the requested page is currently in maintenance mode.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_MAINTENANCE_PLUGIN = 'nc_debug_maintenance_plugin';

    /**
     * No-cache because the current request is being compressed by an incompatible ZLIB coding type.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_OB_ZLIB_CODING_TYPE = 'nc_debug_ob_zlib_coding_type';

    /**
     * No-cache because the current request resulted in a WP error message.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_WP_ERROR_PAGE = 'nc_debug_wp_error_page';

    /**
     * No-cache because the current request is serving an uncacheable content type.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_UNCACHEABLE_CONTENT_TYPE = 'nc_debug_uncacheable_content_type';

    /**
     * No-cache because the current request sent a non-2xx & non-404 status code.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_UNCACHEABLE_STATUS = 'nc_debug_uncacheable_status';

    /**
     * No-cache because this is a new 404 error that we are symlinking.
     *
     * @since 140422 First documented version.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_1ST_TIME_404_SYMLINK = 'nc_debug_1st_time_404_symlink';

    /**
     * No-cache because we detected an early buffer termination.
     *
     * @since 140605 Improving output buffer.
     *
     * @type string A unique string identifier in the set of `NC_DEBUG_` constants.
     */
    const NC_DEBUG_EARLY_BUFFER_TERMINATION = 'nc_debug_early_buffer_termination';
}
