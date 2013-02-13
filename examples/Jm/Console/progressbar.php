<?php

require_once 'Jm/Autoloader.php';
Jm_Autoloader::singleton()->prependPath('lib/php');

$console = Jm_Console::singleton();

for($a = 0; $a < 3; $a++) {
    $s = rand(1, 50000);
    $console->savecursor();
    $total = rand(1,100);
    for($i = 0; $i <= $total; $i++) {
        if($console->stdout()->assumeIsatty()) {
            $console->stdout()->eraseln();
            $console->restorecursor();
            $console->write('importing: ');
            $console->progressbar($i, $total, 90);
            printf("  %s/%s", $i, $total);
        } else {
            printf("importing: %s/%s", $i, $total);
            echo PHP_EOL;
        }
        usleep($s);
    }
    echo PHP_EOL;
}


