<?php
namespace WebSharks\CometCache\Classes;

use WebSharks\CometCache\Interfaces;

/**
 * Abstract Base.
 *
 * @since 150422 Rewrite.
 */
abstract class AbsBase implements Interfaces\Shared\NcDebugConsts, Interfaces\Shared\CachePathConsts
{
    /**
     * @var null|plugin Plugin reference.
     *
     * @since 150422 Rewrite.
     */
    protected $plugin;

    /**
     * @var array Instance cache.
     *
     * @since 150422 Rewrite.
     */
    protected $cache = [];

    /**
     * @var array Global static cache ref.
     *
     * @since 150422 Rewrite.
     */
    protected $static = [];

    /**
     * @var array Global static cache.
     *
     * @since 150422 Rewrite.
     */
    protected static $global_static = [];

    /**
     * @var \stdClass Overload properties.
     *
     * @since 150422 Rewrite.
     */
    protected $overload;

    /**
     * Class constructor.
     *
     * @since 150422 Rewrite.
     */
    public function __construct()
    {
        $this->plugin = &$GLOBALS[GLOBAL_NS];

        $class = get_called_class();

        if (empty(static::$global_static[$class])) {
            static::$global_static[$class] = [];
        }
        $this->static = &static::$global_static[$class];

        $this->overload = new \stdClass();
    }

    /**
     * Instance (singleton).
     *
     * @since 151002 Directory stats.
     *
     * @return AbsBase Instance.
     */
    public static function instance()
    {
        static $instance; // Per class.

        if (isset($instance)) {
            return $instance;
        }

        return $instance = new static();
    }

    /**
     * Magic/overload `isset()` checker.
     *
     * @param string $property Property to check.
     *
     * @return bool TRUE if `isset($this->overload->{$property})`.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __isset($property)
    {
        $property = (string) $property; // Force string.

        return isset($this->overload->{$property});
    }

    /**
     * Magic/overload property getter.
     *
     * @param string $property Property to get.
     *
     * @throws \Exception If the `$overload` property is undefined.
     *
     * @return mixed The value of `$this->overload->{$property}`.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __get($property)
    {
        $property = (string) $property; // Force string.

        if (property_exists($this->overload, $property)) {
            return $this->overload->{$property};
        }
        throw new \Exception(sprintf(__('Undefined overload property: `%1$s`.', 'comet-cache'), $property));
    }

    /**
     * Magic/overload property setter.
     *
     * @param string $property Property to set.
     * @param mixed  $value    The value for this property.
     *
     * @throws \Exception We do NOT allow magic/overload properties to be set.
     *                    Magic/overload properties in this class are read-only.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __set($property, $value)
    {
        $property = (string) $property; // Force string.

        throw new \Exception(sprintf(__('Refused to set overload property: `%1$s`.', 'comet-cache'), $property));
    }

    /**
     * Magic `unset()` handler.
     *
     * @param string $property Property to unset.
     *
     * @throws \Exception We do NOT allow magic/overload properties to be unset.
     *                    Magic/overload properties in this class are read-only.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __unset($property)
    {
        $property = (string) $property; // Force string.

        throw new \Exception(sprintf(__('Refused to unset overload property: `%1$s`.', 'comet-cache'), $property));
    }

    /*
    * Cache key generation helpers.
    */

    /**
     * Construct & acquires a cache key.
     *
     * @param string      $function `__FUNCTION__` is suggested here.
     * @param mixed|array $args     The cachable arguments to the calling function.
     * @param string      $___prop  For internal use only. This defaults to `cache`.
     *
     * @return mixed|null Current value, else `NULL` if the key is not set yet.
     *
     * @note This function returns by reference. The use of `&` is highly recommended when calling this utility.
     *    See also: <http://php.net/manual/en/language.references.return.php>
     */
    public function &cacheKey($function, $args = [], $___prop = 'cache')
    {
        $function = (string) $function;
        $args     = (array) $args;

        if (!isset($this->{$___prop}[$function])) {
            $this->{$___prop}[$function] = null;
        }
        $cache_key = &$this->{$___prop}[$function];

        foreach ($args as $_arg) {
            // Use each arg as a key.

            switch (gettype($_arg)) {
                case 'integer':
                    $_key = (int) $_arg;
                    break; // Break switch handler.

                case 'double':
                case 'float':
                    $_key = (string) $_arg;
                    break; // Break switch handler.

                case 'boolean':
                    $_key = (int) $_arg;
                    break; // Break switch handler.

                case 'array':
                case 'object':
                    $_key = sha1(serialize($_arg));
                    break; // Break switch handler.

                case 'NULL':
                case 'resource':
                case 'unknown type':
                default:
                    $_key = "\0".(string) $_arg;
            }
            if (!isset($cache_key[$_key])) {
                $cache_key[$_key] = null;
            }
            $cache_key = &$cache_key[$_key];
        }

        return $cache_key;
    }

    /**
     * Construct & acquires a static key.
     *
     * @param string      $function See {@link cacheKey()}.
     * @param mixed|array $args     See {@link cacheKey()}.
     *
     * @return mixed|null See {@link cacheKey()}.
     *
     * @note This function returns by reference. The use of `&` is highly recommended when calling this utility.
     *    See also: <http://php.net/manual/en/language.references.return.php>
     */
    public function &staticKey($function, $args = [])
    {
        $key = &$this->cacheKey($function, $args, 'static');

        return $key; // By reference.
    }
}
