<?php
namespace WebSharks\CometCache;

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
        'display'  => __('Every 15 Minutes', 'comet-cache'),
    );
    return $schedules;
};

/*
 * Checks Cron setup, validates schedules, and reschedules events if necessary.
 *
 * @attaches-to `init` hook.
 *
 * @since 151220 Improving WP Cron setup and validation of schedules
 */
$self->checkCronSetup = function () use ($self) {
    if ($self->options['crons_setup'] < 1439005906
        || $self->options['crons_setup_on_namespace'] !== __NAMESPACE__
        || $self->options['crons_setup_with_cache_cleanup_schedule'] !== $self->options['cache_cleanup_schedule']
        || $self->options['crons_setup_on_wp_with_schedules'] !== sha1(serialize(wp_get_schedules()))
        || !wp_next_scheduled('_cron_'.GLOBAL_NS.'_cleanup')
        
    ) {

        wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_cleanup');
        wp_schedule_event(time() + 60, $self->options['cache_cleanup_schedule'], '_cron_'.GLOBAL_NS.'_cleanup');

        

        $self->updateOptions(
          array(
            'crons_setup'                             => time(),
            'crons_setup_on_namespace'                => __NAMESPACE__,
            'crons_setup_with_cache_cleanup_schedule' => $self->options['cache_cleanup_schedule'],
            'crons_setup_on_wp_with_schedules'        => sha1(serialize(wp_get_schedules()))
          )
        );
    }
};

/*
 * Resets `crons_setup` and clears WP-Cron schedules.
 *
 * @since 151220 Fixing bug with Auto-Cache Engine cron disappearing in some scenarios
 *
 * @note This MUST happen upon uninstall and deactivation due to buggy WP_Cron behavior. Events with a custom schedule will disappear when plugin is not active (see http://bit.ly/1lGdr78).
 */
$self->resetCronSetup = function ( ) use ($self) {
    if (is_multisite()) { // Main site CRON jobs.
        switch_to_blog(get_current_site()->blog_id);
        
        wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_cleanup');
        restore_current_blog(); // Restore current blog.
    } else { // Standard WP installation.
        
        wp_clear_scheduled_hook('_cron_'.GLOBAL_NS.'_cleanup');
    }
    $self->updateOptions(
      array( // Reset so that crons are rescheduled upon next activation
             'crons_setup'                             => $self->default_options['crons_setup'],
             'crons_setup_on_namespace'                => $self->default_options['crons_setup_on_namespace'],
             'crons_setup_with_cache_cleanup_schedule' => $self->default_options['crons_setup_with_cache_cleanup_schedule'],
             'crons_setup_on_wp_with_schedules'        => $self->default_options['crons_setup_on_wp_with_schedules']
      )
    );
};
