<?php

require_once 'Jm/Autoloader.php';

$fcolors = array (
    Jm_Console_TextStyle::BLACK,
    Jm_Console_TextStyle::RED,
    Jm_Console_TextStyle::GREEN,
    Jm_Console_TextStyle::YELLOW,
    Jm_Console_TextStyle::BLUE,
    Jm_Console_TextStyle::PURPLE,
    Jm_Console_TextStyle::CYAN,
    Jm_Console_TextStyle::WHITE,
    Jm_Console_TextStyle::DEFAULT_COLOR
);

$bcolors =  $fcolors;

$decorations = array (
    Jm_Console_TextStyle::BOLD,
    Jm_Console_TextStyle::LIGHT,
    Jm_Console_TextStyle::ITALIC,
    Jm_Console_TextStyle::UNDERLINE,
    Jm_Console_TextStyle::BLINK,
    Jm_Console_TextStyle::REVERSE,
    Jm_Console_TextStyle::HIDDEN,
    Jm_Console_TextStyle::NO_DECORATIONS
);

$console = Jm_Console::singleton();

foreach ($fcolors as $fcolor) {
    foreach ($bcolors as $bcolor) {
        foreach ($decorations as $decoration) {
            $style = new Jm_Console_TextStyle($fcolor, $bcolor, $decoration);
            $message = $style->__toString();
            $console->writeln($message, $style);
        }
    }
}

