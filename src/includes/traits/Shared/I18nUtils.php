<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait I18nUtils
{
    /**
     * `X file` or `X files`, translated w/ singlular/plural context.
     *
     * @since 150422 Rewrite.
     *
     * @param int $counter Total files; i.e. the counter.
     *
     * @return string The phrase `X file` or `X files`.
     */
    public function i18nFiles($counter)
    {
        $counter = (integer) $counter;
        return sprintf(_n('%1$s file', '%1$s files', $counter, 'comet-cache'), $counter);
    }

    /**
     * `X directory` or `X directories`, translated w/ singlular/plural context.
     *
     * @since 150422 Rewrite.
     *
     * @param int $counter Total directories; i.e. the counter.
     *
     * @return string The phrase `X directory` or `X directories`.
     */
    public function i18nDirs($counter)
    {
        $counter = (integer) $counter;
        return sprintf(_n('%1$s directory', '%1$s directories', $counter, 'comet-cache'), $counter);
    }

    /**
     * `X file/directory` or `X files/directories`, translated w/ singlular/plural context.
     *
     * @since 150422 Rewrite.
     *
     * @param int $counter Total files/directories; i.e. the counter.
     *
     * @return string The phrase `X file/directory` or `X files/directories`.
     */
    public function i18nFilesDirs($counter)
    {
        $counter = (integer) $counter;
        return sprintf(_n('%1$s file/directory', '%1$s files/directories', $counter, 'comet-cache'), $counter);
    }
}
