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

/**
 * Builder
 *
 * @package    Reflex
 * @subpackage Core
 */
class Builder extends ProviderTypeBase
{
    /**
     * Instantiate Builder
     * 
     * @param string $abstract Classname
     * 
     * @return void
     */
    public function __construct($abstract)
    {
        $this->mixed    =   $abstract;
    }

    /**
     * Get instantiated object
     * 
     * @param  array $parameters Parameters to invoke with
     * 
     * @return object
     */
    public function get(array $parameters = null)
    {
        return $this->container->create($this->mixed, $parameters);
    }
}
