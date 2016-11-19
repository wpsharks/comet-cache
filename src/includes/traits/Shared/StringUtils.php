<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait StringUtils
{
    /**
     * Clips string(s) to X chars deeply.
     *
     * @since 151114 Adding string utils.
     *
     * @param mixed $value          Any input value.
     * @param int   $max_length     Defaults to a value of `80`.
     * @param bool  $force_ellipsis Defaults to a value of `FALSE`.
     *
     * @return string|array|object Clipped value.
     */
    public function clip($value, $max_length = 80, $force_ellipsis = false)
    {
        if (is_array($value) || is_object($value)) {
            foreach ($value as $_key => &$_value) {
                $_value = $this->clip($_value, $max_length, $force_ellipsis);
            }
            unset($_key, $_value); // Housekeeping.

            return $value;
        }
        if (!($string = (string) $value)) {
            return $string; // Empty.
        }
        $max_length = max(4, $max_length);

        $string = strip_tags($string);
        $string = preg_replace('/\s+/u', ' ', strip_tags($string));
        $string = trim($string); // Trim it up now.

        if (mb_strlen($string) > $max_length) {
            $string = (string) mb_substr($string, 0, $max_length - 3).'...';
        } elseif ($force_ellipsis && mb_strlen($string) + 3 > $max_length) {
            $string = (string) mb_substr($string, 0, $max_length - 3).'...';
        } else {
            $string .= $force_ellipsis ? '...' : '';
        }
        return $string;
    }

    /**
     * Mid-clips string(s) to X chars deeply.
     *
     * @since 151114 Adding string utils.
     *
     * @param mixed $value      Any input value.
     * @param int   $max_length Defaults to a value of `80`.
     *
     * @return string|array|object Mid-clipped value.
     */
    public function midClip($value, $max_length = 80)
    {
        if (is_array($value) || is_object($value)) {
            foreach ($value as $_key => &$_value) {
                $_value = $this->midClip($_value, $max_length);
            }
            unset($_key, $_value); // Housekeeping.

            return $value;
        }
        if (!($string = (string) $value)) {
            return $string; // Empty.
        }
        $max_length = max(4, $max_length);

        $string = strip_tags($string);
        $string = preg_replace('/\s+/u', ' ', strip_tags($string));
        $string = trim($string); // Trim it up now.

        if (mb_strlen($string) <= $max_length) {
            return $string; // Nothing to do.
        }
        $full_string     = $string;
        $half_max_length = floor($max_length / 2);

        $first_clip = $half_max_length - 3;
        $string     = $first_clip >= 1 // Something?
            ? mb_substr($full_string, 0, $first_clip).'...'
            : '...'; // Ellipsis only.

        $second_clip = mb_strlen($full_string) - ($max_length - mb_strlen($string));
        $string .= $second_clip >= 0 && $second_clip >= $first_clip
            ? mb_substr($full_string, $second_clip) : '';

        return $string;
    }

    /**
     * Multibyte `str_pad()`.
     *
     * @since 161119 Enhancing multibyte support.
     *
     * @param mixed  $value      Any input value.
     * @param int    $pad_length The required length of the string.
     * @param string $pad_string The string to pad with.
     * @param int    $pad_type   `STR_PAD_LEFT`, `STR_PAD_RIGHT`, `STR_PAD_BOTH`.
     *
     * @throws \Exception if unexpected `pad_type`
     *
     * @return string|array|object Output value.
     */
    public function strPad($value, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        if (is_array($value) || is_object($value)) {
            foreach ($value as $_key => &$_value) {
                $_value = $this->strPad($_value, $pad_length, $pad_string, $pad_type);
            } // unset($_key, $_value);
            return $value;
        }
        $string     = (string) $value;
        $pad_length = (int) $pad_length;
        $pad_string = (string) $pad_string;
        $mb_strlen  = mb_strlen($string);

        if ($pad_length < 0 || $pad_length <= $mb_strlen) {
            return $string; // Nothing to do.
        }
        $pad_string_mb_strlen = mb_strlen($pad_string);

        switch ($pad_type) {
            case STR_PAD_LEFT:
                $repeat = (int) ceil(max(0, $mb_strlen - $pad_string_mb_strlen) + $pad_length);
                $string = str_repeat($pad_string, $repeat).$string;
                return mb_substr($string, -$pad_length);

            case STR_PAD_RIGHT:
                $repeat = (int) ceil(max(0, $mb_strlen - $pad_string_mb_strlen) + $pad_length);
                $string = $string.str_repeat($pad_string, $repeat);
                return mb_substr($string, 0, $pad_length);

            case STR_PAD_BOTH:
                $half_pad_length = ($pad_length - $mb_strlen) / 2;
                $split_repeat    = (int) ceil($pad_length / $pad_string_mb_strlen);
                return mb_substr(str_repeat($pad_string, $split_repeat), 0, (int) floor($half_pad_length))
                        .$string.mb_substr(str_repeat($pad_string, $split_repeat), 0, (int) ceil($half_pad_length));

            default: // Exception on unexpected pad type.
                throw new \Exception(__('Unexpected `pad_type`.', 'comet-cache'));
        }
    }
}
