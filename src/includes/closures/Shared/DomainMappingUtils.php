<?php
namespace WebSharks\CometCache;

/*
 * Can consider domain mapping?
 *
 * @since 150821 Improving multisite compat.
 *
 * @return bool `TRUE` if we can consider domain mapping.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->canConsiderDomainMapping = function () use ($self) {
    if (!is_null($can = &$self->staticKey('canConsiderDomainMapping'))) {
        return $can; // Already cached this.
    }
    if (!$self->isAdvancedCache() && is_multisite() && $self->hostBaseToken() === '/'
    && defined('SUNRISE_LOADED') && SUNRISE_LOADED && !empty($GLOBALS['dm_domain'])) {
        return ($can = true); // Can consider.
    }
    return ($can = false); // Cannot consider.
};

/*
 * Domain mapping?
 *
 * @since 150821 Improving multisite compat.
 *
 * @return integer Domain mapping ID; else `0` (false).
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->isDomainMapping = function () use ($self) {
    if (!is_null($is = &$self->staticKey('isDomainMapping'))) {
        return $is; // Already cached this.
    }
    if (!$self->isAdvancedCache() && is_multisite() && $self->canConsiderDomainMapping()
    && defined('DOMAIN_MAPPING') && DOMAIN_MAPPING && !empty($GLOBALS['domain_mapping_id'])) {
        return ($is = (integer) $GLOBALS['domain_mapping_id']); // Blog ID.
    }
    return ($is = 0); // Not domain mapping.
};

/*
 * Filters a URL in order to apply domain mapping.
 *
 * @since 150821 Improving multisite compat.
 *
 * @param string $url The input URL to filter.
 *
 * @return string The filtered URL; else the original URL.
 *
 * @note The return value of this function is NOT cached, but inner portions are.
 */
$self->domainMappingUrlFilter = function ($url) use ($self) {
    $original_url = (string) $url; // Preserve.
    $url          = trim((string) $url);

    if (!is_multisite() || !$self->canConsiderDomainMapping()) {
        return $original_url; // Not possible.
    }
    if (!$url || !($url_parts = $self->parseUrl($url))) {
        return $original_url; // Not possible.
    }
    if (empty($url_parts['host'])) {
        return $original_url; // Not possible.
    }
    $blog_domain = strtolower($url_parts['host']); // In the unfiltered URL.
    $blog_path   = $self->hostDirToken(false, false, !empty($url_parts['path']) ? $url_parts['path'] : '/');

    if (!($blog_id = (integer) get_blog_id_from_url($blog_domain, $blog_path))) {
        return $original_url; // Not possible.
    }
    if (!($domain = $self->domainMappingBlogDomain($blog_id)) || $domain === $blog_domain) {
        return $original_url; // Not applicable.
    }
    $url_parts['host'] = $domain; // Filter the URL now.
    if (!empty($url_parts['path']) && $url_parts['path'] !== '/') {
        if (($host_base_dir_tokens = trim($self->hostBaseDirTokens(false, false, $url_parts['path']), '/'))) {
            $url_parts['path'] = preg_replace('/^\/'.preg_quote($host_base_dir_tokens, '/').'(\/|$)/i', '${1}', $url_parts['path']);
        }
    }
    return ($url = $self->unParseUrl($url_parts));
};

/*
 * Filters a URL in order to remove domain mapping.
 *
 * @since 150821 Improving multisite compat.
 *
 * @param string $url The input URL to filter.
 *
 * @return string The filtered URL; else the original URL.
 *
 * @note The return value of this function is NOT cached, but inner portions are.
 */
$self->domainMappingReverseUrlFilter = function ($url) use ($self) {
    $original_url = (string) $url; // Preserve.
    $url          = trim((string) $url);

    if (!is_multisite() || !$self->canConsiderDomainMapping()) {
        return $original_url; // Not possible.
    }
    if (!$url || !($url_parts = $self->parseUrl($url))) {
        return $original_url; // Not possible.
    }
    if (empty($url_parts['host'])) {
        return $original_url; // Not possible.
    }
    if (!($blog_id = $self->domainMappingBlogId('', $url_parts['host']))) {
        return $original_url; // No a domain in the map.
    }
    if (!($blog_details = $self->blogDetails($blog_id))) {
        return $original_url; // Not possible.
    }
    $url_parts['host'] = $blog_details->domain; // Filter the URL now.
    if (($host_base_dir_tokens = trim($self->hostBaseDirTokens(false, false, $blog_details->path), '/'))) {
        $url_parts['path'] = '/'.$host_base_dir_tokens.'/'.ltrim(@$url_parts['path'], '/');
    }
    return ($url = $self->unParseUrl($url_parts));
};

/*
 * Converts a host into a mapped blog ID.
 *
 * @since 150821 Improving multisite compat.
 *
 * @param string $url URL containing the domain to convert.
 * @param string $domain The domain to convert. Override URL is provided.
 *
 * @return integer The mapped blog ID; else `0` on failure.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->domainMappingBlogId = function ($url = '', $domain = '') use ($self) {
    $domain = (string) $domain;
    $url    = $domain ? '' : (string) $url;

    if (!is_multisite() || !$self->canConsiderDomainMapping()) {
        return 0; // Not possible/applicable.
    }
    if ($url === 'network' || $domain === 'network') {
        $domain = (string) get_current_site()->domain;
    }
    if (!$domain && $url && $url !== 'network') {
        $domain = $self->parseUrl($url, PHP_URL_HOST);
    }
    if (!$url && !$domain && ($blog_details = $self->blogDetails())) {
        $domain = $blog_details->domain;
    }
    $domain = strtolower(preg_replace('/^www\./i', '', $domain));

    if (!$domain || strpos($domain, '.') === false) {
        return 0; // Not possible.
    }
    if (!is_null($blog_id = &$self->staticKey('domainMappingBlogId', $domain))) {
        return $blog_id; // Already cached this.
    }
    $wpdb                     = $self->wpdb(); // WordPress database class.
    $suppressing_errors       = $wpdb->suppress_errors(); // In case table has not been created yet.
    $enforcing_primary_domain = !get_site_option('dm_no_primary_domain'); // Enforcing primary domain?

    if (!$enforcing_primary_domain) {
        $blog_id = (integer) $wpdb->get_var('SELECT `blog_id` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `domain` IN(\''.esc_sql('www.'.$domain).'\', \''.esc_sql($domain).'\') ORDER BY CHAR_LENGTH(`domain`) DESC, `active` DESC LIMIT 1');
    } else {
        $blog_id = (integer) $wpdb->get_var('SELECT `blog_id` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `domain` IN(\''.esc_sql('www.'.$domain).'\', \''.esc_sql($domain).'\') AND `active` = \'1\' ORDER BY CHAR_LENGTH(`domain`) DESC LIMIT 1');
    }
    $wpdb->suppress_errors($suppressing_errors); // Restore.

    return ($blog_id = (integer) $blog_id);
};

/*
 * Converts a blog ID into a mapped domain.
 *
 * @since 150821 Improving multisite compat.
 *
 * @param integer $blog_id The blog ID.
 *
 * @param boolean $fallback Fallback on blog's domain?
 *
 * @return string The mapped domain, else an empty string.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->domainMappingBlogDomain = function ($blog_id = 0, $fallback = false) use ($self) {
    if (!is_multisite() || !$self->canConsiderDomainMapping()) {
        return ''; // Not possible/applicable.
    }
    if (($blog_id = (integer) $blog_id) < 0) {
        $blog_id = (integer) get_current_site()->blog_id;
    }
    if (!$blog_id) {
        $blog_id = (integer) get_current_blog_id();
    }
    if (!$blog_id || $blog_id < 0) {
        return ''; // Not possible.
    }
    if (!is_null($domain = &$self->staticKey('domainMappingBlogDomain', $blog_id))) {
        return $domain; // Already cached this.
    }
    $wpdb                     = $self->wpdb(); // WordPress database class.
    $suppressing_errors       = $wpdb->suppress_errors(); // In case table has not been created yet.
    $enforcing_primary_domain = !get_site_option('dm_no_primary_domain'); // Enforcing primary domain?

    if (!$enforcing_primary_domain) {
        if ($self->isDomainMapping() === $blog_id) {
            $domain = $self->hostToken();
            $domain = preg_replace('/^www\./i', '', $domain);
            $domain = (string) $wpdb->get_var('SELECT `domain` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `blog_id` = \''.esc_sql($blog_id).'\' AND `domain` IN(\''.esc_sql('www.'.$domain).'\', \''.esc_sql($domain).'\') ORDER BY CHAR_LENGTH(`domain`) DESC LIMIT 1');
        } elseif (($domains = $self->domainMappingBlogDomains($blog_id))) {
            $domain = $domains[0]; // Use the first of all possible domains.
        }
    } else { // A single primary domain in this case; i.e., `active` = primary.
        $domain = (string) $wpdb->get_var('SELECT `domain` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `blog_id` = \''.esc_sql($blog_id).'\' AND `domain` IS NOT NULL AND `domain` != \'\' AND `active` = \'1\' LIMIT 1');
    }
    if (!$domain && $fallback && ($blog_details = $self->blogDetails($blog_id))) {
        $domain = $blog_details->domain; // Use original domain.
    }
    $wpdb->suppress_errors($suppressing_errors); // Restore.

    return ($domain = strtolower((string) $domain));
};

/*
 * Converts a blog ID into mapped domains (plural).
 *
 * @since 150821 Improving multisite compat.
 *
 * @param integer $blog_id The blog ID.
 *
 * @return array Mapped domains; else an empty array.
 *
 * @note The return value of this function is cached to reduce overhead on repeat calls.
 */
$self->domainMappingBlogDomains = function ($blog_id = 0) use ($self) {
    if (!is_multisite() || !$self->canConsiderDomainMapping()) {
        return array(); // Not possible/applicable.
    }
    if (($blog_id = (integer) $blog_id) < 0) {
        $blog_id = (integer) get_current_site()->blog_id;
    }
    if (!$blog_id) {
        $blog_id = (integer) get_current_blog_id();
    }
    if (!$blog_id || $blog_id < 0) {
        return array(); // Not possible.
    }
    if (!is_null($domains = &$self->staticKey('domainMappingBlogDomains', $blog_id))) {
        return $domains; // Already cached this.
    }
    $wpdb                     = $self->wpdb(); // WordPress database class.
    $suppressing_errors       = $wpdb->suppress_errors(); // In case table has not been created yet.
    $enforcing_primary_domain = !get_site_option('dm_no_primary_domain'); // Enforcing primary domain?

    if (!$enforcing_primary_domain) { // Not enforcing a primary domain, so let's pull all of the domains.
        $domains = $wpdb->get_col('SELECT `domain` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `blog_id` = \''.esc_sql($blog_id).'\' AND `domain` IS NOT NULL AND `domain` != \'\' ORDER BY `active` DESC');
    } else { // Primary domains in this case; i.e., `active` = primary.
        $domains = $wpdb->get_col('SELECT `domain` FROM `'.esc_sql($wpdb->base_prefix.'domain_mapping').'` WHERE `blog_id` = \''.esc_sql($blog_id).'\' AND `domain` IS NOT NULL AND `domain` != \'\' AND `active` = \'1\'');
    }
    $wpdb->suppress_errors($suppressing_errors); // Restore.

    return ($domains = array_unique(array_map('strtolower', (array) $domains)));
};
