<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait WcpSitemapUtils
{
    /**
     * Automatically clears cache files related to XML sitemaps.
     *
     * @since 150422 Rewrite.
     *
     * @throws \Exception If a clear failure occurs.
     *
     * @return int Total files cleared by this routine (if any).
     *
     * @note Unlike many of the other `auto_` methods, this one is NOT currently
     *    attached to any hooks. However, it is called upon by {@link autoClearPostCache()}.
     */
    public function autoClearXmlSitemapsCache()
    {
        $counter = 0; // Initialize.

        if (!is_null($done = &$this->cacheKey('autoClearXmlSitemapsCache'))) {
            return $counter; // Already did this.
        }
        $done = true; // Flag as having been done.

        if (!$this->options['enable']) {
            return $counter; // Nothing to do.
        }
        if (!$this->options['cache_clear_xml_sitemaps_enable']) {
            return $counter; // Nothing to do.
        }
        if (!$this->options['cache_clear_xml_sitemap_patterns']) {
            return $counter; // Nothing to do.
        }
        if (!is_dir($cache_dir = $this->cacheDir())) {
            return $counter; // Nothing to do.
        }
        if (!($regex_frags = $this->buildHostCachePathRegexFragsFromWcUris($this->options['cache_clear_xml_sitemap_patterns'], ''))) {
            return $counter; // There are no patterns to look for.
        }
        $regex = $this->buildHostCachePathRegex('', '\/'.$regex_frags.'\.');
        $counter += $this->clearFilesFromHostCacheDir($regex);

        if ($counter && is_admin() && (!IS_PRO || $this->options['change_notifications_enable'])) {
            $this->enqueueNotice(sprintf(__('Found %1$s in the cache for XML sitemaps; auto-clearing.', 'comet-cache'), esc_html($this->i18nFiles($counter))), ['combinable' => true]);
        }
        return $counter;
    }
}
