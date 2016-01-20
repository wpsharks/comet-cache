<?php
namespace WebSharks\ZenCache;

/*
 * Wipe (i.e., reset) OPCache.
 *
 * @since 151002 Adding OPCache support.
 *
 * @param bool $manually True if wiping is done manually.
 * @param boolean $maybe Defaults to a true value.
 * @param array $files Optional; wipe only specific files?
 *
 * @return integer Total keys wiped.
 */
$self->wipeOpcache = function ($manually = false, $maybe = true, $files = array()) use ($self) {
    $counter = 0; // Initialize counter.

    if ($maybe && !$self->options['cache_clear_opcache_enable']) {
        return $counter; // Not enabled at this time.
    }
    if (!$self->functionIsPossible('opcache_reset')) {
        return $counter; // Not possible.
    }
    if (!($status = $self->sysOpcacheStatus())) {
        return $counter; // Not possible.
    }
    if (empty($status->opcache_enabled)) {
        return $counter; // Not necessary.
    }
    if (empty($status->opcache_statistics->num_cached_keys)) {
        return $counter; // Not possible.
    }
    if ($files) { // Specific files?
        foreach ($files as $_file) {
            $counter += (int) opcache_invalidate($_file, true);
        } // unset($_file); // Housekeeping.
    } elseif (opcache_reset()) { // True if a reset occurs.
        $counter += $status->opcache_statistics->num_cached_keys;
    }
    return $counter;
};

/*
 * Clear (i.e., reset) OPCache.
 *
 * @since 151002 Adding OPCache support.
 *
 * @param bool $manually True if clearing is done manually.
 * @param boolean $maybe Defaults to a true value.
 *
 * @return integer Total keys cleared.
 */
$self->clearOpcache = function ($manually = false, $maybe = true) use ($self) {
    if (!is_multisite() || is_main_site() || current_user_can($self->network_cap)) {
        return $self->wipeOpcache($manually, $maybe);
    }
    return 0; // Not applicable.
};

/*
 * Clear AC class file from Opcache (by force).
 *
 * @since 151215 Adding OPCache support.
 *
 * @return integer Total keys cleared.
 */
$self->clearAcDropinFromOpcacheByForce = function () use ($self) {
    return $self->wipeOpcache(false, false, array(WP_CONTENT_DIR.'/advanced-cache.php'));
};
