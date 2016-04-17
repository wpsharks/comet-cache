<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait BbPressUtils
{
    /**
     * Is bbPress active?
     *
     * @since 150821 Improving bbPress support.
     *
     * @return bool `TRUE` if bbPress is active.
     */
    public function isBbPressActive()
    {
        return class_exists('bbPress');
    }

    /**
     * bbPress post types.
     *
     * @since 150821 Improving bbPress support.
     *
     * @return array All bbPress post types.
     */
    public function bbPressPostTypes()
    {
        if (!$this->isBbPressActive()) {
            return [];
        }
        if (!is_null($types = &$this->cacheKey('bbPressPostTypes'))) {
            return $types; // Already did this.
        }
        $types   = []; // Initialize.
        $types[] = bbp_get_forum_post_type();
        $types[] = bbp_get_topic_post_type();
        $types[] = bbp_get_reply_post_type();

        return $types;
    }

    /**
     * bbPress post statuses.
     *
     * @since 150821 Improving bbPress support.
     *
     * @return array All bbPress post statuses.
     */
    public function bbPressStatuses()
    {
        if (!$this->isBbPressActive()) {
            return [];
        }
        if (!is_null($statuses = &$this->cacheKey('bbPressStatuses'))) {
            return $statuses; // Already did this.
        }
        $statuses = []; // Initialize.

        foreach (get_post_stati([], 'objects') as $_key => $_status) {
            if (isset($_status->label_count['domain']) && $_status->label_count['domain'] === 'bbpress') {
                $statuses[] = $_status->name;
            }
        }
        unset($_key, $_status); // Housekeeping.

        return $statuses;
    }
}
