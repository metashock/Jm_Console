<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('lib/php');

$terminal = new Jm_AnsiTerminal(STDIN);

echo 'Password: ';
$pwd = '';
while(TRUE) {
    $c =  $terminal->readc();
    if($c === PHP_EOL) {
        echo $c;
        break;
    } else {
        $pwd .= $c;
        echo '*';
    }
}

echo 'The secret password is: ', $pwd, PHP_EOL;

