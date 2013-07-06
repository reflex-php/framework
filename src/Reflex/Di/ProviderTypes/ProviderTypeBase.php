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

use Reflex\Di\Container;

/**
 * ProviderTypeBase
 *
 * @package    Reflex
 * @subpackage Core
 */
abstract class ProviderTypeBase
{
    /**
     * The mixed type to store
     * 
     * @var mixed
     */
    protected $mixed;

    /**
     * Container instance
     * 
     * @var \Reflex\Di\Container
     */
    protected $container;

    /**
     * Is the provided type shared?
     * 
     * @var boolean
     */
    protected $shared;

    /**
     * Get pre-instantiated object
     *
     * @param array $parameters Parameters for provided service
     * 
     * @return object
     */
    abstract public function get(array $parameters = null);

    /**
     * Set if the provided resource is shared
     * 
     * @param boolean $shared Shared or not
     *
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    public function setShared($shared)
    {
        $this->shared   =   $shared;

        return $this;
    }

    /**
     * Get shared status
     * 
     * @return boolean
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * Get the mixed data
     * 
     * @return mixed
     */
    public function getMixed()
    {
        return $this->mixed;
    }

    /**
     * Set the mixed data
     * 
     * @param mixed $mixed Mixed data
     * 
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    public function setMixed($mixed)
    {
        $this->mixed    =   $mixed;

        return $this;
    }

    /**
     * Store Container instance
     * 
     * @param Container $container Container instance to store
     *
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    public function setContainer(Container $container)
    {
        $this->container    =   $container;

        return $this;
    }

    /**
     * Get Container instance
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function getContainer()
    {
        return $this->container;
    }
}
