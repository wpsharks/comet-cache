<?php
namespace WebSharks\CometCache\Classes;

use WebSharks\CometCache\Traits;

/**
 * Notes.
 *
 * @since 161119 Notes.
 */
class Notes extends AbsBase
{
    use Traits\Shared\StringUtils;

    /**
     * @since 161119
     *
     * @var array
     */
    protected $notes;

    /**
     * Class constructor.
     *
     * @since 161119 Notes.
     */
    public function __construct()
    {
        parent::__construct();

        $this->notes = [];
    }

    /**
     * Get notes.
     *
     * @since 161119 Notes.
     *
     * @return array Notes.
     */
    public function notes()
    {
        return $this->notes;
    }

    /**
     * Add note.
     *
     * @param string $key  Key.
     * @param string $note Note.
     *
     * @since 161119 Notes.
     */
    public function add($key, $note)
    {
        $this->notes[$key] = $note;
    }

    /**
     * Add line break.
     *
     * @since 161119 Notes.
     */
    public function addLineBreak()
    {
        $this->notes[] = "\n";
    }

    /**
     * Add divider.
     *
     * @since 161119 Notes.
     */
    public function addDivider()
    {
        $this->notes[] = str_repeat('.', 70);
    }

    /**
     * Add ASCII artwork.
     *
     * @param string $note Note.
     *
     * @since 161119 Notes.
     */
    public function addAsciiArt($note)
    {
        $this->notes[] = '*´¨)
     ¸.•´¸.•*´¨) ¸.•*¨)
     (¸.•´ (¸.•` ¤ '.$note.' ¤ ´¨)';
    }

    /**
     * As HTML comments.
     *
     * @since 161119 HTML comments.
     */
    public function asHtmlComments()
    {
        $html_comments    = ''; // Initialize.
        $longest_key_size = 0; // Initialize.

        foreach ($this->notes as $_key => $_note) {
            if (is_string($_key) && $_key && isset($_note[0])) {
                $longest_key_size = max($longest_key_size, mb_strlen($_key.':'));
            }
        } // unset($_key, $_note); // Housekeeping.

        foreach ($this->notes as $_key => $_note) {
            if (is_integer($_key)) {
                if ($_note === "\n") {
                    $html_comments .= "\n";
                } elseif (isset($_note[0])) {
                    $html_comments .= '<!-- '.htmlspecialchars($_note).' -->'."\n";
                }
            } elseif ($_key && !isset($_note[0])) {
                $html_comments .= '<!-- '.htmlspecialchars($_key).' -->'."\n";
            } elseif (!$_key && isset($_note[0])) {
                $html_comments .= '<!-- '.htmlspecialchars($_note).' -->'."\n";
            } elseif ($_key && isset($_note[0])) {
                $html_comments .= '<!-- '.htmlspecialchars($this->strPad($_key.':', $longest_key_size).'    '.$_note).' -->'."\n";
            }
        } // unset($_key, $_note); // Housekeeping.

        return trim($html_comments);
    }
}
