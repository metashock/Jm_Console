# J@m; Console

ANSI console library for PHP

## Installation

To install Jm_Console you can use the PEAR installer or get a tarball and install the files manually.

___
### Using the PEAR installer

If you haven't discovered the metashock pear channel yet you'll have to do it. Also you should issue a channel update:

    pear channel-discover metashock.de/pear
    pear channel-update metashock

After this you can install Jm_Console. The following command will install the lastest stable version with its dependencies:

    pear install -a metashock/Jm_Console

If you want to install a specific version or a beta version you'll have to specify this version on the command line. For example:

    pear install -a metashock/Jm_Console-0.3.0

___
### Manually download and install files

Alternatively, you can just download the package from http://www.metashock.de/pear and put into a folder listed in your include_path. Please refer to the php.net documentation of the include_path directive.


## Usage

___
### Basics

Before accessing Jm_Console's functions you'll first get an object reference to it. Jm_Console is a singleton class meaning there is just a single reference available. To get the reference call:

```php

// require Jm_Autoloader
require_once 'Jm/Autoloader.php';

// get an instance of Jm_Console
$console = Jm_Console::singleton();
```
___
### Printing output 

Jm_Console provides write access to STDOUT and STDERR. Output is done using the following functions:

```php
$console->write('foo');    // writes foo to stdout
$console->writeln('foo');  // writes foo to stdout and adds a newline

$console->error('foo');    // writes foo to stderr
$console->errorln('foo');  // writes foo to stderr and adds a newline
```

___
### Terminal colors

The ANSI Terminal standard allows to define a foreground color, a background color and choose a text decoration mode. Jm_Console aims to provide an intuitive access to terminal colors when printing text.

The simplest thing is to just specifiy a foreground color:

```php
$console->writeln('hello, world!', 'green');   // writes green text to stdout
$console->errorln('an error occured!', 'red'); // writes red text to stderr
```

![green text](res/colors.png)


or just specify a text decoration:

```php
$console->writeln('Booh!', 'bold');              // writes bold text to stdout
$console->writeln('I\'m a link!', 'underline');  // writes underlined text to stdout
```

![green text](res/decorations.png)

or specify both a foreground color and a text decoration:

```php
$console->writeln('Booh!', 'blue,bold');                 // writes bold blue text to stdout
$console->writeln('I\'m a link!', 'yellow, underline');  // writes underlined yellow text to stdout
```

![green text](res/decorations2.png)

If want to set the background color you'll have to use the prefix `bg:` in front of the color. Otherwise Jm_Console couldn't make a difference between foreground color and background color:

```php
$console->writeln('Booh!', 'white,bg:blue');             // writes white text on a blue background to stdout
```
![green text](res/background-color.png)

Table: *Available Graphics modes*

<table>
  <tr>
    <th>Colors</th>
    <th>Text Decorations</th>
  </tr>
  <tr>
    <td><ul>
    <li>black</li>
    <li>red</li>
    <li>green</li>
    <li>yellow</li>
    <li>blue</li>
    <li>purple</li>
    <li>cyan</li>
    <li>white</li>
    <li>default</li>
    </ul></td>
    <td><ul>
    <li>bold</li>
    <li>light</li>
    <li>italic</li>
    <li>underline</li>
    <li>blink</li>
    <li>reverse</li>
    <li>hidden</li>
    <li>default</li>
    </ul></td>
  </tr>
</table>


___
### Cursor positioning

ANSI terminals support positioning of the cursor.

```php
$console->cursorPosition(0, 0); // positioning the cursor at upper left corner
```
It is also possible to store the cursor position and restore it later:

```php
$console->savecursor(); // saves the cursor position
// ...
$console->restorecursor(); // restores the cursor position
```

## Example
___
### Drawing a progress bar on terminal

```php
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
    $console->writeln();
}


/**
 * Renders the progressbar
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
```




## API documentation

The API docs can be found here: http://metashock.de/docs/api/Jm/Console/index.xhtml



