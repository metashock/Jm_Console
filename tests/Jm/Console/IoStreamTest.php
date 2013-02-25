<?php
/**
 * @package Jm_Console
 */
/**
 * @package Jm_Console
 */
class Jm_Console_IoStreamTest extends PHPUnit_Framework_TestCase
{

    /**
     * Requires the autoloader
     */
    public function setUp() {
        require_once 'Jm/Autoloader.php';
    }

    /**
     *  @expectedException InvalidArgumentException
     */
    public function testConstructorException() {
        // should throw an InvalidArgumentException as the 
        // argument isn't a resource handle
        $stream = new Jm_Console_IoStream(FALSE);
    }


    /**
     * Tests the getter fd()
     */
    public function testGetFd() {
        $stream = new Jm_Console_IoStream(STDIN);
        $this->assertEquals(STDIN, $stream->fd());
        $stream = new Jm_Console_IoStream(STDOUT);
        $this->assertEquals(STDOUT, $stream->fd());
        $stream = new Jm_Console_IoStream(STDERR);
        $this->assertEquals(STDERR, $stream->fd());
    }


    /**
     * Tests InvalidArgumentException of enforceIsatty()
     *
     * @expectedException InvalidArgumentException
     */
    public function testEnforceIsattyException() {
        $stream = new Jm_Console_IoStream(STDOUT);
        // should throw an InvalidArgumentException as the 
        // argument isn't a boolean
        $stream->enforceIsatty('test');       
    }
}
