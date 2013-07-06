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

use Reflex\Di\ProviderTypes\Lazy;
use Reflex\Di\ProviderTypes\Eager;
use Reflex\Di\ProviderTypes\Builder;
use Reflex\Di\ProviderTypes\ArrayContainer;
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
     * Container instance
     * @var \Reflex\Di\Container
     */
    private $container;

    /**
     * Set Container instance
     * 
     * @param \Reflex\Di\Container $container Container instance
     * 
     * @return \Reflex\Di\ProviderFactory
     */
    public function setContainer(Container $container)
    {
        $this->container    =   $container;

        return $this;
    }

    /**
     * Make the provider
     * 
     * @param  mixed $value Value
     * 
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    public function makeProviderType($value)
    {
        switch (true) {
            case is_closure($value):
                return new Lazy($value);
                break;
            case is_object($value):
                return new Eager($value);
                break;
            case is_string($value):
                if (is_declared($value)) {
                    return new Builder($value);
                }
                return new StringContainer($value);
                break;
            case is_array($value):
                return new ArrayContainer($value);
                break;
        }

        throw new ProviderNotFoundException(
            sprintf("The type given [%s] doesn't have a provider.", gettype($value))
        );
    }
}
