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
 * This class creates Jm_Console_TextStyle instances from textutal
 * representations. The textual syntax can be used in the following 
 * public methods:
 *
 * <ul>
 *   <li>Jm_Console_Output::write()</li>
 *   <li>Jm_Console_Output::writeln()</li>
 *   <li>Jm_Console_Output::colorize()</li>
 * </ul>
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
class Jm_Console_TextStyleFactory 
{

    /**
     * A hashtable that stores the Jm_Console_TextStyle text color 
     * constants by their textual representation
     *
     * @var array
     */
    protected $colornames;

    /**
     * A hashtable that stores the Jm_Console_TextStyle text decoration
     * constants by their textual representation
     *
     * @var array
     */
    protected $decorations;

    /**
     * A hashtable that stores text styles by their textual representation. 
     * This way, parsing of text styles has to be done only once.
     *
     * @var array
     */
    protected $cache;


    /**
     * Stores the singleton instance
     *
     * @var Jm_Console_TextStyleFactory
     */
    protected static $instance;


    /**
     * Initializes $colornames. $decorations and $cache
     *
     * @return Jm_Console_TextStyleFactory
     */
    public function __construct() {
       
        $this->colornames = array(
            'black' => Jm_Console_TextStyle::BLACK,
            'red' => Jm_Console_TextStyle::RED,
            'green' => Jm_Console_TextStyle::GREEN,
            'yellow' => Jm_Console_TextStyle::YELLOW,
            'blue' => Jm_Console_TextStyle::BLUE,
            'purple' => Jm_Console_TextStyle::PURPLE,
            'cyan' => Jm_Console_TextStyle::CYAN,
            'white' => Jm_Console_TextStyle::WHITE,
            'default' => Jm_Console_TextStyle::DEFAULT_COLOR
        );

        $this->decorations = array(
            'bold' => Jm_Console_TextStyle::BOLD,
            'light' => Jm_Console_TextStyle::LIGHT,
            'italic' => Jm_Console_TextStyle::ITALIC,
            'underline' => Jm_Console_TextStyle::UNDERLINE,
            'blink_slow' => Jm_Console_TextStyle::BLINK_SLOW,
            'blink_rapid' => Jm_Console_TextStyle::BLINK_RAPID,
            'blink' => Jm_Console_TextStyle::BLINK,
            'reverse' => Jm_Console_TextStyle::REVERSE,
            'hidden' => Jm_Console_TextStyle::HIDDEN,
            'default' => Jm_Console_TextStyle::NO_DECORATIONS
        );

        $this->cache = array();
    }


    /**
     * This is the factory method. You can pass a string and will get 
     * a Jm_Console_TextStyle object or an Exception if $string is invalid.
     *
     * @param string $string The textual representation
     *
     * @throws Jm_Console_TextStyleException
     */
    public function createFromString($string) {
 
        // if the style has been created before return it
        if(isset($this->cache[$string])) {
            return $this->cache[$string];
        }

        // get a default style
        $style = new Jm_Console_TextStyle();

        foreach(explode(',', $string) as $statement) {
            $keyval = explode(':', $statement);
            if(count($keyval) < 2) {
                // it's a simple statement
                if(in_array($statement, array(
                    'black', 'red', 'green', 'yellow',
                    'blue', 'purple', 'cyan', 'white',
                    'default'
                ))) {
                    $style->setForegroundColor($this->colornames[$statement]);
                    continue;
                }

                if(in_array($statement, array(
                    'bold', 'light', 'italic', 'underline',
                    'blink_slow', 'blink_rapid', 'blink',
                    'reverse', 'hidden'
                ))) {
                    $style->setTextDecoration($this->decorations[$statement]);
                    continue;
                }

                // if its not a color or a text decoration it is an error
                throw new Jm_Console_TextStyleException (
                    'Failed to parse the style identifier \'' . $string . '\''
                  . '. Unknown statement \'' . $statement . '\''
                );
            }

            // fully qualified statemens have a key and a value 
            // separated by a ':' 
            list($key, $value) = $keyval;
            switch($key) {

                case 'fg' :
                    if(isset($this->colornames[$value])) {
                        $style->setForegroundColor($this->colornames[$value]);
                        break;
                    }
                    throw new Jm_Console_TextStyleException (sprintf(
                        'Failed to parse the style identifier \'%s\''
                      . '. Unknown foreground color value \'%s\'',
                        $string, $value
                    ));

                case 'bg' :
                    if(isset($this->colornames[$value])) {
                        $style->setBackgroundColor($this->colornames[$value]);
                        break;
                    }
                    throw new Jm_Console_TextStyleException (
                        'Failed to parse the style identifier \'' . $string . '\''
                      . '. Unknown background color value \'' . $value . '\''
                    );                       

                case 'td' :
                    if(isset($this->decorations[$value])) {
                        $style->setTextDecoration($this->decorations[$value]);
                        break;
                    }
                    throw new Jm_Console_TextStyleException (
                        'Failed to parse the style identifier \'' . $string . '\''
                      . '. Unknown text decoration value \'' . $value . '\''
                    );                       

                default :
                    throw new Jm_Console_TextStyleException (
                        'Failed to parse the style identifier \'' . $string . '\''
                      . '. Unknown text style property \'' . $key . '\''
                    );
            } 
        }

        // add the style to the cache 
        $this->cache[$string]= $style;        
        return $style; 
          
    }


    /**
     *  Returns the singleton object
     *
     *  @return Jm_Console_TextStyleFactory
     */
    public static function singleton() {
        if(!static::$instance) {
            static::$instance = new Jm_Console_TextStyleFactory();
        }
        return static::$instance;
    }


    /**
     * Will throw an excetion in any case
     *
     * @throws Exception
     */
    public function __clone() {
        throw new Exception('Cannot clone a singleton object');
    }
}

