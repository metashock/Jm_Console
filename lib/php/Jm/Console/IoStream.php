<?php
/**
 *
 * @package Jm_Console
 */
/**
 * TODO Register SIGWINCH handler
 * TODO consider to make usage of readline if available
 * 
 * @package Jm_Console
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

