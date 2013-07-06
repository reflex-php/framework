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

namespace Reflex\Di;

use ArrayObject;
use Reflex\Di\ProviderTypes\Lazy;
use Reflex\Di\ProviderTypes\Eager;
use Reflex\Di\ProviderTypes\Builder;
use Reflex\Di\ProviderTypes\ArrayContainer;
use Reflex\Di\ProviderTypes\StringContainer;
use Reflex\Di\Exceptions\ProviderNotFoundException;

/**
 * ProviderFactory
 *
 * @package    Reflex
 * @subpackage Core
 */
class ProviderFactory
{
    /**
     * Make the provider
     * 
     * @param  mixed $value Value
     * 
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     *
     * @throws ProviderNotFoundException If Provider cannot be found for type
     */
    public function makeProviderType($value)
    {
        // Time to find our provider!
        switch (true) {
            /**
             * Closures are considered lazy, in the sense that they
             * provider services when required
             */
            case is_closure($value):
                return new Lazy($value);
                break;
            /**
             * Arrays are stored for later retrieval
             */
            case is_array($value):
            case $value instanceof ArrayObject:
                return new ArrayContainer($value);
                break;
            /**
             * Already instantiated objects are considered eager, in that
             * they are eagerly loaded before the container loads them
             */
            case is_object($value):
                return new Eager($value);
                break;
            /**
             * Strings are handled in two ways
             * If the string is a declared class/interface/trait we
             * will store it as something needing to be built in the future
             * If the string isn't a declared thing then we just contain the string
             * for future use
             */
            case is_string($value):
                if (is_declared($value, $auto = true)) {
                    return new Builder($value);
                }
                return new StringContainer($value);
                break;
        }

        throw new ProviderNotFoundException(
            sprintf("The type given [%s] doesn't have a provider.", gettype($value))
        );
    }
}
