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
/**
 * Base class for console IO streams.
 *
 * TODO Consider to register SIGWINCH handler
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
class Jm_Console_IoStream
{

    /**
     * It requires some effort to answer the question if
     * the file decscriptor belongs to a device that understands
     * escape sequences or not. Therefore the value will be cached.
     *
     * @var boolean
     */
    protected $assumeIsattyCached;


    /**
     * @var boolean
     */
    protected $enforceIsatty;



    /**
     * @param resource $fd               An open file descriptior
     *
     * @return Jm_Console_IoStream
     *
     * @throws InvalidArgumentException  if gettype($fd) !== 'integer'
     */
    public function __construct($fd) {
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
    
        // set proper defaults
        $this->enforceIsatty = FALSE;
    }


    /**
     * The usage of terminal escape sequences is only meaningful if 
     * output goes to a terminal device. Every method of Jm_Console_Output 
     * that uses escape sequences checks this helper method before deciding 
     * whether to use escapes or not.
     *
     * My first attempt was that to just wrap posix_assumeIsatty(). But I 
     * realized that the posix extension is only rarely deployed in most 
     * 3rd party hosted environments.
     * 
     *  @param resource $fd=STDOUT
     *  @return boolean true if STDOUT goes to a terminal false if not.
     */
    public function assumeIsatty() {
        if(!is_null($this->assumeIsattyCached)) {
            return $this->assumeIsattyCached;
        }

        // this behaviour can be enforced (becaus of test suite)
        if($this->enforceIsatty()) {
            return $this->assumeIsattyCached = TRUE;
        };
                
        // on Windows there is ansicon. A cmd.exe replacemnt
        // that supports ANSI escape sequences
        if (DIRECTORY_SEPARATOR == '\\') {
            if (getenv('ANSICON') !== FALSE) {
                // if the ENVVAR isset return TRUE
                return $this->assumeIsattyCached = TRUE;
            } else {
                // otherwise FALSE
                return $this->assumeIsattyCached = FALSE;           
            }
        }

        // on LINUX the prefered way is to use posix_isatty().
        if(function_exists('posix_isatty')) {
            $this->assumeIsattyCached = @posix_isatty($this->fd);
            return $this->assumeIsattyCached;
        }

        // if posix_isatty isn't available we use fstat() to decide
        // if $this->fd belongs to a character device
        
        // the results of fstat() will get cached. clear the cache
        clearstatcache();
        $info = fstat($this->fd);
        $type = decoct($info['mode'] & 0170000); // File encoding bit
        // this are the inode types:
        //
        // 0140000=>'socket',
        // 0120000=>'link',
        // 0100000=>'file',
        // 0060000=>'block',
        // 0040000=>'dir',
        // 0020000=>'char',
        // 0010000=>'pfifo'
        if(octdec($type) === 0020000) {
            return $this->assumeIsattyCached = TRUE;
        }

        // the default fallback is FALSE
        return $this->assumeIsattyCached = FALSE;
    }


    /**
     * Returns the file descriptor (resource) assigned to the stream.
     *
     * @return resource
     */
    public function fd () {
        return $this->fd;
    }


    /**
     * I'm currently needing this method for automated tests
     * where stdout doesn't go to a terminal
     *
     * @param NULL|boolean $value
     * @return boolean
     */
    public function enforceIsatty($value = NULL) {
        if(!is_null($value)) {
            if(!is_bool($value)) {
                throw new InvalidArgumentException(
                    'value expected to be bool or NULL. ' .
                    gettype($value) . ' found.'
                );
            }

            $this->enforceIsatty = $value;
        }
        return $this->enforceIsatty;
    }



}

