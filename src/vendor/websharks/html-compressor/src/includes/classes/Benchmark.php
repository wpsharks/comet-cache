<?php
namespace WebSharks\HtmlCompressor;

/**
 * HTML Compressor (benchmark).
 *
 * @since 150315 Enhancing debugger.
 *
 * @property-read $times Read-only access to times.
 * @property-read $data Read-only access to data.
 */
class Benchmark // For debugging.
{
    /*
     * Protected Properties
     */

    /**
     * @type array An array of times.
     *
     * @since 150315 Enhancing debugger.
     */
    protected $times = array();

    /**
     * @type array An array of data.
     *
     * @since 150315 Enhancing debugger.
     */
    protected $data = array();

    /*
     * Public API Methods
     */

    /**
     * Class Constructor.
     *
     * @since 150315 Enhancing debugger.
     *
     * @api Constructor is available for public use.
     */
    public function __construct()
    {
    }

    /**
     * Magic method for access to read-only properties.
     *
     * @since 150315 Enhancing debugger.
     *
     * @param string $property Propery by name.
     *
     * @throws \exception If `$property` does not exist for any reason.
     *
     * @return mixed Property value.
     *
     * @internal For internal magic use only.
     */
    public function __get($property)
    {
        $property = (string) $property;

        if (property_exists($this, $property)) {
            return $this->{$property};
        }
        throw new \Exception(sprintf('Undefined property: `%1$s`.', $property));
    }

    /**
     * Logs a new time entry.
     *
     * @since 150315 Enhancing debugger.
     *
     * @api This method is available for public use.
     *
     * @param string $function   Caller.
     * @param float  $start_time Start time; via `microtime(TRUE)`.
     * @param string $task       Description of the task(s) performed.
     */
    public function addTime($function, $start_time, $task)
    {
        if (!($function = trim((string) $function))) {
            return; // Not possible.
        }
        if (($start_time = (float) $start_time) <= 0) {
            return; // Not possible.
        }
        if (($end_time = (float) microtime(true)) <= 0) {
            return; // Not possible.
        }
        if (!($task = trim((string) $task))) {
            return; // Not possible.
        }
        $time                   = number_format($end_time - $start_time, 5, '.', '');
        $this->times[$function] = compact('function', 'time', 'task');
    }

    /**
     * Logs a new set of data.
     *
     * @since 150315 Enhancing debugger.
     *
     * @api This method is available for public use.
     *
     * @param string $function Caller.
     * @param array  $data     Associative array.
     */
    public function addData($function, array $data)
    {
        if (!($function = trim((string) $function))) {
            return; // Not possible.
        }
        if (!$data) {
            return; // Not possible.
        }
        $this->data[$function] = compact('function', 'data');
    }
}
