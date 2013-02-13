<?php
/**
 *
 * @package Jm_Console
 */
/**
 * This class represents an ANSI terminal text style with a foreground 
 * text color, a background text color and a text decoration attribute. 
 * It is intended to be used with the following Jm_AnsiTerminal methods:
 *
 * @see Jm_Console_Output::write()
 * @see Jm_Console_Output::writeln()
 * @see Jm_Console_Output::colorize()
 *
 * @package Jm_Console
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
     * @param string $foregroundColor
     * @param string $backgroundColor
     * @param string $textDecoration
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
     * @return Jm_Console_TextStyle
     */
    public static function fromString($string) {
        static $colornames;
        static $decorations;
        static $cache;

        if(!is_array($colornames)) {
            $colornames = array(
                'black' => self::BLACK,
                'red' => self::RED,
                'green' => self::GREEN,
                'yellow' => self::YELLOW,
                'blue' => self::BLUE,
                'purple' => self::PURPLE,
                'cyan' => self::CYAN,
                'white' => self::WHITE,
                'default' => self::DEFAULT_COLOR
            );
        }

        if(!is_array($decorations)) {
            $decorations = array(
                'bold' => self::BOLD,
                'light' => self::LIGHT,
                'italic' => self::ITALIC,
                'underline' => self::UNDERLINE,
                'blink_slow' => self::BLINK_SLOW,
                'blink_rapid' => self::BLINK_RAPID,
                'blink' => self::BLINK,
                'reverse' => self::REVERSE,
                'hidden' => self::HIDDEN,
                'default' => self::NO_DECORATIONS
            );
        }

        if(!is_array($cache)) {
            $cache = array();
        }


        // if the style has been created before return it
        if(isset($cache[$string])) {
            return $cache[$string];
        }


        // get a default style
        $style = static::getDefault();

        foreach(explode(',', $string) as $statement) {
            $keyval = explode(':', $statement);
            if(count($keyval) < 2) {
                // it's a simple statement
                if(in_array($statement, array(
                    'black', 'red', 'green', 'yellow',
                    'blue', 'purple', 'cyan', 'white',
                    'default'
                ))) {
                    $style->setForegroundColor($colornames[$statement]);
                    continue;
                }

                if(in_array($statement, array(
                    'bold', 'light', 'italic', 'underline',
                    'blink_slow', 'blink_rapid', 'blink',
                    'reverse', 'hidden'
                ))) {
                    $style->setTextDecoration($decorations[$statement]);
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
            switch ($key) {
                case 'fg' :
                    if(isset($colornames[$value])) {
                        $style->setForegroundColor($colornames[$value]);
                        break;
                    }
                    throw new Jm_Console_TextStyleException (sprintf(
                        'Failed to parse the style identifier \'%s\''
                      . '. Unknown foreground color value \'%s\'',
                        $string, $value
                    ));

                case 'bg' :
                    if(isset($colornames[$value])) {
                        $style->setBackgroundColor($colornames[$value]);
                        break;
                    }
                    throw new Jm_Console_TextStyleException (
                        'Failed to parse the style identifier \'' . $string . '\''
                      . '. Unknown background color value \'' . $value . '\''
                    );                       

                case 'td' :
                    if(isset($decorations[$value])) {
                        $style->setTextDecoration($decorations[$value]);
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
        $cache[$string]= $style;        
        return $style; 
    }


    /**
     *  @return Jm_Console_TextStyle
     */
    public static function getDefault() {
        return new Jm_Console_TextStyle();
    }


    /**
     *  @param $value
     *  @return Jm_Console_TextStyle
     */
    public function setForegroundColor($value) {
        $this->foregroundColor = $value;
        return $this;
    }


    /**
     *  @return string
     */
    public function getForegroundColor() {
        return $this->foregroundColor;
    }


    /**
     *  @param string value
     *  @return Jm_Console_TextStyle
     */
    public function setBackgroundColor($value) {
        $this->backgroundColor = $value;
        return $this;
    }


    /**
     *  @param $value
     *  @return string
     */
    public function getBackgroundColor() {
        return $this->backgroundColor;
    }


    /**
     *  @param $value
     *  @return Jm_Console_TextStyle
     */
    public function setTextDecoration($value) {
        $this->textDecoration = $value;
        return $this;
    }


    /**
     *  @param $value
     *  @return string
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
            Jm_Console_TextStyle::BLACK => 'black',
            Jm_Console_TextStyle::RED => 'red',
            Jm_Console_TextStyle::GREEN => 'green',
            Jm_Console_TextStyle::YELLOW => 'yellow',
            Jm_Console_TextStyle::BLUE => 'blue',
            Jm_Console_TextStyle::PURPLE => 'purple',
            Jm_Console_TextStyle::CYAN => 'cyan',
            Jm_Console_TextStyle::WHITE => 'white',
            Jm_Console_TextStyle::DEFAULT_COLOR => 'default'
        );

        static $decorations = array(
            Jm_Console_TextStyle::BOLD => 'bold',
            Jm_Console_TextStyle::LIGHT => 'light',
            Jm_Console_TextStyle::ITALIC => 'italic',
            Jm_Console_TextStyle::UNDERLINE => 'underline',
            Jm_Console_TextStyle::BLINK => 'blink',
            Jm_Console_TextStyle::REVERSE => 'reverse',
            Jm_Console_TextStyle::HIDDEN => 'hidden',
            Jm_Console_TextStyle::NO_DECORATIONS => 'no decorations'
        );

        return $colors[$this->getForegroundColor()]
          . ' on ' . $colors[$this->getBackgroundColor()]
          . ', '. $decorations[$this->getTextDecoration()];
    }

}

