<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait ClientSideUtils
{
    /**
     * Sends no-cache headers.
     *
     * @since 150422 Rewrite.
     * @since 151220 Enhancing.
     * @since 161119 Enhancing.
     */
    public function maybeStopBrowserCaching()
    {
        $short_name_lc = mb_strtolower(SHORT_NAME); // Needed below.

        switch (defined('COMET_CACHE_ALLOW_CLIENT_SIDE_CACHE') ? (bool) COMET_CACHE_ALLOW_CLIENT_SIDE_CACHE : false) {
            case true: // If global config allows; check exclusions.

                if (isset($_GET[$short_name_lc.'ABC'])) {
                    if (!filter_var($_GET[$short_name_lc.'ABC'], FILTER_VALIDATE_BOOLEAN)) {
                        return $this->sendNoCacheHeaders(); // Disallow.
                    } // Else, allow client-side caching because `ABC` is a true-ish value.
                } elseif (COMET_CACHE_EXCLUDE_CLIENT_SIDE_URIS && (empty($_SERVER['REQUEST_URI']) || preg_match(COMET_CACHE_EXCLUDE_CLIENT_SIDE_URIS, $_SERVER['REQUEST_URI']))) {
                    return $this->sendNoCacheHeaders(); // Disallow.
                }
                return; // Allow client-side caching; default behavior in this mode.

            case false: // Global config disallows; check inclusions.

                if (isset($_GET[$short_name_lc.'ABC'])) {
                    if (filter_var($_GET[$short_name_lc.'ABC'], FILTER_VALIDATE_BOOLEAN)) {
                        return; // Allow, because `ABC` is a false-ish value.
                    } // Else, disallow client-side caching because `ABC` is a true-ish value.
                }
                return $this->sendNoCacheHeaders(); // Disallow; default behavior in this mode.
        }
    }
}
