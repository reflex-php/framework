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

namespace Reflex\Config;

use ArrayAccess;

/**
 * Storage
 *
 * @package    Reflex
 * @subpackage Core
 */
class Storage implements ArrayAccess
{
    protected $storage  =   array();

    protected $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader   =   $loader;
    }

    public function load($key)
    {
        
    }

    public function get($key, $default = null)
    {
        return array_get($this->storage, $key, $default);
    }

    public function set($key, $value)
    {
        array_set($this->storage, $key, $value);

        return $this;
    }

    public function remove($key)
    {
        $this->set($key, null);

        return $this;
    }

    public function exists($key)
    {
        $microstart =   microtime(true);

        return $microstart !== array_get($this->storage, $key, $microstart);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function getStorage()
    {
        return $this->storage;
    }
}
