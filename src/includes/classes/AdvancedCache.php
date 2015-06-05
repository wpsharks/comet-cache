<?php
namespace WebSharks\ZenCache;

/**
 * Advanced cache.
 *
 * @since 150422 Rewrite.
 */
class AdvancedCache extends AbsBaseAp
{
    /**
     * Flagged as `TRUE` if running.
     *
     * @since 150422 Rewrite.
     *
     * @type bool `TRUE` if running.
     */
    public $is_running = false;

    /**
     * Microtime; for debugging.
     *
     * @since 150422 Rewrite.
     *
     * @type float Microtime; for debugging.
     */
    public $timer = 0;

    /**
     * Class constructor/cache handler.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        parent::__construct();

        $closures_dir = dirname(dirname(__FILE__)).'/closures/Ac';
        $self         = $this; // Reference for closures.

        foreach (scandir($closures_dir) as $_closure) {
            if (substr($_closure, -4) === '.php') {
                require $closures_dir.'/'.$_closure;
            }
        }
        unset($_closure); // Housekeeping.

        if (!defined('WP_CACHE') || !WP_CACHE || !ZENCACHE_ENABLE) {
            return; // Not enabled.
        }
        if (defined('WP_INSTALLING') || defined('RELOCATE')) {
            return; // N/A; installing|relocating.
        }
        $this->is_running = true;
        $this->timer      = microtime(true);

        $this->loadAcPlugins();
        $this->registerShutdownFlag();
        $this->maybeIgnoreUserAbort();
        $this->maybeStopBrowserCaching();
        
        $this->maybeStartOutputBuffering();
    }
}
