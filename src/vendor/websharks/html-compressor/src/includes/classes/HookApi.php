<?php
namespace WebSharks\HtmlCompressor;

/**
 * HTML Compressor (hook API).
 *
 * @since 150321 Adding hook API for plugins.
 *
 * @property-read $times Read-only access to times.
 * @property-read $data Read-only access to data.
 */
class HookApi // For plugins.
{
    /*
     * Protected Properties
     */

    /**
     * @type array An array of hooks.
     *
     * @since 150321 Adding hook API for plugins.
     */
    protected $hooks = array();

    /*
     * Public API Methods
     */

    /**
     * Class Constructor.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @api Constructor is available for public use.
     */
    public function __construct()
    {
        if (empty($GLOBALS[__NAMESPACE__.'_early_hooks'])) {
            return; // Nothing more to do here.
        }
        $GLOBALS[__NAMESPACE__.'_early_hooks'] = (array) $GLOBALS[__NAMESPACE__.'_early_hooks'];
        $early_hooks                           = &$GLOBALS[__NAMESPACE__.'_early_hooks'];

        foreach ($early_hooks as $_early_hook) {
            if (empty($_early_hook['hook'])) {
                continue; // Empty; bypass.
            }
            if (empty($_early_hook['function'])) {
                continue; // Empty; bypass.
            }
            if (!isset($_early_hook['priority'])) {
                $_early_hook['priority'] = 10;
            }
            if (!isset($_early_hook['accepted_args'])) {
                $_early_hook['accepted_args'] = 1;
            }
            $this->addHook(
                $_early_hook['hook'],
                $_early_hook['function'],
                $_early_hook['priority'],
                $_early_hook['accepted_args']
            );
        }
        unset($_early_hook); // Just a little housekeeping.

        $early_hooks = array(); // Empty; i.e., reset early hooks.
    }

    /**
     * Magic method for access to read-only properties.
     *
     * @since 150321 Adding hook API for plugins.
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
     * Assigns an ID to each callable attached to a hook/filter.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @param string|callable|mixed $function A string or a callable.
     *
     * @throws \exception If the hook/function is invalid (i.e. it's not possible to generate an ID).
     *
     * @return string Hook ID for the given `$function`.
     */
    public function hookId($function)
    {
        if (is_string($function)) {
            return $function;
        }
        if (is_object($function)) {
            $function = array($function, '');
        } else {
            $function = (array) $function;
        }
        if (isset($function[0], $function[1])) {
            if (is_object($function[0])) {
                return spl_object_hash($function[0]).$function[1];
            } elseif (is_string($function[0])) {
                return $function[0].'::'.$function[1];
            }
        }
        throw new \Exception('Invalid hook.');
    }

    /**
     * Adds a new hook (works with both actions & filters).
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @param string                $hook          The name of a hook to attach to.
     * @param string|callable|mixed $function      A string or a callable.
     * @param int                   $priority      Hook priority; defaults to `10`.
     * @param int                   $accepted_args Max number of args that should be passed to the `$function`.
     *
     * @return bool This always returns a `TRUE` value.
     */
    public function addHook($hook, $function, $priority = 10, $accepted_args = 1)
    {
        $hook          = (string) $hook;
        $priority      = (integer) $priority;
        $accepted_args = max(0, (integer) $accepted_args);
        $hook_id       = $this->hookId($function);

        $this->hooks[$hook][$priority][$hook_id] = array(
            'function'      => $function,
            'accepted_args' => (integer) $accepted_args,
        );
        return true; // Always returns true.
    }

    /**
     * Adds a new action hook.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @return bool This always returns a `TRUE` value.
     *
     * @see addHook()
     */
    public function addAction() // Simple `add_hook()` alias.
    {
        return call_user_func_array(array($this, 'addHook'), func_get_args());
    }

    /**
     * Adds a new filter.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @return bool This always returns a `TRUE` value.
     *
     * @see addHook()
     */
    public function addFilter() // Simple `add_hook()` alias.
    {
        return call_user_func_array(array($this, 'addHook'), func_get_args());
    }

    /**
     * Removes a hook (works with both actions & filters).
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @param string                $hook     The name of a hook to remove.
     * @param string|callable|mixed $function A string or a callable.
     * @param int                   $priority Hook priority; defaults to `10`.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     */
    public function removeHook($hook, $function, $priority = 10)
    {
        $hook     = (string) $hook;
        $priority = (integer) $priority;
        $hook_id  = $this->hookId($function);

        if (!isset($this->hooks[$hook][$priority][$hook_id])) {
            return false; // Nothing to remove.
        }
        unset($this->hooks[$hook][$priority][$hook_id]);
        if (!$this->hooks[$hook][$priority]) {
            unset($this->hooks[$hook][$priority]);
        }
        return true; // Existed before it was removed.
    }

    /**
     * Removes an action.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     *
     * @see removeHook()
     */
    public function removeAction() // Simple `remove_hook()` alias.
    {
        return call_user_func_array(array($this, 'removeHook'), func_get_args());
    }

    /**
     * Removes a filter.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     *
     * @see removeHook()
     */
    public function removeFilter() // Simple `remove_hook()` alias.
    {
        return call_user_func_array(array($this, 'removeHook'), func_get_args());
    }

    /**
     * Runs any callables attached to an action.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @param string $hook The name of an action hook.
     */
    public function doAction($hook)
    {
        $hook = (string) $hook;
        if (empty($this->hooks[$hook])) {
            return; // No hooks.
        }
        $hook_actions = $this->hooks[$hook];
        $args         = func_get_args();
        ksort($hook_actions);

        foreach ($hook_actions as $_hook_action) {
            foreach ($_hook_action as $_action) {
                if (!isset($_action['function'], $_action['accepted_args'])) {
                    continue; // Not a valid filter in this case.
                }
                call_user_func_array($_action['function'], array_slice($args, 1, $_action['accepted_args']));
            }
        }
        unset($_hook_action, $_action); // Housekeeping.
    }

    /**
     * Runs any callables attached to a filter.
     *
     * @since 150321 Adding hook API for plugins.
     *
     * @param string $hook  The name of a filter hook.
     * @param mixed  $value The value to filter.
     *
     * @return mixed The filtered `$value`.
     */
    public function applyFilters($hook, $value)
    {
        $hook = (string) $hook;
        if (empty($this->hooks[$hook])) {
            return $value; // No hooks.
        }
        $hook_filters = $this->hooks[$hook];
        $args         = func_get_args();
        ksort($hook_filters);

        foreach ($hook_filters as $_hook_filter) {
            foreach ($_hook_filter as $_filter) {
                if (!isset($_filter['function'], $_filter['accepted_args'])) {
                    continue; // Not a valid filter in this case.
                }
                $args[1] = $value; // Continously update the argument `$value`.
                $value   = call_user_func_array($_filter['function'], array_slice($args, 1, $_filter['accepted_args']));
            }
        }
        unset($_hook_filter, $_filter); // Housekeeping.

        return $value; // With applied filters.
    }
}
