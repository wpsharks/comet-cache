<?php
namespace WebSharks\JsMinifier;

/**
 * JS Minifier.
 *
 * @since 150424 Initial release.
 *
 * Original JSMin copyright: {@link http://www.crockford.com/javascript/jsmin.html Douglas Crockford}.
 * Original PHP port copyright: {@link http://github.com/rgrove/jsmin-php/ Ryan Grove}.
 * Enhancements copyright: {@link http://code.google.com/p/minify/ Andrea Giammarchi}.
 * Enhancements copyright: {@link http://code.google.com/p/minify/ Steve Clay}.
 * Modified by: {@link http://websharks-inc.com/ Jason @ WebSharks, Inc.}.
 */
class Core
{
    const ORD_LF            = 10;
    const ORD_SPACE         = 32;
    const ACTION_KEEP_A     = 1;
    const ACTION_DELETE_A   = 2;
    const ACTION_DELETE_A_B = 3;

    protected $a            = "\n";
    protected $b            = '';
    protected $input        = '';
    protected $inputIndex   = 0;
    protected $inputLength  = 0;
    protected $lookAhead    = null;
    protected $output       = '';
    protected $lastByteOut  = '';

    /**
     * Class constructor.
     *
     * @since 150424 Initial release.
     *
     * @param string $input Uncompressed JS.
     */
    public function __construct($input)
    {
        $this->input = (string) $input;
    }

    /**
     * Minify (Compress) Javascript.
     *
     * @since 150424 Initial release.
     *
     * @param string $js Javascript to be minified
     *
     * @return string Minified JavaScript; else original on failure.
     */
    public static function compress($js)
    {
        $js = (string) $js;
        try {
            $js_minifier = new static($js);
            $minified_js = $js_minifier->min();
            return $minified_js;
        } catch (\Exception $exception) {
            return $js;
        }
    }

    /**
     * Perform minification (compression).
     *
     * @since 150424 Initial release.
     *
     * @return string Output string; compressed JS.
     */
    public function min()
    {
        $this->output = ''; // Initialize.
        $mbIntEnc     = null; // Initialize.

        if (function_exists('mb_strlen') && ((integer) ini_get('mbstring.func_overload') & 2)) {
            $mbIntEnc = mb_internal_encoding();
            mb_internal_encoding('8bit');
        }
        $this->input       = str_replace("\r\n", "\n", $this->input);
        $this->inputLength = strlen($this->input);

        $this->action(self::ACTION_DELETE_A_B);

        while ($this->a !== null) {
            $command = self::ACTION_KEEP_A;
            if ($this->a === ' ') {
                if (($this->lastByteOut === '+' || $this->lastByteOut === '-') && ($this->b === $this->lastByteOut)) {
                } elseif (!$this->isAlphaNum($this->b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif ($this->a === "\n") {
                if ($this->b === ' ') {
                    $command = self::ACTION_DELETE_A_B;
                } elseif ($this->b === null || (strpos('{[(+-', $this->b) === false && !$this->isAlphaNum($this->b))) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif (!$this->isAlphaNum($this->a)) {
                if ($this->b === ' ' || ($this->b === "\n" && strpos('}])+-"\'', $this->a) === false)) {
                    $command = self::ACTION_DELETE_A_B;
                }
            }
            $this->action($command);
        }
        $this->output = trim($this->output);

        if ($mbIntEnc !== null) {
            mb_internal_encoding($mbIntEnc);
        }
        return $this->output;
    }

    /**
     * Action handler.
     *
     * @since 150424 Initial release.
     *
     * ACTION_KEEP_A = Output A. Copy B to A. Get the next B.
     * ACTION_DELETE_A = Copy B to A. Get the next B.
     * ACTION_DELETE_A_B = Get the next B.
     *
     * @param int $command Action identifier.
     *
     * @throws Exception On failure.
     */
    protected function action($command)
    {
        if ($command === self::ACTION_DELETE_A_B && $this->b === ' ' && ($this->a === '+' || $this->a === '-')) {
            if ($this->input[$this->inputIndex] === $this->a) {
                $command = self::ACTION_KEEP_A;
            }
        }
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->output .= $this->a;
                $this->lastByteOut = $this->a;
            // Fallthrough to next case.
            case self::ACTION_DELETE_A:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') {
                    $str = $this->a;
                    while (true) {
                        $this->output .= $this->a;
                        $this->lastByteOut = $this->a;

                        $this->a       = $this->get();
                        if ($this->a === $this->b) {
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            throw new \Exception('Unterminated String at byte: '.$this->inputIndex.': '.$str);
                        }
                        $str .= $this->a;
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->lastByteOut = $this->a;

                            $this->a       = $this->get();
                            $str .= $this->a;
                        }
                    }
                }
            // Fallthrough to next case.
            case self::ACTION_DELETE_A_B:
                $this->b = $this->next();
                if ($this->b === '/' && $this->isRegexpLiteral()) {
                    $this->output .= $this->a.$this->b;
                    $pattern = '/';
                    while (true) {
                        $this->a = $this->get();
                        $pattern .= $this->a;
                        if ($this->a === '/') {
                            break;
                        } elseif ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $pattern      .= $this->a;
                        } elseif (ord($this->a) <= self::ORD_LF) {
                            throw new \Exception('Unterminated RegExp at byte: '.$this->inputIndex.': '.$pattern);
                        }
                        $this->output .= $this->a;
                        $this->lastByteOut = $this->a;
                    }
                    $this->b = $this->next();
                }
        }
    }

    /**
     * Utility conditional check.
     *
     * @since 150424 Initial release.
     *
     * @return bool `TRUE` if is regex literal.
     */
    protected function isRegexpLiteral()
    {
        if (strpos("\n{;(,=:[!&|?", $this->a) !== false) {
            return true;
        }
        if ($this->a === ' ') {
            $length = strlen($this->output);
            if ($length < 2) {
                return true;
            }
            if (preg_match('/(?:case|else|in|return|typeof)$/', $this->output, $m)) {
                if ($this->output === $m[0]) {
                    return true;
                }
                $charBeforeKeyword = substr($this->output, $length - strlen($m[0]) - 1, 1);
                if (!$this->isAlphaNum($charBeforeKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get next character.
     *
     * @since 150424 Initial release.
     *
     * @return string|null Next char.
     */
    protected function get()
    {
        $c               = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = $this->input[$this->inputIndex];
                $this->inputIndex += 1;
            } else {
                return;
            }
        }
        if ($c === "\r" || $c === "\n") {
            return "\n";
        }
        if (ord($c) < self::ORD_SPACE) {
            return ' ';
        }
        return $c;
    }

    /**
     * Get next character.
     *
     * @since 150424 Initial release.
     *
     * @return string Next char.
     */
    protected function peek()
    {
        $this->lookAhead = $this->get();

        return $this->lookAhead;
    }

    /**
     * Letter, digit, underscore, dollar sign, escape, or non-ASCII?
     *
     * @since 150424 Initial release.
     *
     * @param string $c Input character to test.
     *
     * @return bool `TRUE` if letter, digit, underscore, dollar sign, escape, or non-ASCII?
     */
    protected function isAlphaNum($c)
    {
        return preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $c) || ord($c) > 126;
    }

    /**
     * Single-line comment handler.
     *
     * @since 150424 Initial release.
     *
     * @return string Single-line comment.
     */
    protected function singleLineComment()
    {
        $comment = '';
        while (true) {
            $get = $this->get();
            $comment .= $get;
            if (ord($get) <= self::ORD_LF) {
                if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                    return '/'.$comment;
                }
                return $get;
            }
        }
        return;
    }

    /**
     * Multi-line comment handler.
     *
     * @since 150424 Initial release.
     *
     * @throws Exception On failre.
     *
     * @return string Multi-line comment.
     */
    protected function multipleLineComment()
    {
        $this->get();
        $comment = '';
        while (true) {
            $get = $this->get();
            if ($get === '*') {
                if ($this->peek() === '/') {
                    $this->get();
                    if (strpos($comment, '!') === 0) {
                        return "\n/*!".substr($comment, 1)."*/\n";
                    }
                    if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                        return '/*'.$comment.'*/';
                    }
                    return ' ';
                }
            } elseif ($get === null) {
                throw new \Exception('Unterminated comment at byte: '.$this->inputIndex.': /*'.$comment);
            }
            $comment .= $get;
        }
        return;
    }

    /**
     * Get the next character, skipping over comments.
     *      Some comments may be preserved.
     *
     * @since 150424 Initial release.
     *
     * @return string Next character (or comment).
     */
    protected function next()
    {
        $get = $this->get();
        if ($get !== '/') {
            return $get;
        }
        switch ($this->peek()) {
            case '/':
                return $this->singleLineComment();

            case '*':
                return $this->multipleLineComment();

            default:
                return $get;
        }
    }
}
