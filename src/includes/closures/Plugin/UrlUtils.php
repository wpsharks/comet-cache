<?php
namespace WebSharks\ZenCache;

/*
 * URL to a ZenCache plugin file.
 *
 * @since 150422 Rewrite.
 *
 * @param string $file   Optional file path; relative to plugin directory.
 * @param string $scheme Optional URL scheme; defaults to the current scheme.
 *
 * @return string URL to plugin directory; or to the specified `$file` if applicable.
 */
$self->url = function ($file = '', $scheme = '') use ($self) {
    $url = rtrim(plugin_dir_url(PLUGIN_FILE), '/');
    $url .= (string) $file;

    if ($scheme) {
        $url = set_url_scheme($url, (string) $scheme);
    }
    return $url;
};
