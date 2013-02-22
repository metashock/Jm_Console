<?php
/**
 *
 * @package Jm_Console
 */
/**
 *
 * @package Jm_Console
 */
class Jm_Console
{

    /**
     * @var Jm_Console_Input
     */
    protected $stdin;

    /**
     * @var Jm_Console_Output
     */
    protected $stdout;

    /**
     * @var Jm_Console_Output
     */
    protected $stderr;

    /**
     * @var Jm_Console_TextStyle
     */
    protected $defaultTextStyle;


    /**
     * @var Jm_Console
     */
    protected static $singletonInstance;



    /**
     * Creates the input / outputs for STDIN, STDOUT, STDERR
     *
     * @return Jm_Console
     */
    protected function __construct() {
        $this->stdin  = new Jm_Console_Input(STDIN);
        $this->stdout = new Jm_Console_Output(STDOUT);
        $this->stderr = new Jm_Console_Output(STDERR);
    }


    /**
     * Returns a reference to the console. The returned object is a singleton.
     *
     * @return Jm_Console
     */
    public static function singleton() {
        if(!self::$singletonInstance) {
            self::$singletonInstance = new Jm_Console();
        }
        return self::$singletonInstance;
    }


    /**
     * Will throw an exception as cloning of a singleton is not allowed
     *
     * @throws Exception
     */
    public function __clone() {
        throw new Exception('You tried to clone a singleton object');
    }


    /**
     * Writes $message to STDOUT
     *
     * @param string $message                              The message to print
     * @param Jm_Console_Output_TextStyle|string $style    The text style
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_Output_Exception if fwrite fails
     */
    public function write($message, $style = NULL) {
        $this->stdout->write($message, $style);
        return $this;
    }

    /**
     * Writes a line to STDOUT. Calls write($message . PHP_EOL, $style);
     *
     * @param string $message                            The message to print
     * @param Jm_Console_Output_TextStyle|string $style  The text style
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_Output_Exception if the write operation fails
     */
    public function writeln($message = '', $style = NULL) {
        $this->stdout->writeln($message, $style);
        return $this;
    }


    /**
     * Writes $message to STDERR
     *
     * @param string $message                              The message to print
     * @param Jm_Console_Output_TextStyle|string $style    The text style
     *
     * @return Jm_Console
     * 
     * @throws Jm_Console_Output_Exception if fwrite fails
     *
     * @codeCoverageIgnore
     */
    public function error($message, $style = NULL) {
        $this->stderr->write($message, $style);
    }


    /**
     * Writes a line to STDOUT. Calls write($message . PHP_EOL, $style);
     *
     * @param string $message                            The message to print
     * @param Jm_Console_Output_TextStyle|string $style  The text style
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_Output_Exception if the write operation fails
     */
    public function errorln($message, $style = NULL) {
        $this->stderr->writeln($message, $style);
    }



    /**
     * Reads a line from STDIN
     *
     * @return string
     */
    public function readln() {
        return $this->stdin->readln();
    }

    
    /**
     * Clears the screen
     *
     * @return Jm_Console
     */
    public function clear() {
        $this->stdout->clear();
        return $this;
    }


    /**
     * Sets the cursor position
     *
     * @return Jm_Console
     */
    public function setCursorPosition($column, $line) {
        $this->stdout->setCursorPosition($column, $line);
        return $this;
    }


    /**
     * Sets the cursor postion to the previous column  
     *
     * @return Jm_Console
     */
    public function cursorback() {
        $this->stdout->cursorback();
        return $this;
    }


    /**
     * Saves the current cursor position
     *
     * @return Jm_Console
     */
    public function savecursor() {
        $this->stdout->savecursor();
        return $this;
    }


    /**
     * Restores a previously saved cursor position
     *
     * @return Jm_Console
     */
    public function restorecursor() {
        $this->stdout->restorecursor();
        return $this;
    }



    /**
     * Returns the screen dimensions in lines / columns
     *
     * @return array
     *
     * @throws Jm_Console_OutputException if for somewhat reason the ANSICON
     * env var on Windows contains crap
     */
    public function getScreenDimensions () {
        return $this->stdout->getScreenDimensions();
    }


    /**
     * Returns a reference to STDIN
     *
     * @return Jm_Console_Input
     */
    public function stdin() {
        return $this->stdin;
    }


    /**
     * Returns a reference to STDOUT
     *
     * @return Jm_Console_Output
     */
    public function stdout() {
        return $this->stdout;
    }


    /**
     * Returns a reference to STDERR
     *
     * @return Jm_Console_Output
     */
    public function stderr() {
        return $this->stderr;
    }


    /**
     * Sets the default text style for stdout
     *
     * @return Jm_Console
     */
    public function setDefaultTextStyle($style) {
        $this->stdout()->setDefaultTextStyle($style);
        return $this;
    }


    /**
     * Returns the default text style for stdout
     *
     * @return Jm_Console_TextStyle
     */
    public function getDefaultTextStyle() {
        return $this->stdout()->getDefaultTextStyle();
    }


    /**
     * Sets the default text style for stderr
     *
     * @return Jm_Console
     */
    public function setDefaultErrorTextStyle($style) {
        $this->stderr()->setDefaultTextStyle($style);
        return $this;       
    }


    /**
     * Returns the default text style for stderr
     *
     * @return Jm_Console_TextStyle
     */
    public function getDefaultErrorTextStyle() {
        return $this->stdout()->getDefaultTextStyle();
    }


    /**
     * @TODO document
     */
    public function ansiDisable($fd = NULL) {
        $this->stdout->ansiDisable();
        $this->stderr->ansiDisable();
        return $this;
    }

    
    /**
     * @TODO document
     */
    public function ansiEnable($fd = NULL) {
        $this->stdout->ansiEnable();
        $this->stderr->ansiEnable();
        return $this;
    }


    /**
     * Returns the ansified string without printing it.
     *
     * @param string                     $text
     * @param string|Jm_ConsoleTextStyle $style
     *
     * @return string
     */
    public function colorize($text, $style) {
        return $this->stdout()->colorize($text, $style);
    }

    /**
     *
     */
    public function progressbar($now, $total, $w=35) {
        $this->write('[', 'fg:white,td:light');
        $n = floor($now * $w / $total);

        $this->write(str_repeat('+', $n), 'fg:green,td:light');
        if($n < $w) {
            $this->write(']', 'fg:green,td:light');
            $this->write(str_repeat('-', $w - $n -1), 'fg:red:td:light');
        }
        $this->write(']', 'fg:white,td:light');
    }
}

