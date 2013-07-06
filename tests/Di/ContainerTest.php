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

use Reflex\Di\Container;

require __DIR__ . '/Stubs/ContainerStub.php';
require __DIR__ . '/Stubs/InterfaceStub.php';
require __DIR__ . '/Stubs/InterfaceImplementationStub.php';
require __DIR__ . '/Stubs/DependentStub.php';
require __DIR__ . '/Stubs/DeeperDependentStub.php';

/**
 * ContainerTest
 *
 * @package    Reflex
 * @subpackage Core
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container    =   new Container;
    }

    public function testClosureResolution()
    {
        $container      =   $this->container;
        $array          =   array(
            'name'  =>  'Mike',
            'email' =>  'me@aziri.us',
            'age'   =>  '24'
        );
        $container['me']=   function () use ($array) {
            return $array;
        };
        $actual         =   $container->create('me');

        $this->assertEquals($array, $actual);
    }

    public function testSharedResolution()
    {
        $container  =   $this->container;
        $class      =   new stdClass;

        $container->singleton(
            'class',
            function () use ($class) {
                return $class;
            }
        );
        $actual     =   $container->create('class');

        $this->assertEquals($class, $actual);
    }

    public function testAutoResolution()
    {
        $container  =   $this->container;
        $expected   =   'ContainerStub';
        $actual     =   $container->create('ContainerStub');

        $this->assertInstanceOf($expected, $actual);
    }

    public function testResolutionOfAbstracts()
    {
        $container      =   $this->container;
        $interface      =   'InterfaceStub';
        $implementation =   'InterfaceImplementationStub';
        $container->store($interface, $implementation);
        $actual         =   $container->create('InterfaceStub');

        $this->assertInstanceOf($implementation, $actual);
    }

    public function testResolutionOfRecursiveDependencies()
    {
        $container      =   $this->container;
        
        $container->store('InterfaceStub', 'InterfaceImplementationStub');

        $class          =   $container->create('DependentStub');

        $this->assertInstanceOf('DependentStub', $class);
        $this->assertInstanceOf('DeeperDependentStub', $class->implementation);
        $this->assertInstanceOf('InterfaceStub', $class->implementation->implementation);
    }

    public function testClosuresReceiveContainer()
    {
        $container          =   $this->container;
        $container['bound'] =   function (Container $c) {
            return $c;
        };
        $expected           =   'Reflex\Di\Container';
        $actual             =   $container['bound'];

        $this->assertInstanceOf($expected, $actual);
    }
}
