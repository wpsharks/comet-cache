<?php
namespace WebSharks\ZenCache;

/*
 * System load averages.
 *
 * @since 151002 Adding cache directory statistics.
 *
 * @return array System load averages.
 */
$self->sysLoadAverages = function () use ($self) {
    if (!is_null($averages = &$self->cacheKey('sysLoadAverages'))) {
        return $averages; // Already cached these.
    }
    if (!$self->functionIsPossible('sys_getloadavg')) {
        return ($averages = array());
    }
    if (!is_array($averages = sys_getloadavg()) || !$averages) {
        return ($averages = array());
    }
    $averages = array_map('floatval', $averages);
    $averages = array_slice($averages, 0, 3);
    // i.e., 1m, 5m, 15m; see: <http://jas.xyz/1gWyJLt>

    return $averages;
};

/*
 * System memory info.
 *
 * @since 151002 Adding cache directory statistics.
 *
 * @return \stdClass|boolean System memory info.
 */
$self->sysMemoryStatus = function () use ($self) {
    if (!is_null($status = &$self->cacheKey('sysMemoryStatus'))) {
        return $status; // Already cached this.
    }
    if (!$self->functionIsPossible('shell_exec')) {
        return ($status = false);
    }
    if (!($free = trim((string) @shell_exec('free')))) {
        return ($status = false);
    }
    if (!($free_lines = explode("\n", $free))) {
        return ($status = false);
    }
    if (empty($free_lines[1])) {
        return ($status = false);
    }
    if (!($memory = explode(' ', $free_lines[1]))) {
        return ($status = false);
    }
    if (!($memory = array_merge(array_filter($memory)))) {
        return ($status = false);
    }
    if (!isset($memory[1], $memory[2])) {
        return ($status = false);
    }
    if (($total = (integer) $memory[1]) <= 0) {
        return ($status = false);
    }
    $used       = (integer) $memory[2];
    $percent    = $used / $total * 100;
    $percentage = sprintf(__('%s%%', 'zencache'), number_format($percent, 2, '.', ''));
    $status     = (object) compact('total', 'used', 'percent', 'percentage');

    return $status;
};

/*
 * System opcache status/details.
 *
 * @since 151002 Adding cache directory statistics.
 *
 * @return \stdClass|boolean System opcache status/details.
 */
$self->sysOpcacheStatus = function () use ($self) {
    if (!is_null($status = &$self->cacheKey('sysOpcacheStatus'))) {
        return $status; // Already cached this.
    }
    if (!$self->functionIsPossible('opcache_get_status')) {
        return ($status = false);
    }
    if (!is_array($status = opcache_get_status(false)) || !$status) {
        return ($status = false);
    }
    if (empty($status['opcache_enabled'])) {
        return ($status = false);
    }
    return json_decode(json_encode($status));
};
