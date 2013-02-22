<?php
/**
 *
 * @package Jm_Console
 */
/**
 * This suite currently contains the singleton clone test
 *
 * @package Jm_Console
 */
class Jm_Console_TextStyleFactoryText extends PHPUnit_Framework_TestCase
{

    /**
     *  @expectedException Exception
     */
    public function testClone() {
        require_once 'Jm/Autoloader.php';
        $factory = Jm_Console_TextStyleFactory::singleton();
        clone $factory;
    }
}
