<?php
/**
 * Reflex Framework
 *
 * @package   Reflex
 * @version   1.0.0
 * @author    Reflex Community
 * @license   MIT License
 * @copyright 2013 Reflex Community
 * @link      http://reflex.aziri.us/
 */

if (! function_exists('literal_class')) {
    /**
     * Get real class name via Reflection
     * 
     * @param  mixed $classname Class name
     * 
     * @return string
     */
    function literal_class($classname)
    {
        static $classes =   array();

        $classname  =   is_object($classname)
            ? get_class($classname)
            : $classname;

        if (isset($classes[ $classname ])) {
            return $classes[ $classname ];
        }

        $reflection =   new ReflectionClass($classname);

        return $classes[ $classname ]   =   $reflection->getName();
    }
}

if (! function_exists('is_closure')) {
    /**
     * Check if item is a Closure
     *
     * @param mixed $mixed
     *
     * @return boolean
     */
    function is_closure($mixed)
    {
        return $mixed instanceof Closure;
    }
}

if (! function_exists('is_declared')) {
    /**
     * Checks to see if a class, interface or trait is declared
     * 
     * @param  string  $string The name to check for
     * 
     * @return boolean
     *
     * @throws InvalidArgumentException If Parameter isn't a string
     */
    function is_declared($string)
    {
        if (! is_string($string)) {
            throw new InvalidArgumentException(
                "%s expects first argument to be string, %s was given",
                __FUNCTION__,
                gettype($string)
            );
        }

        $return =   class_exists($string, false)
            || interface_exists($string, false);

        if (version_compare(phpversion(), '>=', '5.4.0')) {
            $return =   $return || trait_exists($string, false);
        }

        return $return;
    }
}
