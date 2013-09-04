<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('../../../lib/php');

$console = Jm_Console::singleton();

$console->write('Please input while 5 sec ...' . PHP_EOL);
if(!($input = $console->readln(5))) {
    $console->writeln('You failed!', 'red');
} else {
    $console->writeln('Input: ' . $input, 'green');
}

