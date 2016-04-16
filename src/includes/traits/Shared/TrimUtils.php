<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait TrimUtils
{
    /**
     * Trims strings deeply.
     *
     * @since 150422 Rewrite.
     *
     * @param mixed  $values      Any value can be converted into a trimmed string.
     *                            Actually, objects can't, but this recurses into objects.
     * @param string $chars       Specific chars to trim.
     *                            Defaults to PHP's trim: " \r\n\t\0\x0B". Use an empty string to bypass.
     * @param string $extra_chars Additional chars to trim.
     *
     * @return string|array|object Trimmed string, array, object.
     */
    public function trimDeep($values, $chars = '', $extra_chars = '')
    {
        if (is_array($values) || is_object($values)) {
            foreach ($values as $_key => &$_values) {
                $_values = $this->trimDeep($_values, $chars, $extra_chars);
            }
            unset($_key, $_values); // Housekeeping.

            return $values;
        }
        $string      = (string) $values;
        $chars       = (string) $chars;
        $extra_chars = (string) $extra_chars;

        $chars = isset($chars[0]) ? $chars : " \r\n\t\0\x0B";
        $chars = $chars.$extra_chars; // Concatenate.

        return trim($string, $chars);
    }
}
