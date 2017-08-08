<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait CacheIoUtils
{
    /**
     * Cache read.
     *
     * @since 17xxxx IO utils.
     *
     * @return array `['headers' => [], 'output' => '', 'via' => '']`
     */
    public function cacheRead()
    {
        # Validation.

        if (!$this->cache_file) {
            return []; // Not possible.
        }
        # Check memory first, if applicable.
        # This avoids repeated disk reads in favor of RAM.

        

        # Check the filesystem next; slightly more expensive.
        # This requires repeated disk reads; on a busy site it can add up.

        if (is_file($this->cache_file) && ($this->cache_max_age_disabled || filemtime($this->cache_file) >= $this->cache_max_age)) {
            list($headers, $output) = explode('<!--headers-->', (string) file_get_contents($this->cache_file), 2);
            $headers                = (array) unserialize($headers);
            $via                    = 'filesystem';

            // NOTE: There is no need to look at `$nonce_expires_early` when reading from RAM above.
            // This is because an early expiration of nonce data is already baked into the memory cache entry.
            $nonce_expires_early = !COMET_CACHE_CACHE_NONCE_VALUES && filemtime($this->cache_file) < $this->nonce_cache_max_age && preg_match('/\b(?:_wpnonce|akismet_comment_nonce)\b/u', $output);

            if ($nonce_expires_early) {  // Ignoring `cache_max_age_disabled` in favor of better security.
                return [];  // This refuses to read a cache file that contains possibly expired nonce tokens.
            }
            return compact('headers', 'output', 'via');
        }
        # Otherwise, failure.

        return []; // Not possible.
    }

    /**
     * Cache write.
     *
     * @since 17xxxx IO utils.
     *
     * @param string $output Output buffer.
     *
     * @throws \Exception on write failure.
     *
     * @return string|false Output or `false` = no change.
     */
    public function cacheWrite($output)
    {
        # Validation.

        if (!($output = (string) $output)) {
            return false; // Not possible.
        } elseif (!$this->cache_file) {
            return false; // Not possible.
        }
        # Initialize vars.
        # Also locks the cache.

        $time                = time();
        $lock                = $this->cacheLock();
        $dir                 = dirname($this->cache_file);
        $tmp                 = $this->addTmpSuffix($this->cache_file);
        $nonce_expires_early = !COMET_CACHE_CACHE_NONCE_VALUES && preg_match('/\b(?:_wpnonce|akismet_comment_nonce)\b/u', $output);

        # Cache directory checks.
        # Auto-creates cache directories.

        if (!is_dir(COMET_CACHE_DIR)) {
            if (mkdir(COMET_CACHE_DIR, 0775, true) && !is_file(COMET_CACHE_DIR.'/.htaccess')) {
                file_put_contents(COMET_CACHE_DIR.'/.htaccess', $this->htaccess_deny);
            } // ↑ Creates & secures the cache directory.
        }
        if (!is_dir($dir)) { // New file dir.
            $dir_writable = mkdir($dir, 0775, true);
        }
        if (empty($dir_writable) && !is_writable($dir)) {
            throw new \Exception(sprintf(__('Cache directory not writable. %1$s needs this directory please: `%2$s`. Set permissions to `755` or higher; `777` might be needed in some cases.', 'comet-cache'), NAME, $dir));
        }
        # If it's a 404 error and the main 404 error file already exists, we can save time w/ just a symlink.
        # Note: This being the first time a 404 error has occurred for this specific location not found by WordPress.

        if ($this->is_404 && is_file($this->cache_file_404)) {
            if (!(symlink($this->cache_file_404, $tmp) && rename($tmp, $this->cache_file))) {
                throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), $this->cache_file, $this->cache_file_404, COMET_CACHE_DIR));
            }
            $this->cacheUnlock($lock);
            $this->maybeSetDebugInfo($this::NC_DEBUG_1ST_TIME_404_SYMLINK);
            return false; // No change in output buffer.
        }
        # Otherwise, construct & store a new cache file.
        # Also compresses HTML (if applicable) and adds debug notes.

        

        if (COMET_CACHE_DEBUGGING_ENABLE && $this->isHtmlXmlDoc($output)) {
            $total_time = number_format(microtime(true) - $this->timer, 5, '.', '');

            $DebugNotes = new Classes\Notes();
            $DebugNotes->addAsciiArt(sprintf(__('%1$s is Fully Functional', 'comet-cache'), NAME));
            $DebugNotes->addLineBreak();

            if (IS_PRO && COMET_CACHE_WHEN_LOGGED_IN && $this->user_token) {
                $DebugNotes->add(__('Cache File User Token', 'comet-cache'), $this->user_token);
            }
            if (IS_PRO && COMET_CACHE_MOBILE_ADAPTIVE_SALT_ENABLE && COMET_CACHE_MOBILE_ADAPTIVE_SALT && $this->mobile_adaptive_salt) {
                // Note: Not using `$this->mobile_adaptive_salt` here. Instead, generating a human readable variation.
                $DebugNotes->add(__('Cache File for Mobile Device', 'comet-cache'), $this->fillUaTokens(COMET_CACHE_MOBILE_ADAPTIVE_SALT, false));
            }
            $DebugNotes->add(__('Cache File Version Salt', 'comet-cache'), $this->version_salt ? $this->version_salt : __('n/a', 'comet-cache'));

            $DebugNotes->addLineBreak();

            $DebugNotes->add(__('Cache File URL', 'comet-cache'), $this->is_404 ? __('404 [error document]', 'comet-cache') : $this->protocol.$this->host_token.$_SERVER['REQUEST_URI']);
            $DebugNotes->add(__('Cache File Path', 'comet-cache'), str_replace(WP_CONTENT_DIR, '', $this->is_404 ? $this->cache_file_404 : $this->cache_file));

            $DebugNotes->addLineBreak();

            $DebugNotes->add(__('Cache File Generated Via', 'comet-cache'), IS_PRO && $this->isAutoCacheEngine() ? __('Auto-Cache Engine', 'comet-cache') : __('HTTP request', 'comet-cache'));
            $DebugNotes->add(__('Cache File Generated On', 'comet-cache'), date('M jS, Y @ g:i a T'));
            $DebugNotes->add(__('Cache File Generated In', 'comet-cache'), sprintf(__('%1$s seconds', 'comet-cache'), $total_time));

            $DebugNotes->addLineBreak();

            if ($nonce_expires_early) { // Expires early?
                $DebugNotes->add(__('Cache File Expires Early', 'comet-cache'), __('yes, due to nonce in markup', 'comet-cache'));
                $DebugNotes->add(__('Cache File Expires On', 'comet-cache'), date('M jS, Y @ g:i a T', $time + ($time - $this->nonce_cache_max_age)));
                $DebugNotes->add(__('Cache File Auto-Rebuild On', 'comet-cache'), date('M jS, Y @ g:i a T', $time + ($time - $this->nonce_cache_max_age)));
            } else {
                $DebugNotes->add(__('Cache File Expires On', 'comet-cache'), date('M jS, Y @ g:i a T', $time + ($time - $this->cache_max_age)));
                $DebugNotes->add(__('Cache File Auto-Rebuild On', 'comet-cache'), date('M jS, Y @ g:i a T', $time + ($time - $this->cache_max_age)));
            }
            $output .= "\n".$DebugNotes->asHtmlComments();
        }
        $cache = serialize($this->cacheableHeadersList());
        $cache .= '<!--headers-->'; // Headers separator.
        $cache .= $output; // Compressed output w/ debug notes.

        if ($this->is_404) { // Create main 404 error file w/ cache contents.
            if (file_put_contents($tmp, $cache) && rename($tmp, $this->cache_file_404)) {
                if (!(symlink($this->cache_file_404, $tmp) && rename($tmp, $this->cache_file))) {
                    throw new \Exception(sprintf(__('Unable to create symlink: `%1$s` » `%2$s`. Possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), $this->cache_file, $this->cache_file_404, COMET_CACHE_DIR));
                }
                $this->cacheUnlock($lock);
                return $output;
            }
        } elseif (file_put_contents($tmp, $cache) && rename($tmp, $this->cache_file)) {
            
            $this->cacheUnlock($lock);
            return $output;
        }
        @unlink($tmp); // Clean this up (if it exists); and throw an exception with information for the site owner.
        throw new \Exception(sprintf(__('%1$s: failed to write cache file for: `%2$s`; possible permissions issue (or race condition), please check your cache directory: `%3$s`.', 'comet-cache'), NAME, $_SERVER['REQUEST_URI'], COMET_CACHE_DIR));
    }
}
