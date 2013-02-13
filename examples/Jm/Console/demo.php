<?php

require_once 'Jm/Autoloader.php';
require_once 'Jm/Configuration.php';
Jm_Autoloader::singleton()->prependPath('lib/php');

$stdout = new Jm_AnsiTerminal();

$style1 = new Jm_AnsiTerminal_TextStyle (
    Jm_AnsiTerminal_TextStyle::BLACK,
    Jm_AnsiTerminal_TextStyle::YELLOW,
    Jm_AnsiTerminal_TextStyle::UNDERLINE
);

$style2 = Jm_AnsiTerminal_TextStyle::getDefault();


$style3 = new Jm_AnsiTerminal_TextStyle (
    Jm_AnsiTerminal_TextStyle::BLACK,
    Jm_AnsiTerminal_TextStyle::RED,
    Jm_AnsiTerminal_TextStyle::BLINK
);


$stdout->write('hello', $style1);
$stdout->write(' ', $style2);
$stdout->writeln('world', $style3);


/**
 *
 *
 */
class Output extends Jm_AnsiTerminal
{
    /**
     *  @var array
     */
    protected $styles;

    /**
     *
     */
    public function __construct () {
        $this->styles['greenPrefix'] = new Jm_AnsiTerminal_TextStyle (
            Jm_AnsiTerminal_TextStyle::WHITE,
            Jm_AnsiTerminal_TextStyle::GREEN
        );

        /* $style2 = Jm_AnsiTerminal_TextStyle::getDefault(); */
        $this->styles['redPrefix'] = new Jm_AnsiTerminal_TextStyle (
            Jm_AnsiTerminal_TextStyle::BLACK,
            Jm_AnsiTerminal_TextStyle::RED
        );
    }


    /**
     *
     */
    public function success($msg) {
        self::write('SUCCESS', $this->styles['greenPrefix']);
        self::write(' ');
        self::writeln($msg);
    }


    /**
     *
     */
    public function error($msg) {
        self::write('ERROR', $this->styles['redPrefix']);
        self::write(' ');
        self::writeln($msg);
    }

}



$output = new Output();
$output->success('Success test');
$output->error('Error test');

