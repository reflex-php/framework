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
 * Eager
 *
 * @package    Reflex
 * @subpackage Core
 */
class Eager extends ProviderTypeBase
{
    /**
     * Instantiate Builder
     * 
     * @param object $abstract Already instantiated object
     * 
     * @return void
     */
    public function __construct($object)
    {
        $this->mixed    =   $object;
    }

    /**
     * Get pre-instantiated object
     *
     * @param array $parameters Not used here
     * 
     * @return object
     */
    public function get(array $parameters = null)
    {
        return $this->mixed;
    }
}
