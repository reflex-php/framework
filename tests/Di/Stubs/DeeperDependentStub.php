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

/**
 * DeeperDependentStub
 *
 * @package    Reflex
 * @subpackage Core
 */
class DeeperDependentStub
{
    public $implementation;

    public function __construct(InterfaceStub $implementation)
    {
        $this->implementation   =   $implementation;
    }
}
