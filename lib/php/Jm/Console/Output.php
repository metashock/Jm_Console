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
/*
 * I realized that when piping to stdin of a php cli script the 
 * global constants STDIN, STDOUT and STDERR won't be defined. I'm
 * not sure yet if this is a bug or if I'm missing something.
 *
 * Imagine the following example:
 *
 * $ php <<EOF
 * <?php
 *    defined('STDOUT') ? echo "true" : echo "false";
 *
 * The code above will output: 'false'.
 *
 * As a workaround I'll declare the constants when they not exist.
 */
if(!defined('STDOUT')) {
    // @codeCoverageIgnoreStart
    define('STDOUT', fopen('php://stdout', 'w+'));
    // @codeCoverageIgnoreEnd
}
if(!defined('STDERR')) {
    // @codeCoverageIgnoreStart
    define('STDERR', fopen('php://stderr', 'w+'));
    // @codeCoverageIgnoreEnd
}
/**
 * Represents a console output stream.
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
class Jm_Console_Output extends Jm_Console_IoStream
{

    /**
     * Using this flag ANSI escaping can explecitely disabled
     *
     * @var boolean
     */
    protected $ansiEnabled;


    /**
     * The default text style to be used 
     *
     * @var Jm_Console_TextStyle
     */
    protected $defaultTextStyle;


    /**
     * Constructor
     *
     * @param resource $fd An open file descriptior
     *
     * @throws InvalidArgumentException  if gettype($fd) !== 'integer'
     */
    public function __construct($fd = STDOUT) {
        parent::__construct($fd);
        $this->ansiEnabled = TRUE;
    }

    
    /**
     * Writes $message to $fd.
     *
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
     *
     * @return Jm_Console_Output
     *
     * @throws Jm_Console_Output_Exception if fwrite fails
     */
    public function write($message = '', $style = NULL) {
        if(is_null($style)) {
            $style = Jm_Console_TextStyle::getDefault();
        }
        if(is_string($style)) {
            $style = Jm_Console_TextStyle::fromString($style);
        }
        if ($this->ansiEnabled === TRUE && $this->assumeIsatty()) {
            $message = $this->colorize($message, $style);
        }

        // when fd points to STDOUT we use echo instead of fwrite()
        // this will keep the ob_* functions working
        if($this->fd === STDOUT) {
            echo $message;
        } else {
            $ret = @fwrite($this->fd, $message);
            // check for errors
            if($ret === FALSE) {
                throw new Jm_Console_OutputException(
                    sprintf('Failed to write at all', $message)
                );
            } else if ($ret < strlen($message)) {
                $remaining = strlen($message) - $ret;
                throw new Jm_Console_OutputException(
                    sprintf('Failed to write %s chars', $remaining)
                );
            }
        }
    }


    /**
     * Writes a line to stdout. Calls write($message . PHP_EOL, $style);
     *
     * @param string                             $message The message to print
     * @param Jm_Console_Output_TextStyle|string $style   The text style
     *
     * @return Jm_Console_Output
     *
     * @throws Jm_Console_Output_Exception if the write operation fails
     */
    public function writeln($message = '', $style = NULL) {
        $this->write($message, $style);
        $this->write(PHP_EOL);
        return $this;
    }


    /**
     * Clears the screen. This is like typing 'clear' on
     * Linux or 'cls' on Windows terminals.
     *
     * @return Jm_Console_Output
     *
     * @throws Jm_Console_Output_Exception
     */
    public function clear() {
        if(!$this->assumeIsatty()) {
            return $this;
        }
        $this->write("\033[;f\033[2J");
        return $this;
    }



    /**
     * Erases the entire current line
     *
     * @return Jm_Console_Output
     *
     * @throws Jm_Console_Output_Exception
     */
    public function eraseln() {
        if(!$this->assumeIsatty()) {
            return $this;
        }
        $pattern = "\033[1K";
        $this->write($pattern);
        return $this;
    }


    /**
     * Wraps $message into ANSI escape sequences.
     *
     * @param string                      $message The string to wrap
     * @param Jm_Console_Output_TextStyle $style   The text style
     *
     * @return string
     * @todo move to Jm_Console_TextStyle
     */
    public function colorize (
        $message,
        $style
    ) {

        if(is_string($style)) {
            $style = Jm_Console_TextStyle::fromString($style);
        } else if (!is_a($style, 'Jm_Console_TextStyle')) {
            throw new Exception(sprintf(
                '$style expected to be a Jm_Console_TextStyle or a string. '
              . '%s found', gettype($style)
            ));
        }

        // if all style attriubtes set to reset disable styling
        if ($style->getForegroundColor()
            === Jm_Console_TextStyle::DEFAULT_COLOR
        && $style->getBackgroundColor()
            === Jm_Console_TextStyle::DEFAULT_COLOR
        && $style->getTextDecoration()
            === Jm_Console_TextStyle::NO_DECORATIONS) 
        {
            return $message;
        }

        // wrap the message into an ANSI escape sequence 
        // in order to colorize the string

        $codes = array();
        if ( $style->getForegroundColor()
            !== Jm_Console_TextStyle::DEFAULT_COLOR ) {
            $codes []= '3' . $style->getForegroundColor();
        }
        if ( $style->getBackgroundColor()
            !== Jm_Console_TextStyle::DEFAULT_COLOR ) {
            $codes []= '4' . $style->getBackgroundColor();
        }
        if ( $style->getTextDecoration()
            !== Jm_Console_TextStyle::NO_DECORATIONS ) {
            $codes []= $style->getTextDecoration();
        }

        $ansi  = "\033[" . implode(';', $codes) . 'm';
        $ansi .= $message;
        $ansi .= "\033[0m";
        return $ansi;
    }


    /**
     * Sets the cursor position.
     *
     * @return Jm_Console_Output
     *
     * @throws Jm_Console_Output_Exception
     */
    public function cursorPosition($column, $line) {
        if(!$this->assumeIsatty()) {
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }
        $this->write("\033[" . $line . ";" . $column . "f");
        return $this;
    }

    /*
     * Returns the current cursor position. 
     * Note: This method requires stty support.
     *
     * I couldn't get it stable. Therefore I have disabled the method.
     * Maybe it will be supported in upcoming version
     *
     * @return array
     */
    /* public function getCursorPosition() {
        if(!self::assumeIsatty()) {
           // I see no way to test this reliable
           // @codeCoverageIgnoreStart
           return;
           // @codeCoverageIgnoreEnd
        }

        // Get original settings
        $stty_settings = preg_replace("#.*; ?#s", "", self::stty("--all"));
        self::stty('-echo');

        // query the terminal
        self::write("\033[6n");
        $buffer = '';
        while(TRUE) {
            $char = $this->readc(TRUE);
            $buffer .= $char;
            if ($char === 'R') {
                break;
            }            
        }

        // restore old tty settings
        $this->stty($stty_settings);
       
        // the return string is formatted as \33[row,columnR
        $buffer = substr($buffer, 2, strlen($buffer) - 3);
        list($row, $column) = explode(';', $buffer);

        return array (
            'row' => intval($row),
            'column' => intval($column)
        );
    }*/


    /**
     * Sets the cursor postion to the previous
     * column on the same row
     *
     * @return Jm_Console_Output
     */
    public function cursorback() {
         if(!$this->assumeIsatty()) {
            // I see no way to test this reliable
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }
        $this->write("\033[1D");
        return $this;
    }


    /**
     * Saves the cursor position to a stack.
     * The position can be restored using restorecursor()
     *
     * @return Jm_Console_Output
     */
    public function savecursor() {
         if(!$this->assumeIsatty()) {
            return $this;
        }
        $this->write("\033[s");
        return $this;
    }


    /**
     * Restores a cursor position that has been previously
     * saved using savecursor()
     *  
     * @return Jm_Console_Output
     */
    public function restorecursor() {
         if(!$this->assumeIsatty()) {
            // I see no way to test this reliable
            // @codeCoverageIgnoreStart
            return $this;
            // @codeCoverageIgnoreEnd
        }
        $this->write("\033[u");
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
        if (DIRECTORY_SEPARATOR !== '\\') {
            // on nix systems we use tput
            return  array (
                'lines' => intval(`tput lines`),
                'columns' => intval(`tput cols`)
            );
        }

        // In windows we have only a chance on ansicon
        $ansicon = getenv('ANSICON');
        if($ansicon === FALSE) {
            return array (
                'lines' => 0,
                'columns' => 0
            );
        }


        // the ANSICON env is a string like this: 80x300 (80x25)
        // where 80x300 is the screen buffer size and (80x25) 
        // the displayed window size.

        $info = explode(' ', $ansicon);
        if(!count($info) === 2) {
            throw new Jm_Console_Output_Exception(
                sprintf('Failed to parse the ANSICON env var. Got: %s',
                    $ansicon
                )
            );
        }

        // remove the ')' at the end
        $displaySize = str_replace (array('(', ')'), '', $info[1]);
        $linesCols = explode('x', $displaySize);
        if(count($linesCols !== 2)
            || !is_numeric($linesCols[0])
            || !is_numeric($linesCols[1])
        ) {
            throw new Jm_Console_Output_Exception(
                sprintf('Failed to parse the ANSICON env var. Got: %s',
                    $ansicon
                )
            );
        }

        return array (
            'lines' => intval($linesCols[1]),
            'columns' => intval($linesCols[2])
        );
    }


    /**
     * Enables the usage of ANSI sequences. Note that this
     * will be overridden unless self::assumeIsatty returns true.
     *
     * @return Jm_Console_Output
     */
    public function ansiEnable() {
        $this->ansiEnabled = TRUE;
        return $this;
    }


    /**
     * Disables the usage of ANSI escape sequences. If disabled ANSI escapes
     * won't be used even if assumeIsatty returns TRUE.
     *
     * @return Jm_Console_Output
     */
    public function ansiDisable() {
        $this->ansiEnabled = FALSE;
        return $this;
    }


    /**
     * Sets the default text style.
     *
     * @param string|Jm_Console_TextStyle the value
     *
     * @return Jm_Console_Output
     *
     * @throws InvalidArgumentException if $style is not a string or a 
     *                                  Jm_Console_TextStyle
     */
    public function setDefaultTextStyle($style) {
        if(is_string($style)) {
            $style = Jm_Console_TextStyleFactory::singleton()
              ->createFromString($style);
        }

        if(!is_a($style, 'Jm_Console_TextStyle')) {
            throw new Exception(sprintf(
                '$style eas expected to be a string. %s found',
                gettype($style)
            ));
        }

        $this->defaultTextStyle = $style;

        return $this;
    }


    /**
     * Returns the default text style. If it wasn't set before a default
     * text style will be created.
     *
     * @return Jm_Console_TextStyle
     */
    public function getDefaultTextStyle() {
        if(!$this->defaultTextStyle) {
            $this->defaultTextStyle = new Jm_Console_TextStyle();
        }
        return $this->defaultTextStyle;
    }


    /*
     * Prints out which features are supported by your terminal
     * and which not. Not implemented yet
     *
     * @return void
     *
    public static function printCapabilities() {
        static::writeln(sprintf('assumeIsatty : %s', self::isatty(STDOUT)));
    }
    */
}

