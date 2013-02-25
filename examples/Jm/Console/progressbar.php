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
            progressbar($i, $total);
            printf("  %s/%s", $i, $total);
        } else {
            printf("importing: %s/%s", $i, $total);
            echo PHP_EOL;
        }
        usleep($s);
    }
    echo PHP_EOL;
}


/**
 *
 */
function progressbar($now, $total, $w=35) {
    $console = Jm_Console::singleton();
    $console->write('[', 'white,light');
    $n = floor($now * $w / $total);

    $console->write(str_repeat('+', $n), 'green,light');
    if($n < $w) {
        $console->write(']', 'green,light');
        $console->write(str_repeat('-', $w - $n -1), 'red,light');
    }

    $console->write(']', 'white,light');
}


