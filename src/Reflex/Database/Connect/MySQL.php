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

/**
 * MySQL
 *
 * @package    Reflex
 * @subpackage Core
 */
class MySQL extends ConnectionBase
{
    /**
     * Get DSN
     * 
     * @param  array  $config Config
     * 
     * @return string
     */
    protected function getDsn(array $config)
    {
        $host       =   array_get($config, 'host');
        $database   =   array_get($config, 'database');
        $port       =   array_get($config, 'port');
        $unixSocket =   array_get($config, 'unix_socket');

        $dsn        =   sprintf(
            "mysql:host=%s;dbname=%s",
            $host,
            $database
        );

        if (! is_null($port)) {
            $dsn    .=  ";port:" . $port;
        }

        if (! is_null($unixSocket)) {
            $dsn    .=  ";unix_socket:" . $unixSocket;
        }

        return $dsn;
    }
}
