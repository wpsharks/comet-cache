<?php
namespace WebSharks\CssMinifier;

/**
 * Core.
 *
 * @since 150510 Initial release.
 */
class Core
{
    /**
     * CSS.
     *
     * @since 150510 Initial release.
     *
     * @type string CSS.
     */
    protected $css;

    /**
     * Static cache.
     *
     * @since 150510 Initial release.
     *
     * @type array Static cache.
     */
    protected static $static = array();

    /**
     * Constructor.
     *
     * @since 150510 Initial release.
     */
    public function __construct($css)
    {
        $this->css = (string) $css;
    }

    /**
     * Compressor.
     *
     * @since 150510 Initial release.
     *
     * @param string $css Input CSS.
     */
    public static function compress($css)
    {
        $css = (string) $css;
        try {
            $css_minifier = new static($css);
            $minified_css = $css_minifier->min();
            return $minified_css;
        } catch (\Exception $exception) {
            return $css;
        }
    }

    /**
     * Minifier.
     *
     * @since 150510 Initial release.
     *
     * @return string Output (minified) CSS.
     */
    public function min()
    {
        return $this->selfMin();
    }

    /**
     * Self minifier.
     *
     * @since 15xxxx Adding SCSS compiler.
     *
     * @return string Output (minified) CSS.
     */
    protected function selfMin()
    {
        if (!$this->css) {
            return $this->css;
        }
        $static = &static::$static[__FUNCTION__.'_map'];

        if (!isset($static['replace'], $static['with'], $static['colors'])) {
            $de_spacifiables = array(
                '{',
                '}',
                '!=',
                '|=',
                '^=',
                '$=',
                '*=',
                '~=',
                '=',
                '~',
                ';',
                ',',
                '>',
            );
            $de_spacifiables = array_map(
                function ($string) {
                    return preg_quote($string, '/');
                },
                $de_spacifiables
            );
            $de_spacifiables = implode('|', $de_spacifiables);

            $static['replace'] = array(
                'comments'        => '/\/\*.*?\*\//s',
                'line_breaks'     => '/['."\r\n".']+/',
                'extra_spaces'    => '/\s{2,}/',
                'de_spacifiables' => '/ *('.$de_spacifiables.') */',
                'unnecessary_;s'  => '/;\}/',
            );
            $static['with']   = array('', ' ', ' ', '${1}', '}');
            $static['colors'] = '/(?P<context>[:,\h]+#)(?P<hex>[a-z0-9]{6})/i';
        }
        $this->css = preg_replace($static['replace'], $static['with'], $this->css);
        $this->css = preg_replace_callback($static['colors'], array($this, 'selfMaybeCompressCssColorCb'), $this->css);
        $this->css = trim($this->css);

        return $this->css;
    }

    /**
     * Compresses HEX color codes.
     *
     * @since 140417 Initial release.
     *
     * @param array $m Regular expression matches.
     *
     * @return string Full match with compressed HEX color code.
     */
    protected function selfMaybeCompressCssColorCb(array $m)
    {
        $m['hex'] = strtoupper($m['hex']); // Convert to uppercase for easy comparison.

        if ($m['hex'][0] === $m['hex'][1] && $m['hex'][2] === $m['hex'][3] && $m['hex'][4] === $m['hex'][5]) {
            return $m['context'].$m['hex'][0].$m['hex'][2].$m['hex'][4];
        }
        return $m[0];
    }
}
