<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait CacheDirUtils
{
    /**
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
    public function cacheDir($rel_path = '')
    {
        $rel_path = (string) $rel_path;

        if ($this->isAdvancedCache()) {
            $cache_dir = defined('COMET_CACHE_DIR') ? COMET_CACHE_DIR : '';
        } elseif (!empty($this->cache_sub_dir)) {
            $cache_dir = $this->wpContentBaseDirTo($this->cache_sub_dir);
        }
        if (empty($cache_dir)) {
            throw new \Exception(__('Unable to determine cache directory location.', 'comet-cache'));
        }
        return rtrim($cache_dir, '/').($rel_path ? '/'.ltrim($rel_path) : '');
    }

    /**
     * Wipe files from the cache directory (for all hosts/blogs);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 151002 While working on directory stats.
     *
     * @param string $regex A regex pattern; see {@link deleteFilesFromCacheDir()}.
     *
     * @return int Total files wiped by this routine.
     */
    public function wipeFilesFromCacheDir($regex)
    {
        return $this->deleteFilesFromCacheDir($regex);
    }

    /**
     * Clear files from the cache directory (for the current host);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $regex A regex pattern; see {@link deleteFilesFromHostCacheDir()}.
     *
     * @return int Total files cleared by this routine (if any).
     */
    public function clearFilesFromHostCacheDir($regex)
    {
        return $this->deleteFilesFromHostCacheDir($regex);
    }

    /**
     * Wurge (purge) files from the cache directory (for all hosts/blogs);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 151002 While working on directory stats.
     *
     * @param string $regex A regex pattern; see {@link deleteFilesFromCacheDir()}.
     *
     * @return int Total files wurged by this routine.
     */
    public function wurgeFilesFromCacheDir($regex)
    {
        return $this->deleteFilesFromCacheDir($regex, true);
    }

    /**
     * Purge files from the cache directory (for the current host);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $regex A regex pattern; see {@link deleteFilesFromHostCacheDir()}.
     *
     * @return int Total files purged by this routine (if any).
     */
    public function purgeFilesFromHostCacheDir($regex)
    {
        return $this->deleteFilesFromHostCacheDir($regex, true);
    }

    /**
     * Delete files from the cache directory (for all hosts/blogs);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $regex A `/[regex pattern]/`; relative to the cache directory.
     *                      e.g. `/^http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
     *
     *    Or, this can also be a full/absolute regex pattern against an absolute path;
     *    provided that it always starts with `/^`; including the full absolute cache/host directory path.
     *    e.g. `/^\/cache\/dir\/http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
     * @param bool $check_max_age Check max age? i.e., use purge behavior?
     *
     * @throws \Exception If unable to delete a file for any reason.
     *
     * @return int Total files deleted by this routine (if any).
     *
     *
     * @TODO Optimize this for multisite networks w/ a LOT of child blogs.
     * @TODO Optimize this for extremely large sites. A LOT of files here could slow things down.
     *  This class member is currently used in wiping and purging for a network. So there is the potential for a LOT of files in a single scan.
     *  See also: <https://codex.wordpress.org/Function_Reference/wp_is_large_network>
     */
    public function deleteFilesFromCacheDir($regex, $check_max_age = false)
    {
        $counter = 0; // Initialize.

        if (!($regex = (string) $regex)) {
            return $counter; // Nothing to do.
        }
        if (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        $cache_dir = $this->nDirSeps($cache_dir);

        if ($check_max_age && $this->isAdvancedCache()) {
            throw new \Exception(__('Invalid argument; isAdvancedCache!', 'comet-cache'));
        }
        if ($check_max_age && !($max_age = strtotime('-'.$this->options['cache_max_age']))) {
            return $counter; // Invalid cache expiration time.
        }
        /* ------- Begin lock state... ----------- */

        $cache_lock = $this->cacheLock(); // Lock cache writes.

        clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

        $cache_dir_tmp = $this->addTmpSuffix($cache_dir); // Temporary directory.

        $cache_dir_tmp_regex = $regex; // Initialize host-specific regex pattern for the tmp directory.
        $cache_dir_tmp_regex = '\\/'.ltrim($cache_dir_tmp_regex, '^\\/'); // Make sure it begins with an escaped `/`.
        $cache_dir_tmp_regex = preg_replace('/'.preg_quote(preg_quote($cache_dir.'/', '/'), '/').'/ui', '', $cache_dir_tmp_regex, 1);

        $cache_dir_tmp_regex = ltrim($cache_dir_tmp_regex, '^\\/');
        if (mb_strpos($cache_dir_tmp_regex, '(?:\/') === 0 || mb_strpos($cache_dir_tmp_regex, '(\/') === 0) {
            $cache_dir_tmp_regex = '/^'.preg_quote($cache_dir_tmp, '/').$cache_dir_tmp_regex;
        } else {
            $cache_dir_tmp_regex = '/^'.preg_quote($cache_dir_tmp.'/', '/').$cache_dir_tmp_regex;
        }
        # if(WP_DEBUG) file_put_contents(WP_CONTENT_DIR.'/'.mb_strtolower(SHORT_NAME).'-debug.log', print_r($regex, TRUE)."\n".print_r($cache_dir_tmp_regex, TRUE)."\n\n", FILE_APPEND);
        // Uncomment the above line to debug regex pattern matching used by this routine; and others that call upon it.

        if (!rename($cache_dir, $cache_dir_tmp)) {
            throw new \Exception(sprintf(__('Unable to delete files. Rename failure on directory: `%1$s`.', 'comet-cache'), $cache_dir));
        }
        foreach (($_dir_regex_iteration = $this->dirRegexIteration($cache_dir_tmp, $cache_dir_tmp_regex)) as $_resource) {
            $_resource_type = $_resource->getType();
            $_sub_path_name = $_resource->getSubpathname();
            $_path_name     = $_resource->getPathname();

            if ($_resource_type !== 'dir' && mb_strpos($_sub_path_name, '/') === false) {
                continue; // Don't delete links/files in the immediate directory; e.g. `[SHORT_NAME]-advanced-cache` or `.htaccess`, etc.
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
                        $this->tryErasingAllFilesDirsIn($cache_dir_tmp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each link we delete.

                    break; // Break switch handler.

                case 'file': // Regular files; i.e., not symlinks.

                    if ($check_max_age && !empty($max_age)) {
                        if ($_resource->getMTime() >= $max_age) {
                            break; // Break switch.
                        }
                    }
                    if (!unlink($_path_name)) {
                        $this->tryErasingAllFilesDirsIn($cache_dir_tmp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each file we delete.

                    break; // Break switch handler.

                case 'dir': // A regular directory; i.e., not a symlink.

                    if (!in_array(rtrim(str_replace(['^', '$'], '', $regex), 'ui'), ['/.*/', '/.+/'], true)) {
                        break; // Not deleting everything.
                    }
                    if ($check_max_age && !empty($max_age)) {
                        break; // Not deleting everything.
                    }
                    if (!rmdir($_path_name)) {
                        $this->tryErasingAllFilesDirsIn($cache_dir_tmp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    # $counter++; // Increment counter for each directory we delete. ~ NO don't do that here.

                    break; // Break switch handler.

                default: // Something else that is totally unexpected here.
                    $this->tryErasingAllFilesDirsIn($cache_dir_tmp, true); // Cleanup if possible.
                    throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', 'comet-cache'), $_resource_type));
            }
        }
        unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name, $_lstat); // Housekeeping.

        if (!rename($cache_dir_tmp, $cache_dir)) {
            $this->tryErasingAllFilesDirsIn($cache_dir_tmp, true); // Cleanup if possible.
            throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', 'comet-cache'), $cache_dir_tmp));
        }
        /* ------- End lock state... ------------- */

        $this->cacheUnlock($cache_lock); // Release.

        return $counter;
    }

    /**
     * Delete files from the cache directory (for the current host);
     *    i.e., those that match a specific regex pattern.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $regex A `/[regex pattern]/`; relative to the host cache directory.
     *                      e.g. `/^my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
     *
     *    Or, this can also be a full/absolute regex pattern against an absolute path;
     *    provided that it always starts with `/^`; including the full absolute cache/host directory path.
     *    e.g. `/^\/cache\/dir\/http\/example\.com\/my\-slug(?:\/index)?(?:\.|\/(?:page\/[0-9]+|comment\-page\-[0-9]+)[.\/])/`
     * @param bool $check_max_age                                   Check max age? i.e., use purge behavior?
     * @param bool $___considering_domain_mapping                   For internal use only.
     * @param bool $___consider_domain_mapping_host_token           For internal use only.
     * @param bool $___consider_domain_mapping_host_base_dir_tokens For internal use only.
     *
     * @throws \Exception If unable to delete a file for any reason.
     *
     * @return int Total files deleted by this routine (if any).
     */
    public function deleteFilesFromHostCacheDir(
        $regex,
        $check_max_age = false,
        $___considering_domain_mapping = false,
        $___consider_domain_mapping_host_token = null,
        $___consider_domain_mapping_host_base_dir_tokens = null
    ) {
        $counter = 0; // Initialize.

        if (!($regex = (string) $regex)) {
            return $counter; // Nothing to do.
        }
        if (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        // On a standard installation delete from all hosts.
        // See: <https://github.com/websharks/comet-cache/issues/608>
        if (!is_multisite() && !$___considering_domain_mapping) {
            if (in_array(rtrim(str_replace(['^', '$'], '', $regex), 'ui'), ['/.*/', '/.+/'], true)) {
                return $this->deleteFilesFromCacheDir($regex, $check_max_age);
                //
            } else { // Clearing specifics.
                $regex = ltrim($regex, '^\\/');

                if (mb_strpos($regex, '(?:\/') === 0 || mb_strpos($regex, '(\/') === 0) {
                    $regex = '/^https?\/[^\/]+'.$regex;
                } else {
                    $regex = '/^https?\/[^\/]+\/'.$regex;
                }
                return $this->deleteFilesFromCacheDir($regex, $check_max_age);
            }
        }
        $cache_dir            = $this->nDirSeps($cache_dir); // Normalize.
        $host_token           = $current_host_token           = $this->hostToken();
        $host_base_dir_tokens = $current_host_base_dir_tokens = $this->hostBaseDirTokens();

        if ($___considering_domain_mapping && isset($___consider_domain_mapping_host_token, $___consider_domain_mapping_host_base_dir_tokens)) {
            $host_token           = (string) $___consider_domain_mapping_host_token;
            $host_base_dir_tokens = (string) $___consider_domain_mapping_host_base_dir_tokens;
        }
        if (!$host_token) { // Must have a host in the sub-routine below.
            throw new \Exception(__('Invalid argument; host token empty!', 'comet-cache'));
        }
        if ($check_max_age && $this->isAdvancedCache()) {
            throw new \Exception(__('Invalid argument; isAdvancedCache!', 'comet-cache'));
        }
        if ($check_max_age && !($max_age = strtotime('-'.$this->options['cache_max_age']))) {
            return $counter; // Invalid cache expiration time.
        }
        /* ------- Begin lock state... ----------- */

        $cache_lock = $this->cacheLock(); // Lock cache writes.

        clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

        foreach (['http', 'https'] as $_host_scheme) {
            $_host_url              = $_host_scheme.'://'.$host_token.$host_base_dir_tokens;
            $_host_cache_path_flags = $this::CACHE_PATH_NO_PATH_INDEX | $this::CACHE_PATH_NO_QUV | $this::CACHE_PATH_NO_EXT;
            $_host_cache_path       = $this->buildCachePath($_host_url, '', '', $_host_cache_path_flags);
            $_host_cache_dir        = $this->nDirSeps($cache_dir.'/'.$_host_cache_path); // Normalize.

            if (!$_host_cache_dir || !is_dir($_host_cache_dir)) {
                // On a multisite install this may have a cache sub-directory.
                //  e.g., `http/example-com[[-base]-child1][[/base]/child1]` instead of `http/example-com`.
                continue; // Nothing to do.
            }
            $_host_cache_dir_tmp = $this->addTmpSuffix($_host_cache_dir); // Temporary directory.

            $_host_cache_dir_tmp_regex = $regex; // Initialize host-specific regex pattern for the tmp directory.
            $_host_cache_dir_tmp_regex = '\\/'.ltrim($_host_cache_dir_tmp_regex, '^\\/'); // Make sure it begins with an escaped `/`.
            $_host_cache_dir_tmp_regex = preg_replace('/'.preg_quote(preg_quote($_host_cache_path.'/', '/'), '/').'/ui', '', $_host_cache_dir_tmp_regex, 1);
            $_host_cache_dir_tmp_regex = preg_replace('/'.preg_quote(preg_quote($_host_cache_dir.'/', '/'), '/').'/ui', '', $_host_cache_dir_tmp_regex, 1);

            $_host_cache_dir_tmp_regex = ltrim($_host_cache_dir_tmp_regex, '^\\/');
            if (mb_strpos($_host_cache_dir_tmp_regex, '(?:\/') === 0 || mb_strpos($_host_cache_dir_tmp_regex, '(\/') === 0) {
                $_host_cache_dir_tmp_regex = '/^'.preg_quote($_host_cache_dir_tmp, '/').$_host_cache_dir_tmp_regex;
            } else {
                $_host_cache_dir_tmp_regex = '/^'.preg_quote($_host_cache_dir_tmp.'/', '/').$_host_cache_dir_tmp_regex;
            }
            #if(WP_DEBUG) file_put_contents(WP_CONTENT_DIR.'/'.mb_strtolower(SHORT_NAME).'-debug.log', print_r($regex, TRUE)."\n".print_r($_host_cache_dir_tmp_regex, TRUE)."\n\n", FILE_APPEND);
            // Uncomment the above line to debug regex pattern matching used by this routine; and others that call upon it.

            if (!rename($_host_cache_dir, $_host_cache_dir_tmp)) {
                throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', 'comet-cache'), $_host_cache_dir));
            }
            foreach (($_dir_regex_iteration = $this->dirRegexIteration($_host_cache_dir_tmp, $_host_cache_dir_tmp_regex)) as $_resource) {
                $_resource_type = $_resource->getType();
                $_sub_path_name = $_resource->getSubpathname();
                $_path_name     = $_resource->getPathname();

                if ($_host_cache_dir === $cache_dir && $_resource_type !== 'dir' && mb_strpos($_sub_path_name, '/') === false) {
                    continue; // Don't delete links/files in the immediate directory; e.g. `[SHORT_NAME]-advanced-cache` or `.htaccess`, etc.
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
                            $this->tryErasingAllFilesDirsIn($_host_cache_dir_tmp, true); // Cleanup if possible.
                            throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', 'comet-cache'), $_path_name));
                        }
                        ++$counter; // Increment counter for each link we delete.

                        break; // Break switch handler.

                    case 'file': // Regular files; i.e., not symlinks.

                        if ($check_max_age && !empty($max_age)) {
                            if ($_resource->getMTime() >= $max_age) {
                                break; // Break switch handler.
                            }
                        }
                        if (!unlink($_path_name)) {
                            $this->tryErasingAllFilesDirsIn($_host_cache_dir_tmp, true); // Cleanup if possible.
                            throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', 'comet-cache'), $_path_name));
                        }
                        ++$counter; // Increment counter for each file we delete.

                        break; // Break switch handler.

                    case 'dir': // A regular directory; i.e., not a symlink.

                        if (!in_array(rtrim(str_replace(['^', '$'], '', $regex), 'ui'), ['/.*/', '/.+/'], true)) {
                            break; // Not deleting everything.
                        }
                        if ($check_max_age && !empty($max_age)) {
                            break; // Not deleting everything.
                        }
                        if (!rmdir($_path_name)) {
                            $this->tryErasingAllFilesDirsIn($_host_cache_dir_tmp, true); // Cleanup if possible.
                            throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', 'comet-cache'), $_path_name));
                        }
                        # $counter++; // Increment counter for each directory we delete. ~ NO don't do that here.

                        break; // Break switch handler.

                    default: // Something else that is totally unexpected here.
                        $this->tryErasingAllFilesDirsIn($_host_cache_dir_tmp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', 'comet-cache'), $_resource_type));
                }
            }
            unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name, $_lstat); // Housekeeping.

            if (!rename($_host_cache_dir_tmp, $_host_cache_dir)) {
                $this->tryErasingAllFilesDirsIn($_host_cache_dir_tmp, true); // Cleanup if possible.
                throw new \Exception(sprintf(__('Unable to delete files. Rename failure on tmp directory: `%1$s`.', 'comet-cache'), $_host_cache_dir_tmp));
            }
        }
        unset($_host_scheme, $_host_url, $_host_cache_path_flags, $_host_cache_path, $_host_cache_dir, $_host_cache_dir_tmp, $_host_cache_dir_tmp_regex);

        /* ------- End lock state... ------------- */

        $this->cacheUnlock($cache_lock); // Release.

        /* ------- Include domain mapping variations also. ------- */

        if (!$___considering_domain_mapping && is_multisite() && $this->canConsiderDomainMapping()) {
            $domain_mapping_variations = []; // Initialize array of domain variations.

            if (($_host_token_for_blog = $this->hostTokenForBlog())) {
                $_host_base_dir_tokens_for_blog = $this->hostBaseDirTokensForBlog();
                $domain_mapping_variations[]    = ['host_token' => $_host_token_for_blog, 'host_base_dir_tokens' => $_host_base_dir_tokens_for_blog];
            } // The original blog host; i.e., without domain mapping.
            unset($_host_token_for_blog, $_host_base_dir_tokens_for_blog); // Housekeeping.

            foreach ($this->domainMappingBlogDomains() as $_domain_mapping_blog_domain) {
                if (($_domain_host_token_for_blog = $this->hostTokenForBlog(false, true, $_domain_mapping_blog_domain))) {
                    $_domain_host_base_dir_tokens_for_blog = $this->hostBaseDirTokensForBlog(false, true); // This is only a formality.
                    $domain_mapping_variations[]           = ['host_token' => $_domain_host_token_for_blog, 'host_base_dir_tokens' => $_domain_host_base_dir_tokens_for_blog];
                }
            } // This includes all of the domain mappings configured for the current blog ID.
            unset($_domain_mapping_blog_domain, $_domain_host_token_for_blog, $_domain_host_base_dir_tokens_for_blog); // Housekeeping.

            foreach ($domain_mapping_variations as $_domain_mapping_variation) {
                if ($_domain_mapping_variation['host_token'] === $current_host_token && $_domain_mapping_variation['host_base_dir_tokens'] === $current_host_base_dir_tokens) {
                    continue; // Exclude current tokens. They were already iterated above.
                }
                $counter += $this->deleteFilesFromHostCacheDir($regex, $check_max_age, true, $_domain_mapping_variation['host_token'], $_domain_mapping_variation['host_base_dir_tokens']);
            }
            unset($_domain_mapping_variation); // Housekeeping.
        }
        return $counter;
    }

    /**
     * Delete all files/dirs from a directory (for all schemes/hosts);
     *    including `[SHORT_NAME]-` prefixed files; or anything else for that matter.
     *
     * @since 150422 Rewrite. Updated 151002 w/ multisite compat. improvements.
     *
     * @param string $dir The directory from which to delete files/dirs.
     *
     *    SECURITY: This directory MUST be located inside the `/wp-content/` directory.
     *    Also, it MUST be a sub-directory of `/wp-content/`, NOT the directory itself.
     *    Also, it cannot be: `mu-plugins`, `themes`, or `plugins`.
     * @param bool $delete_dir_too Delete parent? i.e., delete the `$dir` itself also?
     *
     * @throws \Exception If unable to delete a file/directory for any reason.
     *
     * @return int Total files/directories deleted by this routine (if any).
     */
    public function deleteAllFilesDirsIn($dir, $delete_dir_too = false)
    {
        $counter = 0; // Initialize.

        if (!($dir = trim((string) $dir)) || !is_dir($dir)) {
            return $counter; // Nothing to do.
        }
        $dir                  = $this->nDirSeps($dir);
        $dir_temp             = $this->addTmpSuffix($dir);
        $wp_content_dir       = $this->nDirSeps(WP_CONTENT_DIR);
        $wp_content_dir_regex = preg_quote($wp_content_dir, '/');

        if (!preg_match('/^'.$wp_content_dir_regex.'\/[^\/]+/ui', $dir)) {
            return $counter; // Security flag; do nothing in this case.
        }
        if (preg_match('/^'.$wp_content_dir_regex.'\/(?:mu\-plugins|themes|plugins)(?:\/|$)/ui', $dir)) {
            return $counter; // Security flag; do nothing in this case.
        }
        /* ------- Begin lock state... ----------- */

        $cache_lock = $this->cacheLock(); // Lock cache writes.

        clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

        if (!rename($dir, $dir_temp)) {
            throw new \Exception(sprintf(__('Unable to delete all files/dirs. Rename failure on tmp directory: `%1$s`.', 'comet-cache'), $dir));
        }
        foreach (($_dir_regex_iteration = $this->dirRegexIteration($dir_temp, '/.+/u')) as $_resource) {
            $_resource_type = $_resource->getType();
            $_sub_path_name = $_resource->getSubpathname();
            $_path_name     = $_resource->getPathname();

            switch ($_resource_type) {// Based on type; i.e., `link`, `file`, `dir`.
                case 'link': // Symbolic links; i.e., 404 errors.

                    if (!unlink($_path_name)) {
                        $this->tryErasingAllFilesDirsIn($dir_temp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete symlink: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each link we delete.

                    break; // Break switch handler.

                case 'file': // Regular files; i.e., not symlinks.

                    if (!unlink($_path_name)) {
                        $this->tryErasingAllFilesDirsIn($dir_temp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete file: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each file we delete.

                    break; // Break switch handler.

                case 'dir': // A regular directory; i.e., not a symlink.

                    if (!rmdir($_path_name)) {
                        $this->tryErasingAllFilesDirsIn($dir_temp, true); // Cleanup if possible.
                        throw new \Exception(sprintf(__('Unable to delete dir: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    # ++$counter; // Increment counter for each directory we delete. ~ NO don't do that here.

                    break; // Break switch handler.

                default: // Something else that is totally unexpected here.
                    $this->tryErasingAllFilesDirsIn($dir_temp, true); // Cleanup if possible.
                    throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', 'comet-cache'), $_resource_type));
            }
        }
        unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name); // Housekeeping.

        if (!rename($dir_temp, $dir)) {
            $this->tryErasingAllFilesDirsIn($dir_temp, true); // Cleanup if possible.
            throw new \Exception(sprintf(__('Unable to delete all files/dirs. Rename failure on tmp directory: `%1$s`.', 'comet-cache'), $dir_temp));
        }
        if ($delete_dir_too) {
            if (!rmdir($dir)) {
                throw new \Exception(sprintf(__('Unable to delete directory: `%1$s`.', 'comet-cache'), $dir));
            }
            ++$counter; // Increment counter for each directory we delete.
        }
        /* ------- End lock state... ------------- */

        $this->cacheUnlock($cache_lock); // Release.

        return $counter;
    }

    /**
     * Erase all files/dirs from a directory (for all schemes/hosts);
     *    including `[SHORT_NAME]-` prefixed files; or anything else for that matter.
     *
     * WARNING: This does NO LOCKING and NO ATOMIC deletions.
     *
     * @since 150821 Improving recovery under stress.
     *
     * @param string $dir The directory from which to erase files/dirs.
     *
     *    SECURITY: This directory MUST be located inside the `/wp-content/` directory.
     *    Also, it MUST be a sub-directory of `/wp-content/`, NOT the directory itself.
     *    Also, it cannot be: `mu-plugins`, `themes`, or `plugins`.
     * @param bool $erase_dir_too Erase parent? i.e., erase the `$dir` itself also?
     *
     * @throws \Exception If unable to erase a file/directory for any reason.
     *
     * @return int Total files/directories erased by this routine (if any).
     */
    public function eraseAllFilesDirsIn($dir, $erase_dir_too = false)
    {
        $counter = 0; // Initialize.

        if (!($dir = trim((string) $dir)) || !is_dir($dir)) {
            return $counter; // Nothing to do.
        }
        $dir                  = $this->nDirSeps($dir);
        $wp_content_dir       = $this->nDirSeps(WP_CONTENT_DIR);
        $wp_content_dir_regex = preg_quote($wp_content_dir, '/');

        if (!preg_match('/^'.$wp_content_dir_regex.'\/[^\/]+/ui', $dir)) {
            return $counter; // Security flag; do nothing in this case.
        }
        if (preg_match('/^'.$wp_content_dir_regex.'\/(?:mu\-plugins|themes|plugins)(?:\/|$)/ui', $dir)) {
            return $counter; // Security flag; do nothing in this case.
        }
        clearstatcache(); // Clear stat cache to be sure we have a fresh start below.

        foreach (($_dir_regex_iteration = $this->dirRegexIteration($dir, '/.+/u')) as $_resource) {
            $_resource_type = $_resource->getType();
            $_sub_path_name = $_resource->getSubpathname();
            $_path_name     = $_resource->getPathname();

            switch ($_resource_type) {// Based on type; i.e., `link`, `file`, `dir`.
                case 'link': // Symbolic links; i.e., 404 errors.

                    if (!unlink($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to erase symlink: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each link we erase.

                    break; // Break switch handler.

                case 'file': // Regular files; i.e., not symlinks.

                    if (!unlink($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to erase file: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    ++$counter; // Increment counter for each file we erase.

                    break; // Break switch handler.

                case 'dir': // A regular directory; i.e., not a symlink.

                    if (!rmdir($_path_name)) {
                        throw new \Exception(sprintf(__('Unable to erase dir: `%1$s`.', 'comet-cache'), $_path_name));
                    }
                    # ++$counter; // Increment counter for each directory we erase. ~ NO don't do that here.

                    break; // Break switch handler.

                default: // Something else that is totally unexpected here.
                    throw new \Exception(sprintf(__('Unexpected resource type: `%1$s`.', 'comet-cache'), $_resource_type));
            }
        }
        unset($_dir_regex_iteration, $_resource, $_resource_type, $_sub_path_name, $_path_name); // Housekeeping.

        if ($erase_dir_too) {
            if (!rmdir($dir)) {
                throw new \Exception(sprintf(__('Unable to erase directory: `%1$s`.', 'comet-cache'), $dir));
            }
            ++$counter; // Increment counter for each directory we erase.
        }
        return $counter;
    }

    /**
     * Try to erase all files/dirs from a directory (for all schemes/hosts);
     *    including `[SHORT_NAME]-` prefixed files; or anything else for that matter.
     *
     * WARNING: This does NO LOCKING and NO ATOMIC deletions.
     *
     * @since 150821 Improving recovery under stress.
     *
     * @param string $dir The directory from which to erase files/dirs.
     *
     *    SECURITY: This directory MUST be located inside the `/wp-content/` directory.
     *    Also, it MUST be a sub-directory of `/wp-content/`, NOT the directory itself.
     *    Also, it cannot be: `mu-plugins`, `themes`, or `plugins`.
     * @param bool $erase_dir_too Erase parent? i.e., erase the `$dir` itself also?
     *
     * @return int Total files/directories erased by this routine (if any).
     */
    public function tryErasingAllFilesDirsIn($dir, $erase_dir_too = false)
    {
        $counter = 0; // Initialize counter.
        try {
            $counter += $this->eraseAllFilesDirsIn($dir, $erase_dir_too);
        } catch (\Exception $exception) {
            // Fail softly.
        }
        return $counter;
    }
}
