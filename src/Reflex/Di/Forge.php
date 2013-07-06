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

namespace Reflex\Di;

use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use Reflex\Di\Exceptions\UninstantiableResourceException;

/**
 * Forge
 *
 * @package    Reflex
 * @subpackage Core
 */
class Forge
{
    /**
     * Container instance
     * 
     * @var \Reflex\Di\Container
     */
    private $container;

    /**
     * Reflection cache
     * 
     * @var ReflectionClass[]
     */
    private $reflection =   array();

    /**
     * Forge a new object instance
     * 
     * @param  string $abstract   Thing to forge
     * @param  array $parameters Array of parameters
     * 
     * @return object
     * 
     * @throws UninstantiableResourceException If Resource isn't instantiable
     */
    public function forger($abstract, array $parameters = null)
    {
        $reflection =   $this->makeReflection($abstract);

        if (false === $reflection->isInstantiable()) {
            throw new UninstantiableResourceException($abstract);
        }

        $arguments  =   $this->recursivelyFetchArguments($abstract);
        $constructor=   $reflection->getConstructor();

        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        $arguments  =   $this->discoverArguments($arguments, $constructor);

        if (0 === count($arguments)) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Recursively get arguments from parents
     * 
     * @param  string $abstract  Thing to get arguments for
     * @param  array  $arguments Arguments to recursively merge with
     * 
     * @return array
     */
    protected function recursivelyFetchArguments($abstract, array $arguments = array())
    {
        $parameters     =   $this->container->raw($abstract);

        if (is_null($parameters)) {
            $tempArguments  =   array();
        }
        
        $parentClass    =   get_parent_class($abstract);
        if (false === $parentClass) {
            return $arguments;
        }

        return $this->recursivelyFetchArguments(
            $parentClass,
            array_merge(
                $arguments,
                $tempArguments
            )
        );
    }

    /**
     * Discover arguments for a invokable
     * 
     * @param  array                      $arguments Arguments to merge with
     * @param  ReflectionFunctionAbstract $function  Function to build for
     * 
     * @return array
     */
    public function discoverArguments(array $arguments, ReflectionFunctionAbstract $function)
    {
        $parameters =   $function->getParameters();
        $return     =   array();

        if (0 === count($parameters)) {
            return array();
        }

        foreach ($parameters as $parameter) {
            $return[]   =   $this->resolveParameter($parameter, $arguments);
        }

        return $return;
    }

    /**
     * Resolve a parameter
     * 
     * @param  ReflectionParameter $parameter Parameter to resolve
     * @param  array               $arguments Arguments to fallback to
     * 
     * @return mixed
     */
    protected function resolveParameter(ReflectionParameter $parameter, array $arguments)
    {
        $class  =   $parameter->getClass();

        if (! is_null($class)) {
            return $this->container->create($class->getName(), $arguments);
        }

        $name   =   $parameter->getName();

        if (isset($arguments[ $name ])) {
            return array_pull($arguments, $name);
        }
        
        if (0 < count($arguments)) {
            return array_shift($arguments);
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        return null;
    }

    /**
     * Make a ReflectionClass instance
     * 
     * @param  string $abstract Thing to Reflect upon
     * 
     * @return \ReflectionClass
     */
    protected function makeReflection($abstract)
    {
        if (! isset($this->reflection[ $abstract ])) {
            $this->reflection[ $abstract ]  =   new ReflectionClass($abstract);
        }

        return $this->reflection[ $abstract ];
    }

    /**
     * Set Container instance
     * 
     * @param \Reflex\Di\Container $container Container instance
     *
     * @return \Reflex\Di\Forge
     */
    public function setContainer(Container $container)
    {
        $this->container    =   $container;

        return $this;
    }
}
