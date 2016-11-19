<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait ArrayUtils
{
    /**
     * Sorts by key.
     *
     * @since 161119 Array utils.
     *
     * @param array $array Input array.
     * @param int   $flags Defaults to `SORT_REGULAR`.
     *
     * @return array Output array.
     */
    public function ksortDeep($array, $flags = SORT_REGULAR)
    {
        $array = (array) $array;
        $flags = (int) $flags;

        ksort($array, $flags);

        foreach ($array as $_key => &$_value) {
            if (is_array($_value)) {
                $_value = $this->ksortDeep($_value, $flags);
            }
        } // unset($_key, $_value); // Housekeeping.

        return $array;
    }
}
