<?php
namespace WebSharks\ZenCache;

/**
 * Abstract Base for Advanced Cache and Plugin.
 *
 * @since 150422 Rewrite.
 */
abstract class AbsBaseAp extends AbsBase
{
    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        parent::__construct();

        $closures_dir = dirname(dirname(__FILE__)).'/closures/Shared';
        $self         = $this; // Reference for closures.

        foreach (scandir($closures_dir) as $_closure) {
            if (substr($_closure, -4) === '.php') {
                require $closures_dir.'/'.$_closure;
            }
        }
        unset($_closure); // Housekeeping.
    }

    /**
     * Magic/overload property setter.
     *
     * @param string $property Property to set.
     * @param mixed  $value    The value for this property.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __set($property, $value)
    {
        $property          = (string) $property;
        $this->{$property} = $value;
    }

    /**
     * Closure overloading.
     *
     * @since 150422 Rewrite.
     */
    public function __call($closure, $args)
    {
        $closure = (string) $closure;

        if (isset($this->{$closure}) && is_callable($this->{$closure})) {
            return call_user_func_array($this->{$closure}, $args);
        }
        throw new \Exception(sprintf(__('Undefined method/closure: `%1$s`.', 'zencache'), $closure));
    }
}
