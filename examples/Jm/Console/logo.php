<?php

/*
 * Console Animator Demo =)
 */
try {
    // set include paths
    ini_set('include_path', join(PATH_SEPARATOR, array (
        'lib/php',
        ini_get('include_path')
    )));
    // require framework
    require_once 'Jm.php';
    class cs extends Jm_Console {}

    Jm_Log::addObserver('syslog');

    $sprites = array();
    // create a sprite for every letter
    $sprites []= new Jm_Console_Sprite(cs::colorize(' J ', cs::GREEN, cs::BOLD));
    $sprites []= new Jm_Console_Sprite(cs::colorize(' A ', cs::RED, cs::BOLD));
    $sprites []= new Jm_Console_Sprite(cs::colorize(' M ', cs::BLUE, cs::BOLD));
    $sprites []= new Jm_Console_Sprite('   ');
    $sprites []= new Jm_Console_Sprite(' C ');
    $sprites []= new Jm_Console_Sprite(' O ');
    $sprites []= new Jm_Console_Sprite(' N ');
    $sprites []= new Jm_Console_Sprite(' S ');
    $sprites []= new Jm_Console_Sprite(' O ');
    $sprites []= new Jm_Console_Sprite(' L ');
    $sprites []= new Jm_Console_Sprite(' E ');

    for ($i = 0; $i < 100; $i++) {
//        $sprites []= new Jm_Console_Sprite(' ' . round(mt_rand(0, 1)) . ' ');       
    }
    
    // add the sprites to the stage
    $stage = Jm_Console_Stage::instance();
    foreach ($sprites as $sprite) {
        $stage->append($sprite);
    }

    // register the onEnterFrame listener
    $stage->addListener (
        Jm_Console_StageEvent::ENTER_FRAME,
        'stage_onEnterFrame'
    );


    // initialize the animation clock
    $t = 0;

    // start animation
    $stage->setFps(25);
    $stage->play();

} catch ( Exception $e ) {
    le($e);
}

/**
 *
 *
 */
function stage_onEnterFrame (Jm_Console_StageEvent $event) {
    global $sprites;
    global $t;

    $stage = Jm_Console_Stage::instance ();
    $w = $stage->getWidth();
    $h = $stage->getHeight();

    foreach ($sprites as $index => $sprite) {
        $posx = intval(ceil($w / 2)) + $index * 3;
        $posy = intval(ceil(($h / 2) * (sin(($t/100 + 3 * $index)* 3.14 / 180) * .35  + 1)));
        $sprite->setPositionX ( $posx);
        $sprite->setPositionY ( $posy);
    }

    $t+= 5;;
}

