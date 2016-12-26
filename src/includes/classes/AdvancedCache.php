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
    use Traits\Ac\ClientSideUtils;
    use Traits\Ac\NcDebugUtils;
    use Traits\Ac\ObUtils;
    use Traits\Ac\PostloadUtils;
    use Traits\Ac\ShutdownUtils;
    /*[/.build.php-auto-generate-use-Traits]*/

    /**
     * Microtime.
     *
     * @since 150422 Rewrite.
     *
     * @type float Microtime.
     */
    public $timer = 0;

    /**
     * True if running.
     *
     * @since 150422 Rewrite.
     *
     * @type bool True if running.
     */
    public $is_running = false;

    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     * @since 161226 Version check.
     */
    public function __construct()
    {
        parent::__construct();

        if (!defined('COMET_CACHE_AC_FILE_VERSION')) {
            return; // Missing; wait for update.
        } elseif (COMET_CACHE_AC_FILE_VERSION !== VERSION) {
            return; // Version mismatch; wait for update.
        } elseif (!defined('WP_CACHE') || !WP_CACHE || !COMET_CACHE_ENABLE) {
            return; // Not enabled in `wp-config.php` or otherwise.
        } elseif (defined('WP_INSTALLING') || defined('RELOCATE')) {
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
