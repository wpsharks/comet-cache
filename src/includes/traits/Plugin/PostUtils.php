<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait PostUtils
{
    /**
     * All post statuses.
     *
     * @since 150821 Improving bbPress support.
     *
     * @return array All post statuses.
     */
    public function postStatuses()
    {
        if (!is_null($statuses = &$this->cacheKey('postStatuses'))) {
            return $statuses; // Already did this.
        }
        $statuses = get_post_stati();
        $statuses = array_keys($statuses);

        return $statuses;
    }

    /**
     * All built-in post statuses.
     *
     * @since 150821 Improving bbPress support.
     *
     * @return array All built-in post statuses.
     */
    public function builtInPostStatuses()
    {
        if (!is_null($statuses = &$this->cacheKey('builtInPostStatuses'))) {
            return $statuses; // Already did this.
        }
        $statuses = []; // Initialize.

        foreach (get_post_stati([], 'objects') as $_key => $_status) {
            if (!empty($_status->_builtin)) {
                $statuses[] = $_status->name;
            }
        }
        unset($_key, $_status); // Housekeeping.

        return $statuses;
    }
}
