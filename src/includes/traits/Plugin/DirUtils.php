<?php
namespace WebSharks\CometCache\Traits\Plugin;

use WebSharks\CometCache\Classes;

trait DirUtils
{
    /**
     * This constructs an absolute server directory path (no trailing slashes);
     *    which is always nested into {@link \WP_CONTENT_DIR} and the configured `base_dir` option value.
     *
     * @since 150422 Rewrite.
     *
     * @param string $rel_dir_file A sub-directory or file; relative location please.
     *
     * @throws \Exception If `base_dir` is empty when this method is called upon;
     *                    i.e. if you attempt to call upon this method before {@link setup()} runs.
     *
     * @return string The full absolute server path to `$rel_dir_file`.
     */
    public function wpContentBaseDirTo($rel_dir_file)
    {
        $rel_dir_file = trim((string) $rel_dir_file, '\\/'." \t\n\r\0\x0B");

        if (empty($this->options['base_dir'])) {
            throw new \Exception(__('Missing `base_dir` option value.', 'comet-cache'));
        }
        $wp_content_base_dir_to = WP_CONTENT_DIR.'/'.$this->options['base_dir'];

        if (isset($rel_dir_file[0])) {
            $wp_content_base_dir_to .= '/'.$rel_dir_file;
        }
        return $wp_content_base_dir_to;
    }

    /**
     * This constructs a relative/base directory path (no leading/trailing slashes).
     *    Always relative to {@link \WP_CONTENT_DIR}. Depends on the configured `base_dir` option value.
     *
     * @since 150422 Rewrite.
     *
     * @param string $rel_dir_file A sub-directory or file; relative location please.
     *
     * @throws \Exception If `base_dir` is empty when this method is called upon;
     *                    i.e. if you attempt to call upon this method before {@link setup()} runs.
     *
     * @return string The relative/base directory path to `$rel_dir_file`.
     */
    public function basePathTo($rel_dir_file)
    {
        $rel_dir_file = trim((string) $rel_dir_file, '\\/'." \t\n\r\0\x0B");

        if (empty($this->options['base_dir'])) {
            throw new \Exception(__('Missing `base_dir` option value.', 'comet-cache'));
        }
        $base_path_to = $this->options['base_dir'];

        if (isset($rel_dir_file[0])) {
            $base_path_to .= '/'.$rel_dir_file;
        }
        return $base_path_to;
    }

    /**
     * Get the absolute filesystem path to the root of the WordPress installation.
     *
     * Copied verbatim from get_home_path() in wp-admin/includes/file.php
     *
     * @since 151114 Adding `.htaccess` tweaks.
     *
     * @return string Full filesystem path to the root of the WordPress installation
     */
    public function wpHomePath()
    {
        $home    = set_url_scheme(get_option('home'), 'http');
        $siteurl = set_url_scheme(get_option('siteurl'), 'http');
        if (!empty($home) && 0 !== strcasecmp($home, $siteurl) && !empty($_SERVER['SCRIPT_FILENAME'])) {
            $wp_path_rel_to_home = preg_replace('/'.preg_quote($home, '/').'/ui', '', $siteurl); /* $siteurl - $home */
            $pos                 = strripos(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), trailingslashit($wp_path_rel_to_home));
            $home_path           = mb_substr($_SERVER['SCRIPT_FILENAME'], 0, $pos);
            $home_path           = trailingslashit($home_path);
        } else {
            $home_path = ABSPATH;
        }
        return str_replace('\\', '/', $home_path);
    }
}
