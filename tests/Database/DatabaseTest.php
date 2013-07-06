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

use Reflex\Database\ConnectionManager;
use Reflex\Database\Connect\ConnectionFactory;
use Reflex\Core\Application;
use Reflex\Filesystem\Filesystem;
use Reflex\Config\FileLoader;
use Reflex\Config\Storage;

/**
 * DatabaseTest
 *
 * @package    Reflex
 * @subpackage Core
 */
class DatabaseTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        $app                =   new Application;
        $filesystem         =   new Filesystem;
        $loader             =   new FileLoader($filesystem, '');
        $config             =   new Storage($loader);
        $config['database'] =   array(
            'connections'   =>  array(
                'mysql' =>  array(
                    'driver'    =>  'mysql',
                    'host'      =>  'db.dev',
                    'database'  =>  'dev',
                    'username'  =>  'azirius',
                    'password'  =>  'FFdragon22'
                )
            ),
            'default'       =>  'mysql'
        );

        $app['config']  =   $config;
        $factory        =   new ConnectionFactory;
        $manager        =   new ConnectionManager($app, $factory);

        $connection =   $manager->connect();
    }
}
