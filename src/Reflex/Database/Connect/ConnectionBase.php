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

namespace Reflex\Database\Connect;

use PDO;

/**
 * ConnectionBase
 *
 * @package    Reflex
 * @subpackage Core
 */
abstract class ConnectionBase
{
    /**
     * Array of PDO options
     * @var array
     */
    protected $options  =   array(
        PDO::ATTR_CASE              =>  PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           =>  PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      =>  PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES =>  false,
        PDO::ATTR_EMULATE_PREPARES  =>  false,
        PDO::MYSQL_ATTR_INIT_COMMAND=>  'SET NAMES utf8'
    );

    /**
     * PDO instance
     * @var \PDO
     */
    protected $connection;

    /**
     * Get DSN
     * 
     * @param  array  $config Config
     * 
     * @return string
     */
    abstract protected function getDsn(array $config);

    /**
     * Connect to the database
     * 
     * @param  array  $config Config
     * 
     * @return \Reflex\Database\Connect\ConnectionBase Current ConnectionBase instance
     */
    public function connect(array $config)
    {
        $dsn                =   $this->getDsn($config);
        $options            =   array_get($config, 'options', array());
        $options            =   array_merge($this->options, $options);
        $username           =   array_get($config, 'username');
        $password           =   array_get($config, 'password');
        $this->connection   =   new PDO($dsn, $username, $password, $options);

        return $this;
    }

    /**
     * Get the current PDO instance
     * 
     * @return \PDO The current PDO instance
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
