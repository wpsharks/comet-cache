<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait CacheLockUtils
{
    /**
     * Get an exclusive lock on the cache directory.
     *
     * @since 150422 Rewrite.
     *
     * @throws \Exception If {@link \sem_get()} not available and there's
     *                    no writable tmp directory for {@link \flock()} either.
     * @throws \Exception If unable to obtain an exclusive lock by any available means.
     * @return array Lock type & resource handle needed to unlock later or FALSE if disabled by filter.
     *
     *
     * @note This call is blocking; i.e. it will not return a lock until a lock becomes possible.
     *    In short, this will block the caller until such time as write access becomes possible.
     */
    public function cacheLock()
    {
        if ($this->applyWpFilters(GLOBAL_NS.'\\share::disable_cache_locking', false)
            || $this->applyWpFilters(GLOBAL_NS.'_disable_cache_locking', false)
        ) {
            return false; // Disabled cache locking.
        }
        if (!($wp_config_file = $this->findWpConfigFile())) {
            throw new \Exception(__('Unable to find the wp-config.php file.', 'comet-cache'));
        }
        $lock_type = 'flock'; // Default lock type.
        $lock_type = $this->applyWpFilters(GLOBAL_NS.'\\share::cache_lock_lock_type', $lock_type);
        $lock_type = $this->applyWpFilters(GLOBAL_NS.'_cache_lock_type', $lock_type);

        if (!in_array($lock_type, ['flock', 'sem'], true)) {
            $lock_type = 'flock'; // Default lock type.
        }
        if ($lock_type === 'sem' && $this->functionIsPossible('sem_get')) {
            if (($ipc_key = ftok($wp_config_file, 'w'))) {
                if (($resource = sem_get($ipc_key, 1)) && sem_acquire($resource)) {
                    return ['type' => 'sem', 'resource' => $resource];
                }
            }
        }
        if (!($tmp_dir = $this->getTmpDir())) {
            throw new \Exception(__('No writable tmp directory.', 'comet-cache'));
        }
        $inode_key = fileinode($wp_config_file);
        $mutex     = $tmp_dir.'/'.SLUG_TD.'-'.$inode_key.'.lock';

        if (!($resource = fopen($mutex, 'wb')) || !flock($resource, LOCK_EX)) {
            throw new \Exception(__('Unable to obtain an exclusive lock.', 'comet-cache'));
        }

        @chmod($mutex, 0666); // See https://git.io/v2WAt

        return ['type' => 'flock', 'resource' => $resource];
    }

    /**
     * Release an exclusive lock on the cache directory.
     *
     * @since 150422 Rewrite. Updated 151002 to remove the `array` typecast.
     *
     * @param array|mixed $lock Type & resource.
     */
    public function cacheUnlock($lock)
    {
        if (!is_array($lock)) {
            return; // Not possible.
            // Or, they disabled cache locking.
        }
        if (empty($lock['type']) || empty($lock['resource'])) {
            return; // Not possible.
        }
        if (!is_resource($lock['resource'])) {
            return; // Not possible.
        }
        if ($lock['type'] === 'sem') {
            sem_release($lock['resource']);
        } elseif ($lock['type'] === 'flock') {
            flock($lock['resource'], LOCK_UN);
            fclose($lock['resource']);
        }
    }
}
