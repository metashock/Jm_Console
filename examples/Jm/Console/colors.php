<?php

require_once 'Jm/Autoloader.php';

$fcolors = array (
    Jm_AnsiTerminal_TextStyle::BLACK,
    Jm_AnsiTerminal_TextStyle::RED,
    Jm_AnsiTerminal_TextStyle::GREEN,
    Jm_AnsiTerminal_TextStyle::YELLOW,
    Jm_AnsiTerminal_TextStyle::BLUE,
    Jm_AnsiTerminal_TextStyle::PURPLE,
    Jm_AnsiTerminal_TextStyle::CYAN,
    Jm_AnsiTerminal_TextStyle::WHITE,
    Jm_AnsiTerminal_TextStyle::DEFAULT_COLOR
);

$bcolors =  $fcolors;

$decorations = array (
    Jm_AnsiTerminal_TextStyle::BOLD,
    Jm_AnsiTerminal_TextStyle::LIGHT,
    Jm_AnsiTerminal_TextStyle::ITALIC,
    Jm_AnsiTerminal_TextStyle::UNDERLINE,
    Jm_AnsiTerminal_TextStyle::BLINK,
    Jm_AnsiTerminal_TextStyle::REVERSE,
    Jm_AnsiTerminal_TextStyle::HIDDEN,
    Jm_AnsiTerminal_TextStyle::NO_DECORATIONS
);

$stdout = new Jm_AnsiTerminal();

foreach ($fcolors as $fcolor) {
    foreach ($bcolors as $bcolor) {
        foreach ($decorations as $decoration) {
            $style = new Jm_AnsiTerminal_TextStyle($fcolor, $bcolor, $decoration);
            $message = $style->__toString();
            $stdout->writeln($message, $style);
        }
    }
}

