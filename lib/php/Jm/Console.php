<?php
/**
 * Jm_Console
 *
 * Copyright (c) 2013, Thorsten Heymann <thorsten@metashock.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name Thorsten Heymann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version >= 5.1.2
 *
 * @category  Console
 * @package   Jm_Console
 * @author    Thorsten Heymann <thorsten@metashock.de>
 * @copyright 2013 Thorsten Heymann <thorsten@metashock.de>
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.3.0
 */
/**
 * This class is the main interface to Jm_Console functionality
 *
 * @category  Console
 * @package   Jm_Console
 * @author    Thorsten Heymann <thorsten@metashock.de>
 * @copyright 2013 Thorsten Heymann <thorsten@metashock.de>
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.3.0
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
        $this->stdin  = new Jm_Console_Input();
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
            $class = get_called_class();
            self::$singletonInstance = new $class();
        }
        return self::$singletonInstance;
    }


    /**
     * Will throw an exception as cloning of a singleton is not allowed
     *
     * @return void
     *
     * @throws Exception
     */
    public function __clone() {
        throw new Exception('You tried to clone a singleton object');
    }


    /**
     * Writes $message to STDOUT
     *
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
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
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
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
     * Flushes the buffers stdin,out,err
     *
     * Note: f you want to flush only the buffer of one of them
     * use `$console->stdout()->flush()` for example.
     *
     * @return Jm_Console
     */
    public function flush() {
        $this->stdin->flush();
        $this->stdout->flush();
        $this->stderr->flush();
        return $this;
    }


    /**
     * Writes $message to STDERR
     *
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
     *
     * @return Jm_Console
     * 
     * @throws Jm_Console_Output_Exception if fwrite fails
     *
     * @codeCoverageIgnore
     */
    public function error($message, $style = NULL) {
        $this->stderr->write($message, $style);
        return $this;
    }


    /**
     * Writes a line to STDOUT. Calls write($message . PHP_EOL, $style);
     *
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_Output_Exception if the write operation fails
     *
     * @codeCoverageIgnore
     */
    public function errorln($message, $style = NULL) {
        $this->stderr->writeln($message, $style);
        return $this;
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
     *
     * @throws Jm_Console_OutputException
     */
    public function clear() {
        $this->stdout->clear();
        return $this;
    }


    /**
     * Sets the cursor position
     *
     * @param integer $column The column
     * @param integer $line   The line
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_OutputException
     */
    public function cursorPosition($column, $line) {
        $this->stdout->cursorPosition($column, $line);
        return $this;
    }


    /**
     * Sets the cursor postion to the previous column  
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_OutputException
     */
    public function cursorback() {
        $this->stdout->cursorback();
        return $this;
    }


    /**
     * Saves the current cursor position
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_OutputException
     */
    public function savecursor() {
        $this->stdout->savecursor();
        return $this;
    }


    /**
     * Restores a previously saved cursor position
     *
     * @return Jm_Console
     *
     * @throws Jm_Console_OutputException
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
    public function screenDimensions () {
        return $this->stdout->screenDimensions();
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
     * @param Jm_Console_TextStyle|string $style The style
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
     * @param Jm_Console_TextStyle|string $style The style
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
     * Disables ANSI control chars for stdout and stderr
     *
     * @return Jm_Console
     */
    public function ansiDisable() {
        $this->stdout->ansiDisable();
        $this->stderr->ansiDisable();
        return $this;
    }

    
    /**
     * Enables ANSI control chars for stdout and stderr
     *
     * @return Jm_Console
     */
    public function ansiEnable() {
        $this->stdout->ansiEnable();
        $this->stderr->ansiEnable();
        return $this;
    }


    /**
     * Returns the ansified string without printing it.
     *
     * @param string                     $text  The text to be colorized
     * @param string|Jm_ConsoleTextStyle $style The style that should be used
     *
     * @return string
     */
    public function colorize($text, $style) {
        return $this->stdout()->colorize($text, $style);
    }

}

