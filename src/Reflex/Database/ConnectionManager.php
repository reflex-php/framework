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

namespace Reflex\Database;

use PDO;
use InvalidArgumentException;
use Reflex\Core\Application;
use Reflex\Database\Connect\ConnectionFactory;
use Reflex\Database\Connect\ConnectionBase;

/**
 * ConnectionManager
 *
 * @package    Reflex
 * @subpackage Core
 */
class ConnectionManager
{
    /**
     * Connection factory
     * @var ConnectionFactory
     */
    private $factory;

    /**
     * Storage for connections
     * @var \Reflex\Database\Connect\ConnectionBase[]
     */
    private $connections    =   array();

    /**
     * Application instance
     * @var \Reflex\Core\Application
     */
    private $app;

    /**
     * Instantiate ConnectionManager
     * 
     * @param ConnectionFactory $factory Factory for connections
     *
     * @return void
     */
    public function __construct(Application $app, ConnectionFactory $factory)
    {
        $this->app      =   $app;
        $this->factory  =   $factory;
    }

    public function connect($name = null)
    {
        $name       =   $name ?: $this->getDefaultConnectionName();

        if (! isset($this->connections[ $name ])) {
            $config     =   $this->getConfig($name);
            $driver     =   array_get($config, 'driver');
            $connection =   $this->factory
                ->createConnection($driver)
                ->connect($config);

            $this->storeConnection($name, $connection);
        }

        return $this->getConnection($name);
    }

    public function reconnect($name = null)
    {
        array_set($this->connections, $name, null);

        return $this->connect($name);
    }

    protected function storeConnection($name, ConnectionBase $connection)
    {
        array_set($this->connections, $name, $connection);
    }

    protected function getConnection($name)
    {
        return array_get($this->connections, $name);
    }

    protected function getConfig($name)
    {
        $connections=   $this->app['config']['database.connections'];
        $config     =   array_get($connections, $name);

        if (is_null($config)) {
            throw new InvalidArgumentException(
                sprintf("Database [%s] not configured.", $name)
            );
        }

        return $config;
    }

    public function getDefaultConnectionName()
    {
        return $this->app['config']['database.default'];
    }

    public function setDefaultConnectionName($name)
    {
        $this->app['config']['database.default'] =   $name;

        return $this;
    }
}
