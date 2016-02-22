<?php
namespace WebSharks\CometCache;

/*
 * Is bbPress active?
 *
 * @since 150821 Improving bbPress support.
 *
 * @return bool `TRUE` if bbPress is active.
 */
$self->isBbPressActive = function () use ($self) {
    return class_exists('bbPress');
};

/*
 * bbPress post types.
 *
 * @since 150821 Improving bbPress support.
 *
 * @return array All bbPress post types.
 */
$self->bbPressPostTypes = function () use ($self) {
    if (!$self->isBbPressActive()) {
        return array();
    }
    if (!is_null($types = &$self->cacheKey('bbPressPostTypes'))) {
        return $types; // Already did this.
    }
    $types   = array(); // Initialize.
    $types[] = bbp_get_forum_post_type();
    $types[] = bbp_get_topic_post_type();
    $types[] = bbp_get_reply_post_type();

    return $types;
};

/*
 * bbPress post statuses.
 *
 * @since 150821 Improving bbPress support.
 *
 * @return array All bbPress post statuses.
 */
$self->bbPressStatuses = function () use ($self) {
    if (!$self->isBbPressActive()) {
        return array();
    }
    if (!is_null($statuses = &$self->cacheKey('bbPressStatuses'))) {
        return $statuses; // Already did this.
    }
    $statuses = array(); // Initialize.

    foreach (get_post_stati(array(), 'objects') as $_key => $_status) {
        if (isset($_status->label_count['domain']) && $_status->label_count['domain'] === 'bbpress') {
            $statuses[] = $_status->name;
        }
    }
    unset($_key, $_status); // Housekeeping.

    return $statuses;
};
