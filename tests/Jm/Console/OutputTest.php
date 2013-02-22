<?php
/**
 * @package Jm_Console
 */
/**
 *
 * @package Jm_Console
 */
class Jm_Console_OutputTest extends PHPUnit_Framework_TestCase
{

    /**
     *  @var Jm_Console_Output
     */
    protected $terminal;

  
    /**
     *  Initializes the console instance
     */
    public function setUp() {
        require_once 'Jm/Autoloader.php';
        $this->terminal = new Jm_Console_Output();
    }


    /**
     * Opens a temporary file and returns the file descriptor. Registers
     * a shutdown function that makes sure that the file will be removed after tests
     *
     * @return resource 
     */
    protected function openTempFile($mode = 'w+', &$file = '') {
        $file = tempnam(sys_get_temp_dir(), 'phpunit');

        // open a tmpfile read only
        $fd = fopen($file, $mode);
        if(!is_resource($fd)) {
            throw new Exception(sprintf(
                'Cannot create temporary file %s', $file
            ));
        }       

        // make sure the file will be removed
        register_shutdown_function(function() use($file) {
        //    unlink($file);
        });

        return $fd;
    }


    /**
     *
     */
    public function testSetDefaultTextStyle() {

        $this->assertEquals(
            $this->terminal->getDefaultTextStyle(),
            new Jm_Console_TextStyle()
        );

        $this->terminal->setDefaultTextStyle('red');
        $style = $this->terminal->getDefaultTextStyle();
        $this->assertEquals(
            $style->getForegroundColor(),
            Jm_Console_TextStyle::RED
        );

        try {
            $this->terminal->setDefaultTextStyle(new DateTime());
            $this->assertFalse(TRUE,
                'An exception should have been thrown at this point');
        } catch(Exception $e) {
            // passed 
        }

        // reset the text style
        $this->terminal->setDefaultTextStyle(
            new Jm_Console_TextStyle()
        ); 
    }


    /**
     * @expectedException Exception
     */
    public function testColorizeException() {
        $this->console->stdout()->colorize('test', new DateTime());
    }


    /**
     *  @expectedException Jm_Console_OutputException
     */
    public function testWriteException() {
        $fd = $this->openTempFile('r');
        $output = new Jm_Console_Output($fd);

        // close the file
        fclose($fd);
        // the next line should throw an Exception because the file was closed
        $output->write('test');
    }   


    /**
     * Tests all terminal control methods that takes no arguments
     * and printing ANSI commands to the terminal in a batch.
     */
    public function testClearAndFriends() {

        $methods = array(
            'clear'     => "\033[;f\033[2J",
            'eraseln'   => "\033[1K",
            'savecursor' => "\033[s",
            'restorecursor' => "\033[u",
            'cursorback' => "\033[1D"
        );

        foreach($methods as $method => $code) {

            $fd = $this->openTempFile('w+', $file);
            $output = new Jm_Console_Output($fd);
            $output->{$method}();

            // the output should be empty as $fd isn't a terminal
            $written = fgets($fd);

            $this->assertEquals('', $written);

            // repeat the test with enforceIsatty. Output should be 
            // the desired escape sequence

            $output->enforceIsatty(TRUE);
            $output->{$method}();

            // rewind file seek
            rewind($fd);

            // the output should be empty as $fd isn't a terminal
            $written = fread($fd, filesize($file));

            // close $fd early
            fclose($fd);            

            $this->assertEquals($code, $written);

        }
    }

}

