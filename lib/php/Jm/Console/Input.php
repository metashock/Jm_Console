<?php
/**
 *
 * @package Jm_Console
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
 *
 * @package Jm_Console
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


    /**
     * @see http://php.net/manual/en/function.fgetc.php
     */
    protected function stty($options) {
        exec($cmd = "/bin/stty $options", $output, $el);
        $el AND die("exec($cmd) failed");
        return implode(" ", $output);
    }
   
}

