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
            //
        } elseif (!defined('WP_CACHE') || !WP_CACHE || !COMET_CACHE_ENABLE) {
            return; // Not enabled in `wp-config.php` or otherwise.
        } elseif (defined('WP_INSTALLING') || defined('RELOCATE')) {
            return; // Not applicable; installing and/or relocating.
            //
        } elseif (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
            return; // Not applicable; bypass API requests.
        } elseif (defined('REST_REQUEST') && REST_REQUEST) {
            return; // Not applicable; bypass API requests.
        }
        // Note: `REST_REQUEST` is only here as a way of future-proofing the software.
        // Ideally, we could catch all API requests here to avoid any overhead in processing.
        // I suspect this will be the case in a future release of WordPress.

        // For now, `REST_REQUEST` is not defined by WP until later in the `parse_request` phase.
        // Therefore, this check by itself is not enough to avoid all REST requests at this time.
        // See: `traits/Ac/ObUtils.php` for additional checks for `REST_REQUEST` API calls.

        // `XMLRPC_REQUEST` on the other hand, is set very early via `xmlrpc.php`. So no issue.
        // -------------------------------------------------------------------------------------------------------------

        $this->is_running = true;
        $this->timer      = microtime(true);

        $this->loadAcPlugins();
        $this->registerShutdownFlag();
        $this->maybeIgnoreUserAbort();
        $this->maybeStopBrowserCaching();
        
        $this->maybeStartOutputBuffering();
    }
}
