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

namespace Reflex\Di\ProviderTypes;

use Closure;
use ReflectionFunction;

/**
 * Lazy
 *
 * @package    Reflex
 * @subpackage Core
 */
class Lazy extends ProviderTypeBase
{
    /**
     * Instantiate Builder
     * 
     * @param Closure $abstract Closure provider
     * 
     * @return void
     */
    public function __construct(Closure $closure)
    {
        $this->mixed    =   $closure;
    }

    /**
     * Return resource provided by Closure
     * 
     * @param  array $parameters Parameters to invoke with
     * 
     * @return mixed
     */
    public function get(array $parameters = null)
    {
        // Using the Forge we allow the Closure to define required dependencies
        $reflection =   $this->getReflection($this->mixed);
        $parameters =   $this->container
            ->getForge()
            ->discoverArguments(
                (array) $parameters,
                $reflection
            );

        return call_user_func_array($this->mixed, $parameters);
    }

    /**
     * Get ReflectionFunction for a Closure
     * 
     * @param  Closure $closure Closure to reflect upon
     * 
     * @return \ReflectionFunction
     */
    protected function getReflection(Closure $closure)
    {
        return new ReflectionFunction($closure);
    }
}
