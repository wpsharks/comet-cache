<?php
namespace WebSharks\ZenCache;

/*
 * Array of hooks.
 *
 * @since 150422 Rewrite.
 *
 * @type array An array of hooks.
 */
$self->hooks = array();

/*
 * Assigns an ID to each callable attached to a hook/filter.
 *
 * @since 150422 Rewrite.
 *
 * @param string|callable|mixed $function A string or a callable.
 *
 * @return string Hook ID for the given `$function`.
 *
 * @throws \Exception If the hook/function is invalid (i.e. it's not possible to generate an ID).
 */
$self->hookId = function ($function) use ($self) {
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
    throw new \Exception(__('Invalid hook.', 'zencache'));
};

/*
 * Adds a new hook (works with both actions & filters).
 *
 * @since 150422 Rewrite.
 *
 * @param string                $hook The name of a hook to attach to.
 * @param string|callable|mixed $function A string or a callable.
 * @param integer               $priority Hook priority; defaults to `10`.
 * @param integer               $accepted_args Max number of args that should be passed to the `$function`.
 *
 * @return boolean This always returns a `TRUE` value.
 */
$self->addHook = function ($hook, $function, $priority = 10, $accepted_args = 1) use ($self) {
    $hook = (string) $hook;
    if (stripos($hook, 'quick_cache') === 0) {
        $hook  = GLOBAL_NS.substr($hook, strlen('quick_cache'));
    }
    $priority      = (integer) $priority;
    $accepted_args = max(0, (integer) $accepted_args);
    $hook_id       = $self->hookId($function);

    $self->hooks[$hook][$priority][$hook_id] = array(
            'function'      => $function,
            'accepted_args' => $accepted_args,
    );
    return true; // Always returns true.
};

/*
 * Adds a new action hook.
 *
 * @since 150422 Rewrite.
 *
 * @return boolean This always returns a `TRUE` value.
 */
$self->addAction = function () use ($self) {
    return call_user_func_array(array($self, 'addHook'), func_get_args());
};
$self->add_action = $self->addAction; // Back compat.

/*
 * Adds a new filter.
 *
 * @since 150422 Rewrite.
 *
 * @return boolean This always returns a `TRUE` value.
 */
$self->addFilter = function () use ($self) {
    return call_user_func_array(array($self, 'addHook'), func_get_args());
};
$self->add_filter = $self->addFilter; // Back compat.

/*
 * Removes a hook (works with both actions & filters).
 *
 * @since 150422 Rewrite.
 *
 * @param string                $hook The name of a hook to remove.
 * @param string|callable|mixed $function A string or a callable.
 * @param integer               $priority Hook priority; defaults to `10`.
 *
 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
 */
$self->removeHook = function ($hook, $function, $priority = 10) use ($self) {
    $hook = (string) $hook;
    if (stripos($hook, 'quick_cache') === 0) {
        $hook  = GLOBAL_NS.substr($hook, strlen('quick_cache'));
    }
    $priority = (integer) $priority;
    $hook_id  = $self->hookId($function);

    if (!isset($self->hooks[$hook][$priority][$hook_id])) {
        return false; // Nothing to remove.
    }
    unset($self->hooks[$hook][$priority][$hook_id]);

    if (!$self->hooks[$hook][$priority]) {
        unset($self->hooks[$hook][$priority]);
    }
    return true; // Existed before it was removed.
};

/*
 * Removes an action.
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
 */
$self->removeAction = function () use ($self) {
    return call_user_func_array(array($self, 'removeHook'), func_get_args());
};

/*
 * Removes a filter.
 *
 * @since 150422 Rewrite.
 *
 * @return boolean `TRUE` if removed; else `FALSE` if not removed for any reason.
 */
$self->removeFilter = function () use ($self) {
    return call_user_func_array(array($self, 'removeHook'), func_get_args());
};

/*
 * Runs any callables attached to an action.
 *
 * @since 150422 Rewrite.
 *
 * @param string $hook The name of an action hook.
 */
$self->doAction = function ($hook) use ($self) {
    $hook = (string) $hook;
    if (empty($self->hooks[$hook])) {
        return; // No hooks.
    }
    $hook_actions = $self->hooks[$hook];
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
};

/*
 * Runs any callables attached to a filter.
 *
 * @since 150422 Rewrite.
 *
 * @param string $hook The name of a filter hook.
 * @param mixed  $value The value to filter.
 *
 * @return mixed The filtered `$value`.
 */
$self->applyFilters = function ($hook, $value) use ($self) {
    $hook = (string) $hook;
    if (empty($self->hooks[$hook])) {
        return $value; // No hooks.
    }
    $hook_filters = $self->hooks[$hook];
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
};

/*
 * Does an action w/ back compat. for Quick Cache.
 *
 * @since 150422 Rewrite.
 *
 * @param string $hook The hook to apply.
 */
$self->doWpAction = function ($hook) use ($self) {
    $hook = (string) $hook;
    $args = func_get_args();
    call_user_func_array('do_action', $args);

    if (stripos($hook, GLOBAL_NS) === 0) {
        $quick_cache_filter  = 'quick_cache'.substr($hook, strlen(GLOBAL_NS));
        $quick_cache_args    = $args; // Use a copy of the args.
        $quick_cache_args[0] = $quick_cache_filter;
        call_user_func_array('do_action', $quick_cache_args);
    }
};

/*
 * Applies filters w/ back compat. for Quick Cache.
 *
 * @since 150422 Rewrite.
 *
 * @param string $hook The hook to apply.
 *
 * @return mixed The filtered value.
 */
$self->applyWpFilters = function ($hook) use ($self) {
    $hook  = (string) $hook;
    $args  = func_get_args();
    $value = call_user_func_array('apply_filters', $args);

    if (stripos($hook, GLOBAL_NS) === 0) {
        $quick_cache_hook    = 'quick_cache'.substr($hook, strlen(GLOBAL_NS));
        $quick_cache_args    = $args; // Use a copy of the args.
        $quick_cache_args[0] = $quick_cache_hook;
        $quick_cache_args[1] = $value; // Filtered value.
        $value               = call_user_func_array('apply_filters', $quick_cache_args);
    }
    return $value; // Filtered value.
};
