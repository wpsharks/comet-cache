<?php
namespace WebSharks\CometCache\Classes;

use WebSharks\CometCache\Traits;

/**
 * Advanced cache.
 *
 * @since 150422 Rewrite.
 */
class AdvancedCache extends AbsBaseAp
{
    /*[.build.php-auto-generate-use-Traits]*/
    use Traits\Ac\AbortUtils;
    use Traits\Ac\AcPluginUtils;
    use Traits\Ac\BrowserUtils;
    use Traits\Ac\NcDebugUtils;
    use Traits\Ac\ObUtils;
    use Traits\Ac\PostloadUtils;
    use Traits\Ac\ShutdownUtils;
    /*[/.build.php-auto-generate-use-Traits]*/

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

        if (!defined('WP_CACHE') || !WP_CACHE || !COMET_CACHE_ENABLE) {
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
