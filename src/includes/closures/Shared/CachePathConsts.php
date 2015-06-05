<?php
namespace WebSharks\ZenCache;

if (defined(__NAMESPACE__.'\\CACHE_PATH_DEFAULT')) {
    return; // Already defined these.
}
/**
 * Default cache path flags.
 *
 * @since 150422 Rewrite.
 *
 * @type int A bitmask.
 */
const CACHE_PATH_DEFAULT = 0;

/**
 * Exclude scheme from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_SCHEME = 1;

/**
 * Exclude host (i.e. domain name) from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_HOST = 2;

/**
 * Exclude path from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_PATH = 4;

/**
 * Exclude path index (i.e. no default `index`) from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_PATH_INDEX = 8;

/**
 * Exclude query, user & version salt from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_QUV = 16;

/**
 * Exclude query string from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_QUERY = 32;

/**
 * Exclude user token from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_USER = 64;

/**
 * Exclude version salt from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_VSALT = 128;

/**
 * Exclude extension from cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_NO_EXT = 256;

/**
 * Allow wildcards in the cache path.
 *
 * @since 150422 Rewrite.
 *
 * @type int Part of a bitmask.
 */
const CACHE_PATH_ALLOW_WILDCARDS = 512;

/**
 * Default cache path regex suffix frag.
 *
 * @since 150422 Rewrite.
 *
 * @type string Default regex suffix frag used in cache path patterns.
 */
const CACHE_PATH_REGEX_DEFAULT_SUFFIX_FRAG = '(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])';
