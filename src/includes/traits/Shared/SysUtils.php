<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait SysUtils
{
    /**
     * System load averages.
     *
     * @since 151002 Adding cache directory statistics.
     *
     * @return array System load averages.
     */
    public function sysLoadAverages()
    {
        if (!is_null($averages = &$this->cacheKey('sysLoadAverages'))) {
            return $averages; // Already cached these.
        }
        if (!$this->functionIsPossible('sys_getloadavg')) {
            return $averages = [];
        }
        if (!is_array($averages = sys_getloadavg()) || !$averages) {
            return $averages = [];
        }
        $averages = array_map('floatval', $averages);
        $averages = array_slice($averages, 0, 3);
        // i.e., 1m, 5m, 15m; see: <http://jas.xyz/1gWyJLt>

        return $averages;
    }

    /**
     * System memory info.
     *
     * @since 151002 Adding cache directory statistics.
     *
     * @return \stdClass|bool System memory info.
     */
    public function sysMemoryStatus()
    {
        if (!is_null($status = &$this->cacheKey('sysMemoryStatus'))) {
            return $status; // Already cached this.
        }
        if (!$this->functionIsPossible('shell_exec')) {
            return $status = false;
        }
        if (!($free = trim((string) @shell_exec('free')))) {
            return $status = false;
        }
        if (!($free_lines = explode("\n", $free))) {
            return $status = false;
        }
        if (empty($free_lines[1])) {
            return $status = false;
        }
        if (!($memory = explode(' ', $free_lines[1]))) {
            return $status = false;
        }
        if (!($memory = array_merge(array_filter($memory)))) {
            return $status = false;
        }
        if (!isset($memory[1], $memory[2])) {
            return $status = false;
        }
        if (($total = (integer) $memory[1]) <= 0) {
            return $status = false;
        }
        $used       = (integer) $memory[2];
        $percent    = $used / $total * 100;
        $percentage = sprintf(__('%s%%', 'comet-cache'), number_format($percent, 2, '.', ''));
        $status     = (object) compact('total', 'used', 'percent', 'percentage');

        return $status;
    }

    /**
     * System opcache status/details.
     *
     * @since 151002 Adding cache directory statistics.
     *
     * @return \stdClass|bool System opcache status/details.
     */
    public function sysOpcacheStatus()
    {
        if (!is_null($status = &$this->cacheKey('sysOpcacheStatus'))) {
            return $status; // Already cached this.
        }
        if (!$this->functionIsPossible('opcache_get_status')) {
            return $status = false;
        }
        if (!is_array($status = opcache_get_status(false)) || !$status) {
            return $status = false;
        }
        if (empty($status['opcache_enabled'])) {
            return $status = false;
        }
        return json_decode(json_encode($status));
    }
}
