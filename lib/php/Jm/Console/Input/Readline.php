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
 * Represents an input stream that makes usage of the leadline library.
 *
 * @TODO consider to make usage of readline if available
 *
 * PHP Version >=5.1.2
 * 
 * @category  Console
 * @package   Jm_Console
 * @author    Thorsten Heymann <info@metashock.net>
 * @copyright 2013 Thorsten Heymann
 * @license   BSD-3 http://www.opensource.org/licenses/bsd-license.php BSD
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/pear
 * @since     0.0.0
 */
class Jm_Console_Input_Readline extends Jm_Console_IoStream
{

    /**
     * Reads a line from keyboard.
     *
     * @param integer $timeout  Secs to wait for input
     * @param integer $utimeout uSecs to wait for input
     *
     * @return string|NULL
     */
    public function readln ($timeout = 0, $utimeout = 0) {
        return readline();
    }
}

