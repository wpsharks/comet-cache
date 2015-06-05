<?php
namespace WebSharks\ZenCache;

/*
 * Extends WP-Cron schedules.
 *
 * @since 150422 Rewrite.
 *
 * @attaches-to `cron_schedules` filter.
 *
 * @param array $schedules An array of the current schedules.
 *
 * @return array Revised array of WP-Cron schedules.
 */
$self->extendCronSchedules = function ($schedules) use ($self) {
    $schedules['every15m'] = array(
        'interval' => 900,
        'display'  => __('Every 15 Minutes', SLUG_TD),
    );
    return $schedules;
};
