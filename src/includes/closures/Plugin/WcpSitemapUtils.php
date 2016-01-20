<?php
namespace WebSharks\ZenCache;

/*
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
$self->autoClearXmlSitemapsCache = function () use ($self) {
    $counter = 0; // Initialize.

    if (!is_null($done = &$self->cacheKey('autoClearXmlSitemapsCache'))) {
        return $counter; // Already did this.
    }
    $done = true; // Flag as having been done.

    if (!$self->options['enable']) {
        return $counter; // Nothing to do.
    }
    if (!$self->options['cache_clear_xml_sitemaps_enable']) {
        return $counter; // Nothing to do.
    }
    if (!$self->options['cache_clear_xml_sitemap_patterns']) {
        return $counter; // Nothing to do.
    }
    if (!is_dir($cache_dir = $self->cacheDir())) {
        return $counter; // Nothing to do.
    }
    if (!($regex_frags = $self->buildHostCachePathRegexFragsFromWcUris($self->options['cache_clear_xml_sitemap_patterns'], ''))) {
        return $counter; // There are no patterns to look for.
    }
    $regex = $self->buildHostCachePathRegex('', '\/'.$regex_frags.'\.');
    $counter += $self->clearFilesFromHostCacheDir($regex);

    if ($counter && is_admin() && (!IS_PRO || $self->options['change_notifications_enable'])) {
        $self->enqueueNotice('<img src="'.esc_attr($self->url('/src/client-s/images/clear.png')).'" style="float:left; margin:0 10px 0 0; border:0;" />'.
                              sprintf(__('<strong>%1$s:</strong> detected changes. Found %2$s in the cache for XML sitemaps; auto-clearing.', 'zencache'), esc_html(NAME), esc_html($self->i18nFiles($counter))));
    }
    return $counter;
};
