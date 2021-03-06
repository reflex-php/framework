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

use ArrayAccess;
use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionException;
use Reflex\Di\Exceptions\UndefinedResourceException;
use Reflex\Di\ProviderTypes\Lazy;

/**
 * Container
 *
 * @package    Reflex
 * @subpackage Core
 */
class Container implements ArrayAccess
{
    /**
     * Forge instance that constructs object instances
     * 
     * @var \Reflex\Di\Forge
     */
    protected $forge;

    /**
     * Abstract factory to create builder types
     * 
     * @var \Reflex\Di\ProviderFactory
     */
    protected $factory;

    /**
     * Stores all instances
     * 
     * @var array
     */
    protected $instances  =   array();

    /**
     * Stores all providers
     * 
     * @var \Reflex\Di\ProviderTypes\ProviderTypeBase[]
     */
    protected $providers    =   array();

    /**
     * Callbacks
     * 
     * @var Closure[]
     */
    protected $callbacks    =   array();

    /**
     * Instantiate a new instance
     *
     * @param \Reflex\Di\Forge           $forge   Forge instance
     * @param \Reflex\Di\ProviderFactory $factory ProviderFactory
     *
     * @return void
     */
    public function __constructor(
        Forge $forge = null,
        ProviderFactory $factory = null
    ) {
        $this->setForge($forge);
        $this->setProviderFactory($factory);
    }

    /**
     * Clone
     * 
     * @return void
     */
    public function __clone()
    {
        // We discard any current providers and instances
        $this->providers=   array();
        $this->instances=   array();

        // Keep our forge and factory instances as they don't have any
        // unwanted state
        $this->forge    =   clone $this->forge;
        $this->factory  =   clone $this->factory;
    }

    /**
     * Create a new instance
     * 
     * @param  string  $abstract  Name of thing to create
     * @param  array   $arguments Arguments to invoke with
     * 
     * @return object
     *
     * @throws UndefinedResourceException If Resource does't exist
     */
    public function create($abstract, $arguments = null)
    {
        if (! is_string($abstract)) {
            throw new InvalidArgumentException(
                sprintf(
                    "First parameter of %s must be string, %s given",
                    __METHOD__,
                    gettype($abstract)
                )
            );
        }

        if (isset($this->instances[ $abstract ])) {
            return $this->instances[ $abstract ];
        }

        // Get our provider
        $provider   =   $this->raw($abstract);

        if (is_null($provider)) {
            try {
                return $this->getForge()->forger($abstract, $arguments);
            } catch (ReflectionException $e) {
                throw new UndefinedResourceException($abstract);
            }
        }
        
        $instance   =   $provider->get($arguments);
        if (true === $provider->getShared()) {
            $this->assign($instance, $abstract);
        }

        $this->fireCallbacks($instance);

        return $instance;
    }

    /**
     * Store a new provider
     * 
     * @param  string  $abstract Abstract to store
     * @param  mixed   $concrete Concrete to store
     * @param  boolean $shared   Shared instance or not
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function store($abstract, $concrete = null, $shared = false)
    {
        $concrete OR $concrete = $abstract;

        $provider   =   $this->makeProviderType($concrete);
        $provider->setShared($shared);

        $this->providers[ $abstract ]   =   $provider;

        return $this;
    }

    /**
     * Store if it doesn't already exist
     * 
     * @param  string  $abstract Key to store under
     * @param  mixed   $concrete Value to store
     * @param  boolean $shared   Shared instance or not
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function storeIf($abstract, $concrete, $shared = false)
    {
        if (false === $this->has($abstract)) {
            return $this->store($abstract, $concrete, $shared);
        }

        return $this;
    }

    /**
     * Singleton instance
     * 
     * @param  string $abstract Thing to create
     * @param  array  $concrete Arguments to invoke with
     * 
     * @return object
     */
    public function singleton($abstract, $concrete = null)
    {
        return $this->store($abstract, $concrete, true);
    }

    /**
     * Have we stored something under this key?
     * 
     * @param  string $abstract abstract to look-up
     * 
     * @return boolean
     */
    public function has($abstract)
    {
        return isset($this[ $abstract ]) || isset($this->instances[ $abstract ]);
    }

    /**
     * Protect a closure so it's not mistaken for a provider
     * 
     * @param  string  $key      Key to store under
     * @param  Closure $callable Closure to protect
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function protect($key, Closure $callable)
    {
        return $this->store(
            $key,
            function ($c) use ($callable) {
                return $callable;
            }
        );
    }

    /**
     * Get the stored provider
     * 
     * @param  string $key Key stored under
     * 
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    public function raw($key)
    {
        return isset($this->providers[ $key ])
            ? $this->providers[ $key ]
            : null;
    }

    /**
     * Lazilly require files when depended upon
     * 
     * @param  string $path File to require
     * @param  string $key  Key to store under
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function lazyRequire($path, $key = null)
    {
        $key OR $key = $this->lastPathSegmentForKey($path);

        return $this->store(
            $key,
            function () use ($path) {
                return require $path;
            }
        );
    }

    /**
     * Lazilly include files when depended upon
     * 
     * @param  string $path File to include
     * @param  string $key  Key to store under
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function lazyInclude($path, $key = null)
    {
        $key OR $key = $this->lastPathSegmentForKey($path);

        return $this->store(
            $key,
            function () use ($path) {
                return include $path;
            }
        );
    }

    /**
     * Get the last segment from the path
     * 
     * @param  string $path Path to retrieve last segment
     * 
     * @return string
     */
    protected function lastPathSegmentForKey($path)
    {
        $pathinfo   =   pathinfo($path, PATHINFO_DIRNAME);
        return strtolower(basename($pathinfo));
    }

    /**
     * Shared closure that is unique for this container
     * 
     * @param  string  $key      Key to store under
     * @param  Closure $callable Closure to share
     * 
     * @return \Reflex\Di\Container Current Container instance
     */
    public function shared($key, Closure $callable)
    {
        return $this->store(
            $key,
            function ($c) use ($callable) {
                static $object;

                if (is_null($object)) {
                    $object =   $callable($c);
                }

                return $object;
            }
        );
    }

    /**
     * Extends a Lazy provider
     * 
     * @param  string  $key      Key to store under
     * @param  Closure $callable Closure to extend with
     * 
     * @return \Reflex\Di\Container Current Container instance
     *
     * @throws InvalidArgumentException If Provider isn't found
     * @throws InvalidArgumentException If Provider isn't Lazy
     */
    public function extend($key, Closure $callable)
    {
        if (! array_key_exists($key, $this->providers)) {
            throw new InvalidArgumentException(
                sprintf(
                    "The identifier [%s] isn't defined.",
                    $key
                )
            );
        }

        $provider   =   $this->raw($key);

        if (! ($provider instanceof Lazy)) {
            throw new InvalidArgumentException(
                sprintf(
                    "The identifier [%s] isn't a Lazy definition.",
                    $key
                )
            );
        }

        $mixed   =   $provider->getMixed();

        return $this->store(
            $key,
            function ($c) use ($callable, $mixed) {
                return $callable(
                    $mixed($c),
                    $c
                );
            }
        );
    }
        
    /**
     * Create a new provider
     * 
     * @param  mixed $value Mixed value
     * 
     * @return \Reflex\Di\ProviderTypes\ProviderTypeBase
     */
    protected function makeProviderType($value)
    {
        $factory    =   $this->getProviderFactory();
        $provider   =   $factory->makeProviderType($value);
        $provider->setContainer($this);    

        return $provider;
    }

    /**
     * Assign a new instance
     * 
     * @param  object $object Object to assign
     * @param  string $key    Key to store under
     * 
     * @return \Reflex\Di\Container
     */
    public function assign($object, $key = null)
    {
        $key OR $key = strtolower(literal_class($object));

        $this->instances[ $key ]    =   $object;

        return $this;
    }

    /**
     * Set Forge instance
     * 
     * @param \Reflex\Di\Forge $forge Forge instance
     *
     * @return \Reflex\Di\Container
     */
    public function setForge(Forge $forge = null)
    {
        $this->forge    =   $forge ?: new Forge;

        $this->forge->setContainer($this);

        return $this;
    }

    /**
     * Get Forge instance
     * 
     * @return \Reflex\Di\Forge
     */
    public function getForge()
    {
        $this->forge OR $this->setForge();

        return $this->forge;
    }

    /**
     * Set ProviderFactory instance
     * 
     * @param \Reflex\Di\ProviderFactory $factory ProviderFactory instance
     *
     * @return \Reflex\Di\Container
     */
    public function setProviderFactory(ProviderFactory $factory = null)
    {
        $this->factory  =   $factory ?: new ProviderFactory;

        return $this;
    }

    /**
     * Get ProviderFactory instance
     * 
     * @return \Reflex\Di\ProviderFactory
     */
    public function getProviderFactory()
    {
        $this->factory OR $this->setProviderFactory();

        return $this->factory;
    }

    /**
     * Get all providers
     * 
     * @return ProviderTypeBase[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Store a new callback
     * 
     * @param  Closure  $callback Callback
     * 
     * @return \Reflex\Di\Container
     */
    public function callback(Closure $callback)
    {
        $this->callbacks[]  =   $callback;

        return $this;
    }

    /**
     * Call stored callbacks
     * 
     * @param  object $object Object to use
     * 
     * @return void
     */
    protected function fireCallbacks($object)
    {
        foreach ($this->callbacks as $callback) {
            if ($this->doesClosureHintObject($object, $callback)) {
                call_user_func($callback, $object);
            }
        }
    }

    /**
     * Check type hinting for callback
     * 
     * @param  object  $object  Object to look for
     * @param  Closure $closure Closure to check
     * 
     * @return boolean
     */
    protected function doesClosureHintObject($object, Closure $closure)
    {
        $reflection =   new ReflectionFunction($closure);

        if (0 === $reflection->getNumberOfParameters()) {
            return false;
        }

        $parameters =   $reflection->getParameters();
        $first      =   array_shift($parameters);
        $class      =   $first->getClass();

        if (! is_null($class)) {
            return is_a($object, $class->getName());
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->providers[ $offset ]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->create($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->store($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->providers[ $offset ]);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($offset)
    {
        return $this[ $offset ];
    }

    /**
     * {@inheritdoc}
     */
    public function __set($offset, $value)
    {
        $this[ $offset ]    =   $value;
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($offset)
    {
        return isset($this[ $offset ]);
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($offset)
    {
        unset($this[ $offset ]);
    }
}
