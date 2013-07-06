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
 * StringContainer
 *
 * @package    Reflex
 * @subpackage Core
 */
class StringContainer extends ProviderTypeBase
{
    /**
     * Instantiate StringContainer
     * 
     * @param string $string String to store
     * 
     * @return void
     */
    public function __construct($string)
    {
        $this->mixed    =   $string;
    }

    /**
     * Get stored string
     * 
     * @param  array $parameters Ignored
     * 
     * @return object
     */
    public function get(array $parameters = null)
    {
        return $this->mixed;
    }
}
