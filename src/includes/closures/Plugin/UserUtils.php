<?php
namespace WebSharks\ZenCache;

/*
 * Current user can clear the cache?
 *
 * @since 151002 Enhancing user permissions.
 *
 * @return boolean Current user can clear the cache?
 */
$self->currentUserCanClearCache = function () use ($self) {
    if (!is_null($can = &$self->cacheKey('currentUserCanClearCache'))) {
        return $can; // Already cached this.
    }
    $is_multisite = is_multisite();

    if (!$is_multisite && current_user_can($self->cap)) {
        return ($can = true); // Plugin admin.
    }
    if ($is_multisite && current_user_can($self->network_cap)) {
        return ($can = true); // Plugin admin.
    }
    
    return ($can = false);
};
$self->currentUserCanWipeCache = $self->currentUserCanClearCache;

/*
 * Current user can clear the opcache?
 *
 * @since 151114 Enhancing user permissions.
 *
 * @return boolean Current user can clear the opcache?
 */
$self->currentUserCanClearOpCache = function () use ($self) {
    if (!is_null($can = &$self->cacheKey('currentUserCanClearOpCache'))) {
        return $can; // Already cached this.
    }
    $is_multisite = is_multisite();

    if (!$is_multisite && current_user_can($self->cap)) {
        return ($can = true); // Plugin admin.
    }
    if ($is_multisite && current_user_can($self->network_cap)) {
        return ($can = true); // Plugin admin.
    }
    return ($can = false);
};
$self->currentUserCanWipeOpCache = $self->currentUserCanClearOpCache;

/*
 * Current user can clear the CDN cache?
 *
 * @since 151114 Enhancing user permissions.
 *
 * @return boolean Current user can clear the CDN cache?
 */
$self->currentUserCanClearCdnCache = function () use ($self) {
    if (!is_null($can = &$self->cacheKey('currentUserCanClearCdnCache'))) {
        return $can; // Already cached this.
    }
    $is_multisite = is_multisite();

    if (!$is_multisite && current_user_can($self->cap)) {
        return ($can = true); // Plugin admin.
    }
    if ($is_multisite && current_user_can($self->network_cap)) {
        return ($can = true); // Plugin admin.
    }
    return ($can = false);
};
$self->currentUserCanWipeCdnCache = $self->currentUserCanClearCdnCache;

/*
* Current user can clear expired transients?
*
* @since 151220 Enhancing user permissions.
*
* @return boolean Current user can clear expired transients?
*/
$self->currentUserCanClearExpiredTransients = function () use ($self) {
    if (!is_null($can = &$self->cacheKey('currentUserCanClearExpiredTransients'))) {
        return $can; // Already cached this.
    }
    $is_multisite = is_multisite();

    if (!$is_multisite && current_user_can($self->cap)) {
        return ($can = true); // Plugin admin.
    }
    if ($is_multisite && current_user_can($self->network_cap)) {
        return ($can = true); // Plugin admin.
    }
    return ($can = false);
};
$self->currentUserCanWipeExpiredTransients = $self->currentUserCanClearExpiredTransients;

/*
 * Current user can see stats?
 *
 * @since 151002 Enhancing user permissions.
 *
 * @return boolean Current user can see stats?
 */
$self->currentUserCanSeeStats = function () use ($self) {
    if (!is_null($can = &$self->cacheKey('currentUserCanSeeStats'))) {
        return $can; // Already cached this.
    }
    $is_multisite = is_multisite();

    if (!$is_multisite && current_user_can($self->cap)) {
        return ($can = true); // Plugin admin.
    }
    if ($is_multisite && current_user_can($self->network_cap)) {
        return ($can = true); // Plugin admin.
    }
    
    return ($can = false);
};
