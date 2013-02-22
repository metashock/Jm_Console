<?php
/**
 *
 *  @package Jm_Console
 */
/**
 *
 *  @package Jm_Console
 */
class Jm_ConsoleTest extends PHPUnit_Framework_TestCase
{


    /**
     * @var array
     */
    protected $textStyles;


    /**
     * requires the classes to be tested
     */
    public function setUp() {
        require_once 'Jm/Autoloader.php';
        $this->console = Jm_Console::singleton();
        $this->console->stdin()->enforceIsatty(TRUE);
        $this->console->stdout()->enforceIsatty(TRUE);
        $this->console->stderr()->enforceIsatty(TRUE);
    }

    /**
     *  Initializes a table with all possible text style 
     *  combinations. It should be created just once
     *
     *  @return void
     */
    protected function initTextStyles() {
        static $fcolors = array (
            Jm_Console_TextStyle::BLACK,
            Jm_Console_TextStyle::RED,
            Jm_Console_TextStyle::GREEN,
            Jm_Console_TextStyle::YELLOW,
            Jm_Console_TextStyle::BLUE,
            Jm_Console_TextStyle::PURPLE,
            Jm_Console_TextStyle::CYAN,
            Jm_Console_TextStyle::WHITE,
            Jm_Console_TextStyle::DEFAULT_COLOR
        );

        static $bcolors;
        $bcolors =  $fcolors;

        static $decorations = array (
            Jm_Console_TextStyle::BOLD,
            Jm_Console_TextStyle::LIGHT,
            Jm_Console_TextStyle::ITALIC,
            Jm_Console_TextStyle::UNDERLINE,
            Jm_Console_TextStyle::BLINK,
            Jm_Console_TextStyle::REVERSE,
            Jm_Console_TextStyle::HIDDEN,
            Jm_Console_TextStyle::NO_DECORATIONS
        );

        if(!empty($this->styles)) {
            return;
        }

        $this->styles = array();
        foreach ($fcolors as $fcolor) {
            foreach ($bcolors as $bcolor) {
                foreach ($decorations as $decoration) {
                    $this->styles []= new Jm_Console_TextStyle (
                        $fcolor, $bcolor, $decoration
                    );
                }
            }
        }

        // textual representations
        $this->styles[]= "fg:red,bg:blue,td:bold";
    }


    /**
     * Returns an array with all possible style combinations
     *
     * @return array
     */
    protected function getTextStyles() {
        $this->initTextStyles();
        return $this->styles;
    }


    /**
     * @dataProvider textStyleExceptionProvider
     *
     * @expectedException Jm_Console_TextStyleException
     */
    public function testTextStyleException($style) {
        $style = Jm_Console_TextStyle::fromString($style);
    }


    /**
     * Provides testTextStyleException with invalid data
     */
    public function textStyleExceptionProvider() {
        return array (
            array('pink'),
            array('bg:pink'),
            array('td:big'),
            array('color:blue'),
            array('fg:zero')
        );
    }


    /**
     * Tests if Jm_Console::write() will properly
     * write to PHP's output buffer
     */
    public function testWrite() {
        $message = uniqid();

        $this->console->ansiDisable();
        ob_start();
        $this->console->write($message);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($content, $message);

        $this->console->ansiEnable();
        ob_start();
        $this->console->write($message);
        $content2 = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($content, $message);

        // both outputs should be same regardless of enableColors()
        // was called or not. this tests if colorize() disables
        // esacpaing when the default style is used
        $this->assertEquals($content, $content2);

        foreach($this->getTextStyles() as $style) {
            if(is_string($style)) {
                $message = $style;
            } else {
                $message = $style->__toString();
            }
            ob_start();
            $this->console->write($message, $style);
            $content = ob_get_contents();
            ob_end_clean();

            $message = $this->console->stdout()->colorize($message, $style);
            $this->assertEquals($content, $message,
                'Failed to assert that the write produces the right output'
            );
        }
    }


    /**
     * @expectedException Jm_Console_Exception
     */
    public function testWriteFailed() {
        // open a readonly file descriptor
        $fd = fopen(__FILE__, 'r');
        $output = new Jm_Console_Output($fd);
        // the following line should throw an exception
        $output->write('hello');
    }


    /**
     * Tests if Jm_Console::writeln() will properly
     * write to PHP's output buffer
     */
    public function testWriteln() {
        $message = uniqid();
        ob_start();
        $this->console->writeln($message);
        $content = ob_get_contents();
        ob_end_clean();
        $message .= PHP_EOL;
        $this->assertEquals($message, $content);
    
        foreach($this->getTextStyles() as $style) {
            if(is_string($style)) {
                $message = $style;
            } else {
                $message = $style->__toString();
            }
            ob_start();
            $this->console->writeln($message, $style);
            $content = ob_get_contents();
            ob_end_clean();

            $message = $this->console->stdout()->colorize($message, $style) . PHP_EOL;
            $this->assertEquals($content, $message,
              'Failed to assert that the writeln produces the right output'
            );
        }
    }


    /**
     * Tests if eraseln() will output the right escape squence.
     */
    public function testEraseln() {
        ob_start();
        $this->console->stdout()->eraseln();
        $output = ob_get_contents();
        ob_end_clean();
        $expect = "\033[1K";
        $this->assertEquals($expect, $output);
    }


    /**
     * Test the static property getter / setter
     */
    public function testGetSetDefaultTextStyle() {
        $style = new Jm_Console_TextStyle(); 
        $defaultStyle = $this->console->getDefaultTextStyle();
        $this->assertEquals($style, $defaultStyle,
            'Failed to assert that the getDefaultStyle() returns a '
          . 'default text style (right after __construct())');
            
        $style->setForegroundColor(Jm_Console_TextStyle::BLUE);
        $style->setBackgroundColor(Jm_Console_TextStyle::BLUE);
        $style->setTextDecoration(Jm_Console_TextStyle::UNDERLINE);

        $this->console->setDefaultTextStyle($style);
        $defaultStyle = $this->console->getDefaultTextStyle();
        $this->assertEquals($style, $defaultStyle);
    }


    /**
     * Test if a line can be read from console
     *
     * @runInSeparateProcess
     * @stdinProvider stdinReadln
     */
    public function testReadln() {
        $line = $this->console->readln();
        $this->assertEquals('hello', $line);
        $line = $this->console->readln();
        $this->assertEquals('world', $line);
        // after closing STDIN readln() should return NULL
        fclose(STDIN);
        $line = $this->console->readln();
        $this->assertNull($line);
    }


    /**
     *  Provides the stdin for testReadln()
     *
     *  @return string
     */
    protected function stdinReadln() {
        return <<<EOF
hello
world

EOF;
    }


    /**
     * Tests if the right escape sequence will
     * be printed by $this->console->clear()
     */
    public function testClear() {
        ob_start();
        $this->console->clear();
        $output = ob_get_contents();
        ob_end_clean();
        $expect = "\033[;f\033[2J";
        $this->assertEquals($expect, $output);
    }


    /**
     * Tests if the right escape sequence
     * will be printed by $this->console->setCursorPosition()
     */
    public function testSetCursorPosition() {
        $row = 10;
        $column = 5;

        ob_start();
        $this->console->setCursorPosition($column, $row);
        $output = ob_get_contents();
        ob_end_clean();

        $expect = "\033[" . $row . ";" . $column . "f";
        $this->assertEquals($expect, $output);
    }


    /**
     * Tests if cursorback() will output the right
     * escape sequence.
     */
    public function testCursorback() {
        ob_start();
        $this->console->cursorback();
        $output = ob_get_contents();
        ob_end_clean();

        $expect = "\033[1D";
        $this->assertEquals($expect, $output);
    }


    /**
     * Tests if savecursor() will output the right
     * escape sequence
     */
    public function testSavecursor() {
        ob_start();
        $this->console->savecursor();
        $output = ob_get_contents();
        ob_end_clean();

        $expect = "\033[s";
        $this->assertEquals($expect, $output);
    }

    /**
     * Tests if a previously saved cursor position can be restored
     */
    public function testRestorecursor() {
        ob_start();
        $this->console->restorecursor();
        $output = ob_get_contents();
        ob_end_clean();

        $expect = "\033[u";
        $this->assertEquals($expect, $output);
    }


    /**
     *
     */
    public function testGetScreenDimensions() {
        $dim = $this->console->getScreenDimensions();
        // we cannot do more as we don't know the size of
        // the console ( and won't use the same methods as 
        // the tested method )
        $this->assertInternalType('array', $dim);
        $this->assertArrayHasKey('lines', $dim); 
        $this->assertArrayHasKey('columns', $dim); 
        $this->assertInternalType('integer', $dim['lines']);
        $this->assertInternalType('integer', $dim['columns']);
        $this->assertGreaterThan(0, $dim['lines']);
        $this->assertGreaterThan(0, $dim['columns']);
    }


    /**
     * Jm_Console is a singleton class. cloning is not allowed
     *
     * @expectedException Exception
     */
    public function testClone() {
        clone $this->console;
    }
}

