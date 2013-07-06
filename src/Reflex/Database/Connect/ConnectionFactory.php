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

use InvalidArgumentException;

/**
 * ConnectionFactory
 *
 * @package    Reflex
 * @subpackage Core
 */
class ConnectionFactory
{
    /**
     * Create a new connection
     * 
     * @param  string $driver Driver to create
     * 
     * @return \Reflex\Database\Connect\ConnectionBase
     */
    public function createConnection($driver)
    {
        switch (strtolower($driver)) {
            case 'mysql':
                return new MySQL;
                break;
        }

        throw new InvalidArgumentException(
            sprintf("Driver [%s] not found.", $driver)
        );
    }
}
