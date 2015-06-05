<?php
namespace WebSharks\ZenCache;

/*
 * Cache directory path.
 *
 * @since 150422 Rewrite.
 *
 * @param string $rel_path Relative path inside cache directory.
 *
 * @throws \Exception If unable to get cache directory.
 *
 * @return string Absolute path to cache directory.
 */
$self->cacheDir = function ($rel_path = '') use ($self) {
    $rel_path = (string) $rel_path;

    if (!($self instanceof AdvancedCache)) {
        $cache_dir = $self->wpContentBaseDirTo($self->cache_sub_dir);
    } elseif (defined('ZENCACHE_DIR') && \ZENCACHE_DIR) {
        $cache_dir = \ZENCACHE_DIR;
    }
    if (empty($cache_dir)) {
        throw new \Exception(__('Unable to determine cache directory location.', SLUG_TD));
    }
    return $cache_dir.($rel_path ? '/'.ltrim($rel_path) : '');
};

/*
 * Clear files from the cache directory (for all hosts/blogs);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string $regex A regex pattern; see {@link deleteFilesFromCacheDir()}.
 *
 * @return integer Total files cleared by this routine (if any).
 */
$self->clearFilesFromCacheDir = function ($regex) use ($self) {
    return $self->deleteFilesFromCacheDir($regex);
};

/*
 * Clear files from the cache directory (for the current host);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string $regex A regex pattern; see {@link deleteFilesFromHostCacheDir()}.
 *
 * @return integer Total files cleared by this routine (if any).
 */
$self->clearFilesFromHostCacheDir = function ($regex) use ($self) {
    return $self->deleteFilesFromHostCacheDir($regex);
};

/*
 * Purge files from the cache directory (for all hosts/blogs);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string $regex A regex pattern; see {@link deleteFilesFromCacheDir()}.
 *
 * @return integer Total files purged by this routine (if any).
 */
$self->purgeFilesFromCacheDir = function ($regex) use ($self) {
    return $self->deleteFilesFromCacheDir($regex, true);
};

/*
 * Purge files from the cache directory (for the current host);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string $regex A regex pattern; see {@link deleteFilesFromHostCacheDir()}.
 *
 * @return integer Total files purged by this routine (if any).
 */
$self->purgeFilesFromHostCacheDir = function ($regex) use ($self) {
    return $self->deleteFilesFromHostCacheDir($regex, true);
};

/*
 * Delete files from the cache directory (for all hosts/blogs);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string  $regex A `/[regex pattern]/`; relative to the cache directory.
 *    e.g. `/^http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
 *
 *    Or, this can also be a full/absolute regex pattern against an absolute path;
 *    provided that it always starts with `/^`; including the full absolute cache/host directory path.
 *    e.g. `/^\/cache\/dir\/http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
 *
 *    NOTE: Paths used in any/all regex patterns should be generated with {@link buildCachePath()}.
 *       Recommended flags to {@link buildCachePath()} include the following.
 *
 *       - {@link CACHE_PATH_NO_PATH_INDEX}
 *       - {@link CACHE_PATH_NO_QUV}
 *       - {@link CACHE_PATH_NO_EXT}
 *
 *    **TIP:** There is a variation of {@link buildCachePath()} to assist with this.
 *    Please see: {@link buildCachePathRegex()}. It is much easier to work with :-)
 *
 * @param boolean $check_max_age Check max age? i.e., use purge behavior?
 *
 * @return integer Total files deleted by this routine (if any).
 *
 * @throws \Exception If unable to delete a file for any reason.
 */
$self->deleteFilesFromCacheDir = function ($regex, $check_max_age = false) use ($self) {
    $counter = 0; // Initialize.

    if (!($regex = (string) $regex)) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    $cache_dir = $self->nDirSeps($cache_dir);

    if ($check_max_age && $self instanceof AdvancedCache) {
        throw new \Exception(__('Requires an instance of Plugin.', SLUG_TD));
    }
    if ($check_max_age && !($max_age = strtotime('-'.$self->options['cache_max_age']))) {
        return $counter; // Invalid cache expiration time.
    }
    /* ------- Begin lock state... ----------- */

    $cache_lock = $self->cacheLock(); // Lock cache writes.

    clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

    $cache_dir_tmp       = $self->addTmpSuffix($cache_dir); // Temporary directory.
    $cache_dir_tmp_regex = $regex; // Initialize host-specific regex pattern for the tmp directory.

    $cache_dir_tmp_regex = '\\/'.ltrim($cache_dir_tmp_regex, '^\\/'); // Make sure it begins with an escaped `/`.
    $cache_dir_tmp_regex = $self->strIreplaceOnce(preg_quote($cache_dir.'/', '/'), '', $cache_dir_tmp_regex);

    $cache_dir_tmp_regex = ltrim($cache_dir_tmp_regex, '^\\/');
    if (strpos($cache_dir_tmp_regex, '(?:\/') === 0 || strpos($cache_dir_tmp_regex, '(\/') === 0) {
        $cache_dir_tmp_regex = '/^'.preg_quote($cache_dir_tmp, '/').$cache_dir_tmp_regex;
    } else {
        $cache_dir_tmp_regex = '/^'.preg_quote($cache_dir_tmp.'/', '/').$cache_dir_tmp_regex;
    }
    # if(WP_DEBUG) file_put_contents(WP_CONTENT_DIR.'/zc-debug.log', print_r($regex, TRUE)."\n".print_r($cache_dir_tmp_regex, TRUE)."\n\n", FILE_APPEND);
    // Uncomment the above line to debug regex pattern matching used by this routine; and others that call upon it.

    if (!rename($cache_dir, $cache_dir_tmp)) {
        throw new \Exception(sprintf(__('Unable to delete files. Rename failure on directory: `%1$s`.', SLUG_TD), $cache_dir));
    }
    foreach (($_dir_regex_iteration = $self->dirRegexIteration($cache_dir_tmp, $cache_dir_tmp_regex)) as $_resource) {
        $_resource_type = $_resource->getType();
        $_sub_path_name = $_resource->getSubpathname();
        $_path_name     = $_resource->getPathname();

        if ($_resource_type !== 'dir' && strpos($_sub_path_name, '/') === false) {
            continue; // Don't delete links/files in the immediate directory; e.g. `zc-advanced-cache` or `.htaccess`, etc.
            // Actual `http|https/...` cache links/files are nested. Links/files in the immediate directory are for other purposes.
        }
        switch ($_resource_type) {// Based on type; i.e., `link`, `file`, `dir`.

            case 'link': // Symbolic links; i.e., 404 errors.

                if ($check_max_age && !empty($max_age) && is_file($_resource->getLinkTarget())) {
                    if (($_lstat = lstat($_path_name)) && !empty($_lstat['mtime'])) {
                        if ($_lstat['mtime'] >= $max_age) {
                            break; // Break switch.
                        }
                    }
                }
                if (!unlink($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', SLUG_TD), $_path_name));
                }
                $counter++; // Increment counter for each link we delete.

                break; // Break switch handler.

            case 'file': // Regular files; i.e., not symlinks.

                if ($check_max_age && !empty($max_age)) {
                    if ($_resource->getMTime() >= $max_age) {
                        break; // Break switch.
                    }
                }
                if (!unlink($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', SLUG_TD), $_path_name));
                }
                $counter++; // Increment counter for each file we delete.

                break; // Break switch handler.

            case 'dir': // A regular directory; i.e., not a symlink.

                if ($regex !== '/^.+/i') {
                    break; // Break; not deleting everything.
                }
                if ($check_max_age && !empty($max_age)) {
                    break; // Break; not deleting everything.
                }
                if (!rmdir($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', SLUG_TD), $_path_name));
                }
                # $counter++; // Increment counter for each directory we delete. ~ NO don't do that here.

                break; // Break switch handler.

            default: // Something else that is totally unexpected here.
                throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', SLUG_TD), $_resource_type));
        }
    }
    unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name, $_lstat); // Housekeeping.

    if (!rename($cache_dir_tmp, $cache_dir)) {
        throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', SLUG_TD), $cache_dir_tmp));
    }
    /* ------- End lock state... ------------- */

    $self->cacheUnlock($cache_lock);

    return $counter;
};

/*
 * Delete files from the cache directory (for the current host);
 *    i.e., those that match a specific regex pattern.
 *
 * @since 150422 Rewrite.
 *
 * @param string  $regex A `/[regex pattern]/`; relative to the host cache directory.
 *    e.g. `/^my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
 *
 *    Or, this can also be a full/absolute regex pattern against an absolute path;
 *    provided that it always starts with `/^`; including the full absolute cache/host directory path.
 *    e.g. `/^\/cache\/dir\/http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
 *
 *    NOTE: Paths used in any/all regex patterns should be generated with {@link buildCachePath()}.
 *       Recommended flags to {@link buildCachePath()} include the following.
 *
 *       - {@link CACHE_PATH_NO_SCHEME}
 *       - {@link CACHE_PATH_NO_HOST}
 *       - {@link CACHE_PATH_NO_PATH_INDEX}
 *       - {@link CACHE_PATH_NO_QUV}
 *       - {@link CACHE_PATH_NO_EXT}
 *
 *    **TIP:** There is a variation of {@link buildCachePath()} to assist with this.
 *    Please see: {@link buildHostCachePathRegex()}. It is much easier to work with :-)
 *
 * @param boolean $check_max_age Check max age? i.e., use purge behavior?
 *
 * @return integer Total files deleted by this routine (if any).
 *
 * @throws \Exception If unable to delete a file for any reason.
 */
$self->deleteFilesFromHostCacheDir = function ($regex, $check_max_age = false) use ($self) {
    $counter = 0; // Initialize.

    if (!($regex = (string) $regex)) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    $host  = !empty($_SERVER['HTTP_HOST'])
        ? strtolower((string) $_SERVER['HTTP_HOST'])
        : '';
    $host_base_dir_tokens = $self->hostBaseDirTokens();
    $cache_dir            = $self->nDirSeps($cache_dir);

    if ($check_max_age && $self instanceof AdvancedCache) {
        throw new \Exception(__('Requires an instance of Plugin.', SLUG_TD));
    }
    if ($check_max_age && !($max_age = strtotime('-'.$self->options['cache_max_age']))) {
        return $counter; // Invalid cache expiration time.
    }
    /* ------- Begin lock state... ----------- */

    $cache_lock = $self->cacheLock(); // Lock cache writes.

    clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

    foreach (array('http', 'https') as $_host_scheme) {
        /* This multi-scheme iteration could (alternatively) be accomplished via regex `\/https?\/`.
            HOWEVER, since this operation is supposed to impact only a single host in a network, and because
            we want to do atomic deletions, we iterate and rename `$_host_cache_dir` for each scheme.

            It's also worth noting that most high traffic sites will not be in the habit of serving
            pages over SSL all the time; so this really should not have a significant performance hit.
            In fact, it may improve performance since we are traversing each sub-directory separately;
            i.e., we don't need to glob both `http` and `https` traffic into a single directory scan. */
        $_host_url              = $_host_scheme.'://'.$host.$host_base_dir_tokens;
        $_host_cache_path_flags = CACHE_PATH_NO_PATH_INDEX | CACHE_PATH_NO_QUV | CACHE_PATH_NO_EXT;
        $_host_cache_path       = $self->buildCachePath($_host_url, '', '', $_host_cache_path_flags);
        $_host_cache_dir        = $self->nDirSeps($cache_dir.'/'.$_host_cache_path); // Normalize.

        if (!$_host_cache_dir || !is_dir($_host_cache_dir)) {
            continue; // Nothing to do.
        }
        $_host_cache_dir_tmp       = $self->addTmpSuffix($_host_cache_dir); // Temporary directory.
        $_host_cache_dir_tmp_regex = $regex; // Initialize host-specific regex pattern for the tmp directory.

        $_host_cache_dir_tmp_regex = '\\/'.ltrim($_host_cache_dir_tmp_regex, '^\\/'); // Make sure it begins with an escaped `/`.
        $_host_cache_dir_tmp_regex = $self->strIreplaceOnce(preg_quote($_host_cache_path.'/', '/'), '', $_host_cache_dir_tmp_regex);
        $_host_cache_dir_tmp_regex = $self->strIreplaceOnce(preg_quote($_host_cache_dir.'/', '/'), '', $_host_cache_dir_tmp_regex);

        $_host_cache_dir_tmp_regex = ltrim($_host_cache_dir_tmp_regex, '^\\/');
        if (strpos($_host_cache_dir_tmp_regex, '(?:\/') === 0 || strpos($_host_cache_dir_tmp_regex, '(\/') === 0) {
            $_host_cache_dir_tmp_regex = '/^'.preg_quote($_host_cache_dir_tmp, '/').$_host_cache_dir_tmp_regex;
        } else {
            $_host_cache_dir_tmp_regex = '/^'.preg_quote($_host_cache_dir_tmp.'/', '/').$_host_cache_dir_tmp_regex;
        }
        # if(WP_DEBUG) file_put_contents(WP_CONTENT_DIR.'/zc-debug.log', print_r($regex, TRUE)."\n".print_r($_host_cache_dir_tmp_regex, TRUE)."\n\n", FILE_APPEND);
        // Uncomment the above line to debug regex pattern matching used by this routine; and others that call upon it.

        if (!rename($_host_cache_dir, $_host_cache_dir_tmp)) {
            throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', SLUG_TD), $_host_cache_dir));
        }
        foreach (($_dir_regex_iteration = $self->dirRegexIteration($_host_cache_dir_tmp, $_host_cache_dir_tmp_regex)) as $_resource) {
            $_resource_type = $_resource->getType();
            $_sub_path_name = $_resource->getSubpathname();
            $_path_name     = $_resource->getPathname();

            if ($_host_cache_dir === $cache_dir && $_resource_type !== 'dir' && strpos($_sub_path_name, '/') === false) {
                continue; // Don't delete links/files in the immediate directory; e.g. `zc-advanced-cache` or `.htaccess`, etc.
                // Actual `http|https/...` cache links/files are nested. Links/files in the immediate directory are for other purposes.
            }
            switch ($_resource_type) {// Based on type; i.e., `link`, `file`, `dir`.

                case 'link': // Symbolic links; i.e., 404 errors.

                    if ($check_max_age && !empty($max_age) && is_file($_resource->getLinkTarget())) {
                        if (($_lstat = lstat($_path_name)) && !empty($_lstat['mtime'])) {
                            if ($_lstat['mtime'] >= $max_age) {
                                break; // Break switch.
                            }
                        }
                    }
                    if (!unlink($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', SLUG_TD), $_path_name));
                    }
                    $counter++; // Increment counter for each link we delete.

                    break; // Break switch handler.

                case 'file': // Regular files; i.e., not symlinks.

                    if ($check_max_age && !empty($max_age)) {
                        if ($_resource->getMTime() >= $max_age) {
                            break; // Break switch handler.
                        }
                    }
                    if (!unlink($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', SLUG_TD), $_path_name));
                    }
                    $counter++; // Increment counter for each file we delete.

                    break; // Break switch handler.

                case 'dir': // A regular directory; i.e., not a symlink.

                    if ($regex !== '/^.+/i') {
                        break; // Break; not deleting everything.
                    }
                    if ($check_max_age && !empty($max_age)) {
                        break; // Break; not deleting everything.
                    }
                    if (!rmdir($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', SLUG_TD), $_path_name));
                    }
                    # $counter++; // Increment counter for each directory we delete. ~ NO don't do that here.

                    break; // Break switch handler.

                default: // Something else that is totally unexpected here.
                    throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', SLUG_TD), $_resource_type));
            }
        }
        unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name, $_lstat); // Housekeeping.

        if (!rename($_host_cache_dir_tmp, $_host_cache_dir)) {
            throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', SLUG_TD), $_host_cache_dir_tmp));
        }
    }
    unset($_host_scheme, $_host_url, $_host_cache_path_flags, $_host_cache_path, $_host_cache_dir, $_host_cache_dir_tmp, $_host_cache_dir_tmp_regex);

    /* ------- End lock state... ------------- */

    $self->cacheUnlock($cache_lock);

    return $counter;
};

/*
 * Delete all files/dirs from a directory (for all schemes/hosts);
 *    including `zc-` prefixed files; or anything else for that matter.
 *
 * @since 150422 Rewrite.
 *
 * @param string  $dir The directory from which to delete files/dirs.
 *
 *    SECURITY: This directory MUST be located inside the `/wp-content/` directory.
 *    Also, it MUST be a sub-directory of `/wp-content/`, NOT the directory itself.
 *    Also, it cannot be: `mu-plugins`, `themes`, or `plugins`.
 *
 * @param boolean $delete_dir_too Delete parent? i.e., delete the `$dir` itself also?
 *
 * @return integer Total files/directories deleted by this routine (if any).
 *
 * @throws \Exception If unable to delete a file/directory for any reason.
 */
$self->deleteAllFilesDirsIn = function ($dir, $delete_dir_too = false) use ($self) {
    $counter = 0; // Initialize.

    if (!($dir = trim((string) $dir)) || !is_dir($dir)) {
        return $counter; // Nothing to do.
    }
    $dir                  = $self->nDirSeps($dir);
    $dir_temp             = $self->addTmpSuffix($dir);
    $wp_content_dir       = $self->nDirSeps(WP_CONTENT_DIR);
    $wp_content_dir_regex = preg_quote($wp_content_dir, '/');

    if (!preg_match('/^'.$wp_content_dir_regex.'\/[^\/]+/i', $dir)) {
        return $counter; // Security flag; do nothing in this case.
    }
    if (preg_match('/^'.$wp_content_dir_regex.'\/(?:mu\-plugins|themes|plugins)(?:\/|$)/i', $dir)) {
        return $counter; // Security flag; do nothing in this case.
    }
    /* ------- Begin lock state... ----------- */

    $cache_lock = $self->cacheLock(); // Lock cache writes.

    clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

    if (!rename($dir, $dir_temp)) {
        throw new \Exception(sprintf(__('Unable to delete all files/dirs. Rename failure on tmp directory: `%1$s`.', SLUG_TD), $dir));
    }
    foreach (($_dir_regex_iteration = $self->dirRegexIteration($dir_temp, '/.+/')) as $_resource) {
        $_resource_type = $_resource->getType();
        $_sub_path_name = $_resource->getSubpathname();
        $_path_name     = $_resource->getPathname();

        switch ($_resource_type) {// Based on type; i.e., `link`, `file`, `dir`.

            case 'link': // Symbolic links; i.e., 404 errors.

                if (!unlink($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', SLUG_TD), $_path_name));
                }
                $counter++; // Increment counter for each link we delete.

                break; // Break switch handler.

            case 'file': // Regular files; i.e., not symlinks.

                if (!unlink($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', SLUG_TD), $_path_name));
                }
                $counter++; // Increment counter for each file we delete.

                break; // Break switch handler.

            case 'dir': // A regular directory; i.e., not a symlink.

                if (!rmdir($_path_name)) {
                    throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', SLUG_TD), $_path_name));
                }
                $counter++; // Increment counter for each directory we delete.

                break; // Break switch handler.

            default: // Something else that is totally unexpected here.
                throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', SLUG_TD), $_resource_type));
        }
    }
    unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name); // Housekeeping.

    if (!rename($dir_temp, $dir)) {
        throw new \Exception(sprintf(__('Unable to delete all files/dirs. Rename failure on tmp directory: `%1$s`.', SLUG_TD), $dir_temp));
    }
    if ($delete_dir_too) {
        if (!rmdir($dir)) {
            throw new \Exception(sprintf(__('Unable to delete directory: `%1$s`.', SLUG_TD), $dir));
        }
        $counter++; // Increment counter for each directory we delete.
    }
    /* ------- End lock state... ------------- */

    $self->cacheUnlock($cache_lock);

    return $counter;
};
