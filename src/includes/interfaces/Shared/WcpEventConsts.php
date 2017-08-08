<?php
namespace WebSharks\CometCache\Interfaces\Shared;

interface WcpEventConsts
{
    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_POST_SAVED = 'wcp_event_post_saved';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_POST_UPDATED = 'wcp_event_post_updated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_POST_DELETED = 'wcp_event_post_deleted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_POST_CACHE_CLEANED = 'wcp_event_post_cache_cleaned';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_POST_CACHE_CLEANED = 'wcp_event_post_cache_cleaned';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_TERM_RELATIONSHIP_ADDED = 'wcp_event_term_relationship_added';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_TERM_RELATIONSHIP_DELETED = 'wcp_event_term_relationship_deleted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_PLUGIN_ACTIVATED = 'wcp_event_plugin_activated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_PLUGIN_DEACTIVATED = 'wcp_event_plugin_deactivated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_PLUGIN_INSTALLED_OR_UPDATED = 'wcp_event_plugin_installed_or_updated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_THEME_INSTALLED_OR_UPDATED = 'wcp_event_theme_installed_or_updated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_GENERAL_OPTIONS_CHANGED = 'wcp_event_general_options_changed';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_READING_OPTIONS_CHANGED = 'wcp_event_reading_options_changed';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_DISCUSSION_OPTIONS_CHANGED = 'wcp_event_discussion_options_changed';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_PERMALINK_OPTIONS_CHANGED = 'wcp_event_permalink_options_changed';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_JETPACK_CSS_CHANGED = 'wcp_event_jetpack_css_changed';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_TRACKBACK_POSTED = 'wcp_event_trackback_posted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_PINGBACK_POSTED = 'wcp_event_pingback_posted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_COMMENT_POSTED = 'wcp_event_comment_posted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_POST_REQUEST = 'wcp_event_user_post_request';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_PROFILE_UPDATED = 'wcp_event_user_profile_updated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_METADATA_ADDED = 'wcp_event_user_metadata_added';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_METADATA_UPDATED = 'wcp_event_user_metadata_updated';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_METADATA_DELETED = 'wcp_event_user_metadata_deleted';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_AUTH_COOKIE_SET = 'wcp_event_user_auth_cookie_set';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_USER_AUTH_COOKIE_CLEARED = 'wcp_event_user_auth_cookie_cleared';

    /**
     * WCP event/reason constant.
     *
     * @since 17xxxx WCP event constants.
     *
     * @type string A unique string identifier in the set of `WCP_EVENT_` constants.
     */
    const WCP_EVENT_URL_CLEARED_BY_USER = 'wcp_event_url_cleared_by_user';
}
