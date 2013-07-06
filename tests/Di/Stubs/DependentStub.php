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
 * DependentStub
 *
 * @package    Reflex
 * @subpackage Core
 */
class DependentStub
{
    public $implementation;

    public function __construct(DeeperDependentStub $implementation)
    {
        $this->implementation   =   $implementation;
    }
}
