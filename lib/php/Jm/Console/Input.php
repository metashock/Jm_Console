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
 * @license   http://www.opensource.org/licenses/BSD-3-Clause
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
 *    defined('STDIN') ? echo "true" : echo "false";
 *
 * The code above will output: 'false'.
 *
 * As a workaround I'll declare the constants when they not exist.
 */
if(!defined('STDIN')) {
    // @codeCoverageIgnoreStart
    define('STDIN', fopen('php://stdin', 'r'));
    // @codeCoverageIgnoreEnd
}
/**
 * Represents a console input stream
 *
 * @TODO consider to make usage of readline if available
 *
 * PHP Version >=5.1.2
 * 
 * @category  Console
 * @package   Jm_Console
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2013 Thorsten Heymann
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/pear
 * @since     0.0.0
 */
class Jm_Console_Input extends Jm_Console_IoStream
{

    /**
     * A reference to the underlying file descriptor
     *
     * @var resource
     */
    protected  $fd;


    /**
     * @param resource $fd               An open file descriptior
     *
     * @throws InvalidArgumentException  if gettype($fd) !== 'integer'
     */
    public function __construct($fd = STDOUT) {
        if(!is_resource($fd)) {
            throw new InvalidArgumentException (
                sprintf('the type $fd expected to be resource. %s found',
                    gettype($fd)
                )
            );
        }

        $this->fd = $fd;
        // at the moment it's 'just a resource'
        $this->assumeIsattyCached = NULL;
    }


    /*
     * @see http://php.net/manual/en/function.fgetc.php
     * current problem: when typing very quick the method might
     *
     * not catch all characters. The problem may especially exist 
     * on slower hardware. Therefore disabled the method
     *
     * @return string
     */
/*    public function readc($echo = FALSE) {
        // Never tried it in on windows. will do
        if(DIRECTORY_SEPARATOR === '\\') {
            return '';
        }

        // readc currently needs help from /bin/stty
        // check if stty can be executed
        if(!is_executable('/bin/stty')) {
            return FALSE;
        }

        $echo = $echo ? "" : "-echo";

        # Get original settings
        $stty_settings = preg_replace("#.*; ?#s", "", self::stty("--all"));

        # Set new ones
        $this->stty("cbreak $echo");
        $c = fgetc(STDIN);

        # Return settings
        $this->stty($stty_settings);

        return $c;
    }*/


    /**
     * Reads a line from keyboard.
     *
     * @returns string|NULL
     */
    public function readln () {
        // read next line from stdin
        $input = fgets($this->fd);

        // EOF or an error?
        if ($input === FALSE) {
            // I see no way to test this reliable
            // @codeCoverageIgnoreStart
            return NULL;
            // @codeCoverageIgnoreEnd
        }
        
        // remove the new line from input
        return str_replace(PHP_EOL, "", $input);
    }
   
}

