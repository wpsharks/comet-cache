<?php
namespace WebSharks\ZenCache;

/*
 * `X file` or `X files`, translated w/ singlular/plural context.
 *
 * @since 150422 Rewrite.
 *
 * @param integer $counter Total files; i.e. the counter.
 *
 * @return string The phrase `X file` or `X files`.
 */
$self->i18nFiles = function ($counter) use ($self) {
    $counter = (integer) $counter;
    return sprintf(_n('%1$s file', '%1$s files', $counter, SLUG_TD), $counter);
};

/*
 * `X directory` or `X directories`, translated w/ singlular/plural context.
 *
 * @since 150422 Rewrite.
 *
 * @param integer $counter Total directories; i.e. the counter.
 *
 * @return string The phrase `X directory` or `X directories`.
 */
$self->i18nDirs = function ($counter) use ($self) {
    $counter = (integer) $counter;
    return sprintf(_n('%1$s directory', '%1$s directories', $counter, SLUG_TD), $counter);
};

/*
 * `X file/directory` or `X files/directories`, translated w/ singlular/plural context.
 *
 * @since 150422 Rewrite.
 *
 * @param integer $counter Total files/directories; i.e. the counter.
 *
 * @return string The phrase `X file/directory` or `X files/directories`.
 */
$self->i18nFilesDirs = function ($counter) use ($self) {
    $counter = (integer) $counter;
    return sprintf(_n('%1$s file/directory', '%1$s files/directories', $counter, SLUG_TD), $counter);
};
