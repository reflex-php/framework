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

/**
 * ArrayContainer
 *
 * @package    Reflex
 * @subpackage Core
 */
class ArrayContainer extends ProviderTypeBase
{
    /**
     * Instantiate ArrayContainer
     * 
     * @param string $array Array of data
     * 
     * @return void
     */
    public function __construct(array $array)
    {
        $this->mixed    =   $array;
    }

    /**
     * Get stored array
     * 
     * @param  array $parameters Parameters to invoke with
     * 
     * @return object
     */
    public function get(array $parameters = null)
    {
        return array_merge($this->mixed, (array) $parameters);
    }
}
