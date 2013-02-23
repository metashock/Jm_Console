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
 * This class represents an ANSI terminal text style with a foreground 
 * text color, a background text color and a text decoration attribute. 
 * It is intended to be used with the following Jm_AnsiTerminal methods:
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
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.3.0
 */
class Jm_Console_TextStyle
{
    /*
     * ANSI SGR (Select Graphic Rendition) color constants
     */

    /**
     * @const string
     */
    const BLACK          = '0';

    /**
     * @const string
     */
    const RED            = '1';

    /**
     * @const string
     */
    const GREEN          = '2';

    /**
     * @const string
     */
    const YELLOW         = '3';

    /**
     * @const string
     */
    const BLUE           = '4';

    /**
     * @const string
     */
    const PURPLE         = '5';

    /**
     * @const string
     */
    const CYAN           = '6';

    /**
     * @const string
     */
    const WHITE          = '7';

    /*
     * The default color may behave unexpected in some cases.
     *
     * When you use a terminal with enabled alpha transparancy,
     * you must use DEFAULT_COLOR as background to enable the 
     * transparent background
     *
     * @const
     */
    const DEFAULT_COLOR  = '9';


    /*
     * SGR (Select Graphic Rendition) text decoration constants
     */

    /**
     * @const string
     */
    const BOLD           = '1';

    /**
     * @const string
     */
    const LIGHT          = '2';

    /**
     * Never seen supported
     *
     * @const string
     */
    const ITALIC         = '3';

    /**
     * @const string
     */
    const UNDERLINE      = '4';

    /**
     * Never seen supported
     *
     * @const string
     */
    const BLINK_SLOW     = '5';

    /**
     * Never seen supported
     *
     * @const string
     */
    const BLINK_RAPID    = '6';

    /**
     * Supported on : <ul>
     *  <li>Linux console</li>
     * </ul>
     *
     * @const string
     */
    const BLINK          = '6';

    /**
     * Supported on : <ul>
     *  <li>Linux console</li>
     *  <li>xterm</li>
     * </ul>
     *
     * @const string
     */
    const REVERSE        = '7';

    /**
     * Supported on : <ul>
     *  <li>Linux console</li>
     *  <li>xterm</li>
     * </ul>
     *
     * @const string
     */
    const HIDDEN         = '8';

    /**
     * Supported on : <ul>
     *  <li>xterm</li>
     * </ul>
     *
     * @const string
     */
    const STROKED        = '9';

    /**
     * Disabled decorations. Pseudo code that prevents 
     *
     * @const string
     */
    const NO_DECORATIONS = '';

    /**
     * Resets all SGR values to default. This should work on 
     * all terminals. If not, please file a bug report to me.
     *
     * @const
     */
    const _RESET         = '0';



    /**
     * Color identifier for the foreground color.
     * Values are expected between '0'..'9' (not between '40'..'49')
     *
     * @var string
     */
    protected $foregroundColor;


    /**
     * Color identifier for the background color.
     * Values are expected between '0'..'9' (not between '40'..'49')
     *
     * @var string
     */
    protected $backgroundColor;

    
    /**
     * SGR value for the text decoration or empty string
     * if no text decoration is 
     *
     * @var string
     */
    protected $textDecoration;


    /**
     * Constructor
     *
     * @param string $foregroundColor The foreground color
     * @param string $backgroundColor The background color
     * @param string $textDecoration  The text decoration
     *
     * @return Jm_Console_TextStyle
     */
    public function __construct(
        $foregroundColor = Jm_Console_TextStyle::DEFAULT_COLOR,
        $backgroundColor = Jm_Console_TextStyle::DEFAULT_COLOR,
        $textDecoration = Jm_Console_TextStyle::NO_DECORATIONS
    ) {
        $this
           ->setForegroundColor($foregroundColor)
           ->setBackgroundColor($backgroundColor)
           ->setTextDecoration($textDecoration); 
    }


    /**
     * Creates a TextStyle from its textual representation. This is useful
     * to keep the syntax short and readable when working with text styles.
     *
     * @param string $string The textual representation
     *
     * @return Jm_Console_TextStyle
     *
     * @throws Jm_Console_TextStyleException if $string cannot be parsed
     *
     * @TODO examples
     */
    public static function fromString($string) {
        return Jm_Console_TextStyleFactory::singleton()
          ->createFromString($string);
    }


    /**
     * Returns a default text style 
     *
     * @deprecated
     * @return Jm_Console_TextStyle
     */
    public static function getDefault() {
        return new Jm_Console_TextStyle();
    }


    /**
     * Sets the foreground color
     *
     * @param string $value One of the color constants 
     *
     * @return Jm_Console_TextStyle
     */
    public function setForegroundColor($value) {
        $this->foregroundColor = $value;
        return $this;
    }


    /**
     * Returns the foreground color
     *
     * @return string
     */
    public function getForegroundColor() {
        return $this->foregroundColor;
    }


    /**
     * Sets the background color
     *
     * @param string $value One of the color constants
     *
     * @return Jm_Console_TextStyle
     */
    public function setBackgroundColor($value) {
        $this->backgroundColor = $value;
        return $this;
    }


    /**
     * Returns the background color
     *
     * @return string
     */
    public function getBackgroundColor() {
        return $this->backgroundColor;
    }


    /**
     * Sets the text decoration
     *
     * @param string $value One of the text decoration constants
     *
     * @return Jm_Console_TextStyle
     */
    public function setTextDecoration($value) {
        $this->textDecoration = $value;
        return $this;
    }


    /**
     * Gets the text decoration
     *
     * @return string
     */
    public function getTextDecoration() {
        return $this->textDecoration;
    }


    /**
     * Returns a string like: 'green on black, underline'
     *
     * @return string
     */
    public function __toString() {
        static $colors = array(
            Jm_Console_TextStyle::BLACK         => 'black',
            Jm_Console_TextStyle::RED           => 'red',
            Jm_Console_TextStyle::GREEN         => 'green',
            Jm_Console_TextStyle::YELLOW        => 'yellow',
            Jm_Console_TextStyle::BLUE          => 'blue',
            Jm_Console_TextStyle::PURPLE        => 'purple',
            Jm_Console_TextStyle::CYAN          => 'cyan',
            Jm_Console_TextStyle::WHITE         => 'white',
            Jm_Console_TextStyle::DEFAULT_COLOR => 'default'
        );

        static $decorations = array(
            Jm_Console_TextStyle::BOLD           => 'bold',
            Jm_Console_TextStyle::LIGHT          => 'light',
            Jm_Console_TextStyle::ITALIC         => 'italic',
            Jm_Console_TextStyle::UNDERLINE      => 'underline',
            Jm_Console_TextStyle::BLINK          => 'blink',
            Jm_Console_TextStyle::REVERSE        => 'reverse',
            Jm_Console_TextStyle::HIDDEN         => 'hidden',
            Jm_Console_TextStyle::NO_DECORATIONS => 'no decorations'
        );

        return $colors[$this->getForegroundColor()]
          . ' on ' . $colors[$this->getBackgroundColor()]
          . ', '. $decorations[$this->getTextDecoration()];
    }

}

