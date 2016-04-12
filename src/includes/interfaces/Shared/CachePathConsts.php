<?php
namespace WebSharks\CometCache\Interfaces\Shared;

interface CachePathConsts
{
    /**
     * Default cache path flags.
     *
     * @since 150422 Rewrite.
     *
     * @type int A bitmask.
     */
    const CACHE_PATH_DEFAULT = 0;

    /**
     * Use a domain-mapped cache path.
     *
     * @since 150821 Improving multisite compat.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_CONSIDER_DOMAIN_MAPPING = 1;

    /**
     * Generate an unmapped cache path?
     *
     * @since 150821 Improving multisite compat.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_REVERSE_DOMAIN_MAPPING = 2;

    /**
     * Exclude scheme from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_SCHEME = 4;

    /**
     * Exclude host (i.e. domain name) from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_HOST = 8;

    /**
     * Exclude path from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_PATH = 16;

    /**
     * Exclude path index (i.e. no default `index`) from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_PATH_INDEX = 32;

    /**
     * Exclude query, user & version salt from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_QUV = 64;

    /**
     * Exclude query string from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_QUERY = 128;

    /**
     * Exclude user token from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_USER = 256;

    /**
     * Exclude version salt from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_VSALT = 512;

    /**
     * Exclude extension from cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_NO_EXT = 1024;

    /**
     * Allow wildcards in the cache path.
     *
     * @since 150422 Rewrite.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_ALLOW_WILDCARDS = 2048;

    /**
     * Allow watered-down regex in the cache path.
     *
     * @since 151114 Improving regex syntax.
     *
     * @type int Part of a bitmask.
     */
    const CACHE_PATH_ALLOW_WD_REGEX = 4096;

    /**
     * Default cache path regex suffix frag.
     *
     * @since 150422 Rewrite.
     *
     * @type string Default regex suffix frag used in cache path patterns.
     */
    const CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG = null;
}
