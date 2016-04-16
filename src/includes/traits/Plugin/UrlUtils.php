<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait UrlUtils
{
    /**
     * URL to a Comet Cache plugin file.
     *
     * @since 150422 Rewrite.
     *
     * @param string $file   Optional file path; relative to plugin directory.
     * @param string $scheme Optional URL scheme; defaults to the current scheme.
     *
     * @return string URL to plugin directory; or to the specified `$file` if applicable.
     */
    public function url($file = '', $scheme = '')
    {
        $url = rtrim(plugin_dir_url(PLUGIN_FILE), '/');
        $url .= (string) $file;

        if ($scheme) {
            $url = set_url_scheme($url, (string) $scheme);
        }
        return $url;
    }

    /**
     * Retrieves the home URL for a given site preserving the home URL scheme.
     *
     * @since 160416 Improving Auto-Cache Engine Sitemap routines.
     *
     * @param int $blog_id (Optional) Blog ID. Default null (current blog).
     *
     * @return string $url Home URL link with Home URL scheme preserved.
     */
    public function getHomeUrlWithHomeScheme($blog_id = null)
    {
        if (empty($blog_id) || !is_multisite()) {
            $url = get_option('home');
        } else {
            switch_to_blog($blog_id);
            $url = get_option('home');
            restore_current_blog();
        }

        $url = set_url_scheme($url, parse_url($url, PHP_URL_SCHEME));

        return $url;
    }
}
