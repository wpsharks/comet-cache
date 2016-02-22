<?php
namespace WebSharks\CometCache;

/*
 * String replace ONE time.
 *
 * @since 150422 Rewrite.
 *
 * @param string  $needle A string to search/replace.
 * @param string  $replace What to replace `$needle` with.
 * @param string  $haystack The string/haystack to search in.
 *
 * @param boolean $caSe_insensitive Defaults to a `FALSE` value.
 *    Pass this as `TRUE` to a caSe-insensitive search/replace.
 *
 * @return string The `$haystack`, with `$needle` replaced with `$replace` ONE time only.
 */
$self->strReplaceOnce = function ($needle, $replace, $haystack, $caSe_insensitive = false) use ($self) {
    $needle      = (string) $needle;
    $replace     = (string) $replace;
    $haystack    = (string) $haystack;
    $caSe_strpos = $caSe_insensitive ? 'stripos' : 'strpos';

    if (($needle_strpos = $caSe_strpos($haystack, $needle)) === false) {
        return $haystack; // Nothing to replace.
    }
    return (string) substr_replace($haystack, $replace, $needle_strpos, strlen($needle));
};

/*
 * String replace ONE time (caSe-insensitive).
 *
 * @since 150422 Rewrite.
 *
 * @param string $needle A string to search/replace.
 * @param string $replace What to replace `$needle` with.
 * @param string $haystack The string/haystack to search in.
 *
 * @return string The `$haystack`, with `$needle` replaced with `$replace` ONE time only.
 */
$self->strIreplaceOnce = function ($needle, $replace, $haystack) use ($self) {
    return $self->strReplaceOnce($needle, $replace, $haystack, true);
};
