<?php
namespace WebSharks\CometCache\Traits\Ac;

use WebSharks\CometCache\Classes;

trait BrowserUtils
{
    /**
     * Sends no-cache headers (if applicable).
     *
     * @since 150422 Rewrite. Enhanced/altered 151220.
     */
    public function maybeStopBrowserCaching()
    {
        switch ((bool) COMET_CACHE_ALLOW_BROWSER_CACHE) {

            case true: // If global config allows, check exclusions.

                if (isset($_GET[strtolower(SHORT_NAME).'ABC'])) {
                    if (!filter_var($_GET[strtolower(SHORT_NAME).'ABC'], FILTER_VALIDATE_BOOLEAN)) {
                        return $this->sendNoCacheHeaders(); // Disallow.
                    } // Else, allow client-side caching; because `ABC` is a true-ish value.
                    // ↑ Note that exclusion patterns are ignored in this case, in favor of `ABC`.
                } elseif (COMET_CACHE_EXCLUDE_CLIENT_SIDE_URIS && preg_match(COMET_CACHE_EXCLUDE_CLIENT_SIDE_URIS, $_SERVER['REQUEST_URI'])) {
                    return $this->sendNoCacheHeaders(); // Disallow.
                }
                return; // Allow browser caching; default behavior in this mode.

            case false: // Global config disallows; check inclusions.

                if (isset($_GET[strtolower(SHORT_NAME).'ABC'])) {
                    if (filter_var($_GET[strtolower(SHORT_NAME).'ABC'], FILTER_VALIDATE_BOOLEAN)) {
                        return; // Allow, because `ABC` is a false-ish value.
                    } // Else, disallow client-side caching; because `ABC` is a true-ish value.
                    // ↑ Note that inclusion patterns are ignored in this case, in favor of `ABC`.
                }
                return $this->sendNoCacheHeaders(); // Disallow; default behavior in this mode.
        }
    }
}
