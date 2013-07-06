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

namespace Reflex\Core;

use Reflex\Di\Container;

/**
 * Application
 *
 * @package    Reflex
 * @subpackage Core
 */
class Application extends Container
{
    /**
     * Version of Reflex in use
     *
     * @var string
     */
    const VERSION   =   '1.0.0';

    /**
     * Paths for the application
     * 
     * @var array
     */
    protected $paths=   array();

    /**
     * Set the application paths
     * 
     * @param array $paths Paths to store
     * 
     * @return \Reflex\Core\Application Current Application instance
     */
    public function setApplicationPaths(array $paths)
    {
        array_set($this->paths, 'path', realpath($paths['app']));

        foreach (array_except($paths, 'app') as $key => $path) {
            array_set($this->paths, 'path.' . $key, realpath($value));
        }

        return $this;
    }

    /**
     * Get the stored application paths or a specific one
     * 
     * @param  string $key Path to get
     * 
     * @return mixed
     */
    public function getApplicationPaths($key = null)
    {
        return is_null($key)
            ? array_get($this->paths, $key)
            : $this->paths;
    }
}
