<?php
namespace WebSharks\ZenCache;

/*
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
$self->wpContentBaseDirTo = function ($rel_dir_file) use ($self) {
    $rel_dir_file = trim((string) $rel_dir_file, '\\/'." \t\n\r\0\x0B");

    if (empty($self->options['base_dir'])) {
        throw new \Exception(__('Missing `base_dir` option value.', SLUG_TD));
    }
    $wp_content_base_dir_to = WP_CONTENT_DIR.'/'.$self->options['base_dir'];

    if (isset($rel_dir_file[0])) {
        $wp_content_base_dir_to .= '/'.$rel_dir_file;
    }
    return $wp_content_base_dir_to;
};

/*
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
$self->basePathTo = function ($rel_dir_file) use ($self) {
    $rel_dir_file = trim((string) $rel_dir_file, '\\/'." \t\n\r\0\x0B");

    if (empty($self->options['base_dir'])) {
        throw new \Exception(__('Missing `base_dir` option value.', SLUG_TD));
    }
    $base_path_to = $self->options['base_dir'];

    if (isset($rel_dir_file[0])) {
        $base_path_to .= '/'.$rel_dir_file;
    }
    return $base_path_to;
};
