<?php

require __DIR__ . '/../vendor/autoload.php';

use MoSamirzz\Coroutine\Coroutine as Co;

$before = microtime(true);
Co::create(function() {
    Co::delay(2000, function() { echo "World\n"; });

    Co::delay(1000, function() { echo "Hello\n"; });

    Co::go(function() {
        $gen = (function() {
            yield 1;
            yield 2;
            yield 3;
        })();

        foreach ($gen as $g) {
            echo $g . "\t";
        }

        echo "\n";
    });

    Co::go(function() { echo time() . "\n"; });
    Co::go(function() { echo time() . "\n"; });
    Co::go(function() { echo time() . "\n"; });
    Co::go(function() { echo time() . "\n"; });
});

echo PHP_EOL . "Time: " . (microtime(true) - $before) . PHP_EOL;