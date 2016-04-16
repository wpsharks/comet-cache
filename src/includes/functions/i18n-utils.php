<?php
namespace WebSharks\CometCache {

    /**
     * Polyfill for {@link \__()}.
     *
     * @since 150422 Rewrite.
     *
     * @param string $string      String to translate.
     * @param string $text_domain Plugin text domain.
     *
     * @return string Possibly translated string.
     */
    function __($string, $text_domain)
    {
        static $exists; // Cache.

        if ($exists || ($exists = function_exists('__'))) {
            return \__($string, $text_domain);
        }
        return $string; // Not possible (yet).
    }
}
namespace WebSharks\CometCache\Traits\Ac {

    function __($string, $text_domain)
    {
        return \WebSharks\CometCache\__($string, $text_domain);
    }
}
namespace WebSharks\CometCache\Traits\Shared {

    function __($string, $text_domain)
    {
        return \WebSharks\CometCache\__($string, $text_domain);
    }
}
