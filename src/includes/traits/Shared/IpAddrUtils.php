<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait IpAddrUtils
{
    /**
     * Get the current visitor's real IP address.
     *
     * @since 150422 Rewrite.
     *
     * @return string Real IP address, else `unknown` on failure.
     *
     * @note This supports both IPv4 and IPv6 addresses.
     * @note See my tests against this here: http://3v4l.org/fVWUp
     */
    public function currentIp()
    {
        if (!is_null($ip = &$this->staticKey('currentIp'))) {
            return $ip; // Already cached this.
        }
        $sources = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_VIA',
            'REMOTE_ADDR',
        ];
        $sources = $this->applyFilters(GLOBAL_NS.'\\share::current_ip_sources', $sources);
        $sources = $this->applyFilters(GLOBAL_NS.'_current_ip_sources', $sources);

        $prioritize_remote_addr = false; // Off by default; can be filtered however.
        $prioritize_remote_addr = $this->applyFilters(GLOBAL_NS.'\\share::current_ip_prioritize_remote_addr', $prioritize_remote_addr);
        $prioritize_remote_addr = $this->applyFilters(GLOBAL_NS.'_current_ip_prioritize_remote_addr', $prioritize_remote_addr);

        if (!empty($_SERVER['REMOTE_ADDR']) && $prioritize_remote_addr) {
            if (($_valid_public_ip = $this->validPublicIp((string) $_SERVER['REMOTE_ADDR']))) {
                return $ip = $_valid_public_ip;
            }
            unset($_valid_public_ip); // Housekeeping.
        }
        foreach ($sources as $_key => $_source) {
            if (!empty($_SERVER[$_source])) {
                if (($_valid_public_ip = $this->validPublicIp((string) $_SERVER[$_source]))) {
                    return $ip = $_valid_public_ip;
                }
            }
            unset($_key, $_source, $_valid_public_ip); // Housekeeping.
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $ip = mb_strtolower((string) $_SERVER['REMOTE_ADDR']);
        }
        return $ip = 'unknown'; // Not possible.
    }

    /**
     * Gets a valid/public IP address.
     *
     * @since 150422 Rewrite.
     *
     * @param string $list_of_possible_ips A single IP, or a comma-delimited list of IPs.
     *
     * @return string A valid/public IP address (if one is found), else an empty string.
     *
     * @note This supports both IPv4 and IPv6 addresses.
     * @note See my tests against this here: http://3v4l.org/fVWUp
     */
    public function validPublicIp($list_of_possible_ips)
    {
        if (!$list_of_possible_ips || !is_string($list_of_possible_ips)) {
            return ''; // Empty or invalid data.
        }
        if (!($list_of_possible_ips = trim($list_of_possible_ips))) {
            return ''; // Not possible; i.e., empty string.
        }
        foreach (preg_split('/[\s;,]+/', $list_of_possible_ips, -1, PREG_SPLIT_NO_EMPTY) as $_key => $_possible_ip) {
            if (($_valid_public_ip = filter_var(mb_strtolower($_possible_ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))) {
                return $_valid_public_ip; // A valid public IPv4 or IPv6 address.
            }
        }
        unset($_key, $_possible_ip, $_valid_public_ip); // Housekeeping.

        return ''; // Default return value.
    }
}
