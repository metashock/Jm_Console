<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('lib/php');

$console = Jm_Console::singleton();

$console->write('hello', 'black,bg:yellow');
$console->write(' ');
$console->writeln('world', 'black,bg:red');

