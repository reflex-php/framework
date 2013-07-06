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

use Reflex\Filesystem\Filesystem;

/**
 * FileLoader
 *
 * @package    Reflex
 * @subpackage Core
 */
class FileLoader implements LoaderInterface
{
    /**
     * Loader for Config
     * @var \Reflex\Filesystem\Filesystem
     */
    protected $loader;

    /**
     * Path to locate config files
     * @var string
     */
    protected $defaultPath;

    public function __construct(Filesystem $loader, $defaultPath)
    {
        $this->setLoader($loader);
        $this->setDefaultPath($defaultPath);
    }

    public function load()
    {
        
    }

    public function setLoader(Filesystem $loader)
    {
        $this->loader   =   $loader;

        return $this;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function setDefaultPath($defaultPath)
    {
        $this->defaultPath  =   $defaultPath;

        return $this;
    }

    public function getDefaultPath()
    {
        return $this->defaultPath;
    }
}
