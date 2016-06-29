<?php
namespace WebSharks\CometCache\Traits\Shared;

use WebSharks\CometCache\Classes;

trait HookUtils
{
    /**
     * Array of hooks.
     *
     * @since 150422 Rewrite.
     *
     * @type array An array of hooks.
     */
    public $hooks = [];

    /**
     * Assigns an ID to each callable attached to a hook/filter.
     *
     * @since 150422 Rewrite.
     *
     * @param string|callable|mixed $function A string or a callable.
     *
     * @throws \Exception If the hook/function is invalid (i.e. it's not possible to generate an ID).
     *
     * @return string Hook ID for the given `$function`.
     */
    public function hookId($function)
    {
        if (is_string($function)) {
            return $function;
        }
        if (is_object($function)) {
            $function = [$function, ''];
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
        throw new \Exception(__('Invalid hook.', 'comet-cache'));
    }

    /**
     * Adds a new hook (works with both actions & filters).
     *
     * @since 150422 Rewrite.
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
        $hook = (string) $hook;
        if (mb_stripos($hook, 'zencache') === 0) {
            $hook = GLOBAL_NS.mb_substr($hook, mb_strlen('zencache'));
        }
        $priority      = (integer) $priority;
        $accepted_args = max(0, (integer) $accepted_args);
        $hook_id       = $this->hookId($function);

        $this->hooks[$hook][$priority][$hook_id] = [
            'function'      => $function,
            'accepted_args' => $accepted_args,
        ];
        return true; // Always returns true.
    }

    /**
     * Adds a new action hook.
     *
     * @since 150422 Rewrite.
     *
     * @return bool This always returns a `TRUE` value.
     */
    public function addAction()
    {
        return call_user_func_array([$this, 'addHook'], func_get_args());
    }

    // @codingStandardsIgnoreStart
    /*
    * Back compat. alias for addAction()
    */
    public function add_action()
    { // @codingStandardsIgnoreEnd
        return call_user_func_array([$this, 'addAction'], func_get_args());
    }

    /**
     * Adds a new filter.
     *
     * @since 150422 Rewrite.
     *
     * @return bool This always returns a `TRUE` value.
     */
    public function addFilter()
    {
        return call_user_func_array([$this, 'addHook'], func_get_args());
    }

    // @codingStandardsIgnoreStart
    /*
    * Back compat. alias for addFilter()
    */
    public function add_filter()
    { // @codingStandardsIgnoreEnd
        return call_user_func_array([$this, 'addFilter'], func_get_args());
    }

    /**
     * Removes a hook (works with both actions & filters).
     *
     * @since 150422 Rewrite.
     *
     * @param string                $hook     The name of a hook to remove.
     * @param string|callable|mixed $function A string or a callable.
     * @param int                   $priority Hook priority; defaults to `10`.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     */
    public function removeHook($hook, $function, $priority = 10)
    {
        $hook = (string) $hook;
        if (mb_stripos($hook, 'zencache') === 0) {
            $hook = GLOBAL_NS.mb_substr($hook, mb_strlen('zencache'));
        }
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
     * @since 150422 Rewrite.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     */
    public function removeAction()
    {
        return call_user_func_array([$this, 'removeHook'], func_get_args());
    }

    /**
     * Removes a filter.
     *
     * @since 150422 Rewrite.
     *
     * @return bool `TRUE` if removed; else `FALSE` if not removed for any reason.
     */
    public function removeFilter()
    {
        return call_user_func_array([$this, 'removeHook'], func_get_args());
    }

    /**
     * Runs any callables attached to an action.
     *
     * @since 150422 Rewrite.
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
     * @since 150422 Rewrite.
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

    /**
     * Does an action w/ back compat. for ZenCache.
     *
     * @since 150422 Rewrite.
     *
     * @param string $hook The hook to apply.
     */
    public function doWpAction($hook)
    {
        $hook = (string) $hook;
        $args = func_get_args();
        call_user_func_array('do_action', $args);

        if (mb_stripos($hook, GLOBAL_NS) === 0) {
            $zencache_filter  = 'zencache'.mb_substr($hook, mb_strlen(GLOBAL_NS));
            $zencache_args    = $args; // Use a copy of the args.
            $zencache_args[0] = $zencache_filter;
            call_user_func_array('do_action', $zencache_args);
        }
    }

    /**
     * Applies filters w/ back compat. for ZenCache.
     *
     * @since 150422 Rewrite.
     *
     * @param string $hook The hook to apply.
     *
     * @return mixed The filtered value.
     */
    public function applyWpFilters($hook)
    {
        $hook  = (string) $hook;
        $args  = func_get_args();
        $value = call_user_func_array('apply_filters', $args);

        if (mb_stripos($hook, GLOBAL_NS) === 0) {
            $zencache_hook    = 'zencache'.mb_substr($hook, mb_strlen(GLOBAL_NS));
            $zencache_args    = $args; // Use a copy of the args.
            $zencache_args[0] = $zencache_hook;
            $zencache_args[1] = $value; // Filtered value.
            $value            = call_user_func_array('apply_filters', $zencache_args);
        }
        return $value; // Filtered value.
    }
}
