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

namespace Reflex\Autoloader;

use Reflex\Autoloader\Exceptions\ResourceNotDeclaredException;
use SplFileObject;
use Exception;

/**
 * Reflex class loader
 *
 * @package    Reflex
 * @subpackage Core
 */
class Loader
{
    private $fallbackMode   =   true;
    private $extension      =   '.php';
    private $namespaces     =   array();
    private $paths          =   array();
    private $loaded         =   array();
    private $map            =   array();

    public function register($prepend)
    {
        spl_autoload_register(array($this, 'load'), true, (bool) $prepend);
    }

    public function unregister()
    {
        spl_autoload_register(array($this, 'load'));
    }

    public function load($abstract)
    {
        if ($this->isDeclared($abstract)) {
            return;
        }

        $file   =   $this->find($abstract);

        if (false === $file) {
            return;
        }

        require $file;

        if (false === $this->isDeclared($abstract)) {
            throw new ResourceNotDeclaredException($abstract);
        }

        array_push($this->loaded, $abstract);
    }

    protected function find($abstract)
    {
        if (isset($this->map[ $abstract ])) {
            return $this->map[ $abstract ];
        }

        $namespaces =   $this->namespaces;
        $huntResult =   $this->hunt($abstract);

        if (0 < count($namespaces)) {
            foreach ($namespaces as $namespacePrefix => $paths) {
                if (false === starts_with($abstract, $namespacePrefix)) {
                    continue 1;
                }

                foreach ($paths as $path) {
                    if (is_readable($file = $path . DIRECTORY_SEPARATOR . $huntResult)) {
                        return $file;
                    }
                }
            }
        }

        try {
            $splFileObject  =   new SplFileObject($huntResult, 'r', true);
        } catch (Exception $e) {
            return false;
        }

        return $splFileObject->getRealPath();
    }

    protected function hunt($abstract)
    {
        $namespace  =   '';
        $classname  =   $abstract;

        if (false !== ($position = strpos($abstract, '\\'))) {
            $namespace  =   substr($abstract, 0, $position);
            $namespace  =   str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            $classname  =   substr($abstract, $position + 1);
        }

        return $namespace .
            str_replace('_', DIRECTORY_SEPARATOR, $classname) .
            $this->extension;
    }

    public function addNamespaces($prefix, $paths)
    {
        foreach ((array) $paths as $path) {
            $path   =   rtrim($path, DIRECTORY_SEPARATOR);
            if (! in_array($prefix, $this->paths)) {
                $this->paths[ $prefix ][]   =   $path;
            }
        }

        return $this;
    }

    public function setMap($classname, $path)
    {
        $this->map[ $classname ]    =   $path;

        return $this;
    }

    public function setMaps(array $map)
    {
        $this->map  =   $map;

        return $this;
    }

    public function addMaps(array $map)
    {
        $this->map  =   array_merge($this->map, $map);

        return $this;
    }

    public function setExtension($extension)
    {
        $this->extension    =   $extension;

        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setFallbackMode($fallbackMode)
    {
        $this->fallbackMode =   $fallbackMode;

        return $this;
    }

    public function getFallbackMode()
    {
        return $this->fallbackMode;
    }

    protected function isDeclared($abstract)
    {
        return is_declared($abstract);
    }
}
