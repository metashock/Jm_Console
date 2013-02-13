<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('lib/php');

$t = new Jm_AnsiTerminal();
$t->writeln('hello world', 'fg:white,bg:cyan,td:bold');
